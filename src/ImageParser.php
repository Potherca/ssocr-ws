<?php

namespace Potherca\SevenSegmentOcrWebService;

use Symfony\Component\HttpFoundation\File\File;

/**
 *
 */
class ImageParser
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @var  File
     */
    protected $m_sImageFile;

    /**
     * @var SsocrProcess
     */
    protected $m_oProcess;

    //////////////////////////// SETTERS AND GETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->m_sImageFile;
    }

    /**
     * @param  File $p_sImageFile
     */
    public function setImageFile(File $p_sImageFile)
    {
        $this->m_sImageFile = $p_sImageFile;
    }

    /**
     * @return SsocrProcess
     */
    public function getProcess()
    {
        return $this->m_oProcess;
    }

    /**
     * @param SsocrProcess $p_oProcess
     */
    public function setProcess(SsocrProcess $p_oProcess)
    {
        $this->m_oProcess = $p_oProcess;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @param File $p_oFile
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function parseImage(File $p_oFile)
    {

        if ($p_oFile->isReadable() === false) {
            throw new \InvalidArgumentException('Given file path is not a readable file');
        } else {
            $this->setImageFile($p_oFile);

            $aArguments = $this->buildArguments();
            $oProcess = $this->getProcess();

            $oProcess->setArguments($aArguments);
            $oProcess->run();

            $sOutput = $oProcess->getOutput();

            return $sOutput;
        }
    }

    /**
     * @param File $p_oFile
     *
     * @return string
     */
    public function getProcessedImageName(File $p_oFile = null)
    {
        if ($p_oFile === null) {
            $oFile = $this->getImageFile();
        } else {
            $oFile = $p_oFile;
        }
        $sImagePath = $oFile->getFilename();
        $sExtension = $oFile->guessExtension();

        return basename($sImagePath, $sExtension) . '-processed.' . $sExtension;
    }
    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return array
     */
    protected function buildArguments()
    {
        $sImagePath = $this->getImagePath();

        $sDebugImageFolder = $sImageFolder = __DIR__ . '/uploads/';
        $aParseCommands = array('remove_isolated', 'invert');
        $sThreshold = '95';
        $sDebugImageName = $this->getProcessedImageName();
        $sDebugImagePath = $sDebugImageFolder . '/' . $sDebugImageName;

        $aArguments = array(
            '--debug-image=' . $sDebugImagePath,
            '--threshold=' . $sThreshold,
        );

        $aArguments = array_merge($aArguments, $aParseCommands);

        array_push($aArguments, $sImagePath);

        return $aArguments;
    }

    /**
     * @return string
     */
    protected function getImagePath()
    {
        return $this->getImageFile()->getPathName();
    }

}

/*EOF*/