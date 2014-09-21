<?php

namespace Potherca\SevenSegmentOcrWebService;

use Potherca\SevenSegmentOcrWebService\ContainerBuilder;
use Potherca\Silex\ApiExceptionHandler;
use Silex\Application;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;

////////////////////////////////////// SETUP \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
require '../vendor/autoload.php';

$bDebug = true;/*@TODO: Load from a config file. Which? What? Where? */

///////////////////////////////// ERROR HANDLING \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
/* Convert all errors to exceptions */
ErrorHandler::register(E_ALL | E_STRICT, $bDebug);

///////////////////////////////// INITIALISATION \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$oControllerProvider = ContainerBuilder::build();
$oApp = new Application(array('debug' => $bDebug));

/////////////////////////////// REGISTER URL ROUTES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$oApp->mount('/', $oControllerProvider);

///////////////////////////////// RUN APPLICATION \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$oApp->run();

exit;
/*EOF*/
