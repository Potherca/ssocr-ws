<?php

namespace Potherca\SevenSegmentOcrWebService;

use Potherca\SevenSegmentOcrWebService\ControllerProvider;
use Potherca\SevenSegmentOcrWebService\ImageParser;
use Potherca\SevenSegmentOcrWebService\SsocrProcess;
use Silex\Application;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides a fully populated ControllerProvider to be used with the Application
 */
class ContainerBuilder
{
    /**
     * @param ControllerProvider $p_oControllerProvider
     * @param ImageParser $p_oImageParser
     * @param SsocrProcess $p_oProcess
     * @param ProcessBuilder $p_oProcessBuilder
     *
     * @return mixed
     */
    public static function build(
        ControllerProvider $p_oControllerProvider = null,
        ImageParser $p_oImageParser = null,
        SsocrProcess $p_oProcess = null,
        ProcessBuilder $p_oProcessBuilder = null
    ) {
        $oSelf = new self;
        $oControllerProvider = $oSelf->validateObject($p_oControllerProvider, 'ControllerProvider');
        $oImageParser = $oSelf->validateObject($p_oImageParser, 'ImageParser');
        $oProcess = $oSelf->validateObject($p_oProcess, 'SsocrProcess');
        $oProcessBuilder = $oSelf->validateObject($p_oProcessBuilder, '\Symfony\Component\Process\ProcessBuilder');

        $oImageParser->setProcess($oProcess);
        $oProcess->setProcessBuilder($oProcessBuilder);

        $oControllerProvider->setImageParser($oImageParser);
        $oControllerProvider->setProcessBuilder($oProcessBuilder);

        return $oControllerProvider;
    }

    /**
     * Checks a given object against a given class name. If they do not a match
     * new object is created from the given class name.
     *
     * Class names are expected to either be in the project namespace or be a
     * FQN (Fully Qualified Name).
     *
     * @param $p_oObject
     * @param $p_sClassName
     *
     * @return mixed
     */
    protected function validateObject($p_oObject, $p_sClassName)
    {
        if ($p_sClassName{0} !== '\\') {
            $sClassName = 'Potherca\\SevenSegmentOcrWebService\\' . $p_sClassName;
        } else {
            $sClassName = $p_sClassName;
        }

        if ($p_oObject instanceof $sClassName) {
            $oObject = $p_oObject;
        } else {
            $oReflectionClass  = new \ReflectionClass($sClassName);
            $oObject = $oReflectionClass->newInstance();
        }

        return $oObject;
    }
}

/*EOF*/
