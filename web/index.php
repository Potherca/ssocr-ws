<?php

namespace Potherca\ImageParser;

use Silex\Application;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\HttpFoundation\Request;

require '../vendor/autoload.php';

$debug = true;

$app = new Application(array('debug'=>$debug));

$app->get('/', function (Request $p_oRequest) use ($app){
    return file_get_contents('../templates/index.html');
});

$app->post('/', function (Request $p_oRequest) use ($app){
    $sUploadPath = __DIR__ . '/uploads/';

    $oUploadedFile = $p_oRequest->files->get('file');
    $sExtension = $oUploadedFile->guessExtension();
    $sName = date('Ymd-His-u');
    $sFileName = $sName . '.' . $sExtension;

    $oFile = $oUploadedFile->move($sUploadPath, $sFileName);
    $sMessage = '<a href="' . basename($sUploadPath) . '/' . $sFileName . '"><img src="' . basename($sUploadPath) . '/' . $sFileName . '"/></a>';
    $sResult = trim(parseImage($oFile));
    
    if (is_numeric($sResult)) {
        $aResponse = array(
            'status' => 'ok',
            'data' => array(
                'score' => (int) $sResult,
            ),
        );
    } else {
        $aResponse = array(
            'status' => 'error',
            'data' => array(
                'message' => $sResult,
            ),
        );
    }
    
    return $app->json($aResponse);
});

$app->run();

/*EOF*/

function parseImage ($p_sImagePath)
{
    $sImagePath = $p_sImagePath;

    $sDebugImageFolder = $sImageFolder = __DIR__ . '/uploads/';

    $aParseCommands = array('remove_isolated', 'invert');
    $sThreshold = '95';
    $sDebugImageName = basename($sImagePath) . '-processed.png';
    $sDebugImagePath = $sDebugImageFolder . '/' . $sDebugImageName;
    /*
    var_dump(file_exists($strImagePath));
    var_dump(is_readable($strImagePath));
    */

    $oBuilder = new ProcessBuilder();
    $oBuilder->setPrefix('ssocr');

    $aArguments = array(
        '--debug-image=' . $sDebugImagePath,
        '--threshold=' . $sThreshold,
    );
    $aArguments = array_merge($aArguments, $aParseCommands);
    array_push($aArguments, $sImagePath);
            
    $oProcess = $oBuilder->setArguments($aArguments)->getProcess();
    $sCommand = $oProcess->getCommandLine();

    $iResult = $oProcess->run();
    if ($oProcess->isSuccessful() === true) {
        $sOutput = $oProcess->getOutput();
    } else {
        //@TODO: Errorhandling
            $sOutput = $oProcess->getErrorOutput();
    }
/*    
    $iResult = $oProcess->run(function ($p_sStreamType, $strOutputBuffer) {
        if ($p_sStreamType === Process::ERR) {
            $sOutput = 'ERR > ' . $oProcess->getErrorOutput();
        } else {
            $sOutput = 'OUT > ' . $strOutputBuffer;
        }
        return $sOutput;
    });
    var_dump($iResult);
    $sImage = basename($sDebugImageFolder) . '/' . $sDebugImageName;
    echo '<a href="' . $sImage . '">' . '<img src="' . $sImage . '" />' . '</a>';
*/
    return $sOutput;
}

