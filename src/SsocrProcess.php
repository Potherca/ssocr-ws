<?php

namespace Potherca\SevenSegmentOcrWebService;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Process to run Ssocr functionality
 */
class SsocrProcess
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @var array
     */
    protected $m_aArguments = array();
    /**
     * @var ProcessBuilder
     */
    protected $m_oProcessBuilder;

    /**
     * @var Process
     */
    protected $m_oProcess;
    //////////////////////////// SETTERS AND GETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->m_aArguments;
    }

    /**
     * @param array $p_aArguments
     */
    public function setArguments(array $p_aArguments)
    {
        $this->m_aArguments = $p_aArguments;
    }

    /**
     * @return ProcessBuilder
     */
    public function getProcessBuilder()
    {
        return $this->m_oProcessBuilder;
    }

    /**
     * @param ProcessBuilder $p_oProcessBuilder
     */
    public function setProcessBuilder(ProcessBuilder $p_oProcessBuilder)
    {
        $this->m_oProcessBuilder = $p_oProcessBuilder;
    }

    /**
     * @return Process
     */
    protected function getProcess()
    {
        return $this->m_oProcess;
    }

    /**
     * @param Process $p_oProcess
     */
    protected function setProcess(Process $p_oProcess)
    {
        $this->m_oProcess = $p_oProcess;
    }
    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return int
     */
    public function run()
    {
        $oProcess = $this->getProcessFromBuilder();
        $this->setProcess($oProcess);
        return $oProcess->run();
    }

    /**
     * Whether or not a process has been successfully executed
     *
     * Will return NULL if a process has not (yet) been executed or TRUE/FALSE
     * for success/failure.
     *
     * @return bool|null
     */
    public function isSuccessful()
    {
        $mSuccess = null;

        $oProcess = $this->getProcess();
        if ($oProcess !== null) {
            $mSuccess = $oProcess->isSuccessful();
        }

        return $mSuccess;
    }

    /**
     * Get the result of the executed process
     * @return string
     */
    public function getOutput()
    {
        $oProcess = $this->getProcess();

        if ($oProcess->isSuccessful() === true) {
            $sOutput = $oProcess->getOutput();
        } else {
            //@TODO: ErrorHandling?
            $sOutput = $oProcess->getErrorOutput();
        }

        return $sOutput;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return Process
     */
    protected function getProcessFromBuilder()
    {
        $oBuilder = $this->getProcessBuilder();
        $aArguments = $this->getArguments();

        $oBuilder->setPrefix('ssocr');
        $oBuilder->setArguments($aArguments);

        $oProcess = $oBuilder->getProcess();

        return $oProcess;
    }

    /**
     * Get the exact command that was executed
     *
     * @return string
     */
    protected function getCommand()
    {
        $oProcess = $this->getProcess();
        $sCommand = $oProcess->getCommandLine();
        return $sCommand;
    }
}

/*EOF*/