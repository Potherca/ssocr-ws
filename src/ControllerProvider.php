<?php

namespace Potherca\SevenSegmentOcrWebService;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provider of Controllers to populate the Application with
 */
class ControllerProvider implements ControllerProviderInterface
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /** @var Application */
    protected $m_oApp;
    /** @var  ProcessBuilder */
    protected $m_oProcessBuilder;
    /** @var  ImageParser */
    protected $m_oImageParser;
    //////////////////////////// SETTERS AND GETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->m_oApp;
    }

    /**
     * @param Application $p_oApp
     */
    public function setApp(Application $p_oApp)
    {
        $this->m_oApp = $p_oApp;
    }

    /**
     * @return ProcessBuilder
     */
    public function getProcessBuilder()
    {
        return $this->m_oProcessBuilder;
    }

    /**
     * @param ProcessBuilder $p_oBuilder
     */
    public function setProcessBuilder(ProcessBuilder $p_oBuilder)
    {
        $this->m_oProcessBuilder = $p_oBuilder;
    }

    /**
     * @return ImageParser
     */
    public function getImageParser()
    {
        return $this->m_oImageParser;
    }

    /**
     * @param ImageParser $p_oImageParser
     */
    public function setImageParser(ImageParser $p_oImageParser)
    {
        $this->m_oImageParser = $p_oImageParser;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @param Application $p_oApp
     *
     * @return mixed
     */
    public function connect(Application $p_oApp)
    {
        $this->setApp($p_oApp);

        /** @var ControllerCollection $oControllerFactory */
        $oControllerFactory = $p_oApp['controllers_factory'];

        $p_oApp->get('/', array($this, 'getHandler'));

        $p_oApp->post('/', array($this, 'postHandler'));

        return $oControllerFactory;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return file_get_contents('../templates/index.html');
    }

    /**
     * @param Request $p_oRequest
     *
     * @throws \UnexpectedValueException
     *
     * @return JsonResponse
     */
    public function postHandler(Request $p_oRequest)
    {
        $oImageParser = $this->getImageParser();

        $oApp = $this->getApp();
        $sUploadFolder = '/uploads/';
        $sUploadPath = realpath(__DIR__ . '/..') . '/web' . $sUploadFolder;

        $oUploadedFile = $p_oRequest->files->get('file');
        if ($oUploadedFile instanceof UploadedFile) {
            $sExtension = $oUploadedFile->guessExtension();
            $sName = date('Ymd-His');
            $sFileName = $sName . '.' . $sExtension;

            $sFile = $oUploadedFile->move($sUploadPath, $sFileName);
            $sResult = trim($oImageParser->parseImage($sFile));
        } else {
            throw new \UnexpectedValueException('No file was uploaded');
        }

        if (is_numeric($sResult)) {
            $aResponse = array(
                'status' => 'ok',
                'message' => 'Image Successfully Parsed',
                'data' => array(
                    'score' => (int) $sResult,
                    'uploaded-file' =>  $sUploadFolder . $sFileName,
                    'parsed-file' => $sUploadFolder . $oImageParser->getProcessedImageName($oFile),
                ),
            );
        } else {
            $aResponse = array(
                'status' => 'ok',
                'message' => 'Failed Parsing Image',
                'data' => array(
                    'error' => array(
                        'message' => $sResult,
                        'uploaded-file' =>  $sUploadFolder . $sFileName,
                        'parsed-file' => $sUploadFolder . $oImageParser->getProcessedImageName($sFile),
                    ),
                ),
            );
        }

        return $oApp->json($aResponse);
    }
}
////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\


/*EOF*/