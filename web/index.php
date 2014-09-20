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
    return <<<HTML
<!DOCTYPE html>
<html>
<head profile="http://microformats.org/profile/rel-license">
    <meta charset="utf-8"/>
    <title>Seven Segment Optical Character Recognition - Web Service</title>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/foundation/5.2.2/css/foundation.min.css"/>
    <link rel="stylesheet" href="http://pother.ca/CssBase/css/created-by-potherca.css"/>
    <style>
        .file {
            margin: 1em 40%;
        }
        .file label {
            color: rgb(255,255,255);
            height: 100%;
            width: 100%;
        }

        .file input {
          position: absolute;
          left: 0;
          top: 0;
          bottom: 0;
          right: 0;
          opacity: 0.01;
          cursor: pointer;
        }
    </style>
</head>
<body class="text-center">
    <header>
        <h1>
            <a href="./">SSOCR-WS</a>
            <small>Seven Segment Optical Character Recognition - Web Service</small>
        </h1>
    </header>
    
    
    <form action="" method="post" enctype="multipart/form-data">
       <p class="panel callout radius">
            The uploaded image will be parsed and the found number will be returned.
        </p>
         <fieldset>
            <legend>File Upload</legend>
            <p class="file button radius">
                <input type="file" name="file" id="file" accept="image/*" placeholder="Select an image to upload"/>
                <label for="file" id="file-label">Select an image to upload</label>
            </p>

            <button type="submit" class="radius">Upload!</button>
        </fieldset>
   </form>  
    
    <p  class="panel radius">Fetching the result could take some time. Please be patient.</p>
    <hr/>

    <footer class="text-right">
        <span class="version">0.0.0</span>
        &ndash;
        The Source Code for this project is <a href="https://github.com/potherca/ssocr-ws">available on github.com</a> under a <a href="https://www.gnu.org/licenses/gpl.html" rel="license">GPLv3 License</a>
        &ndash;
        <a href="http://pother.ca/" class="created-by">Created by <span class="potherca">Potherca</span></a>
    </footer>
    <script>
        document.getElementById('file').onchange = function () {
            document.getElementById('file-label').innerHTML = this.value.split(/[\\\\/]/).pop();
        };
    </script>
</body>
</html>
HTML;
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

