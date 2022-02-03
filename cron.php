<?php

namespace Framework;

require __DIR__ . '/vendor/autoload.php';

/** Define the correct and incorrect seperators */
define('DSEP', DIRECTORY_SEPARATOR);
define('RSEP', ((DSEP === '/') ? '\\' : '/'));

/**
 * Return a path with the correct platform directory seperators
 * @param  string $path Path before conversion to platform specific seperators
 * @return string       Path after conversion to platform specific seperators
 */
function _FixPath($path){
  return str_replace( RSEP, DSEP, $path );
}

/**
 * Include a file with the correct seperators and the correct method enabling exceptions
 * @param  string $path Path before conversion to platform specific seperators
 * @return void
 * @uses   \Framework\_FixPath()
 */
function _IncludeCorrect($path)
{
  include_once _FixPath($path);
}


/** Setup the base defines to get us running */
define("WEB_ROOT", dirname(__DIR__) . DSEP);
define("FRAMEWORK_ROOT", __DIR__ . DSEP);
define("FRAMEWORK_SUPPORT", FRAMEWORK_ROOT . "Support" . DSEP);
define("FRAMEWORK_ABSTRACT", FRAMEWORK_SUPPORT . "Abstracts" . DSEP);
define("FRAMEWORK_INTERFACE", FRAMEWORK_SUPPORT . "Interfaces" . DSEP);
define("FRAMEWORK_OBJECT", FRAMEWORK_SUPPORT . "Object" . DSEP);
define("FRAMEWORK_APPLICATION", FRAMEWORK_ROOT . "Application" . DSEP);
define("CONFIGURATION_ROOT", FRAMEWORK_ROOT . 'Configuration' . DSEP);
define("LOG_DIRECTORY", FRAMEWORK_ROOT . "Logs" . DSEP);

ini_set('display_errors', 0);

_IncludeCorrect(FRAMEWORK_ROOT."Exceptional".DSEP."ExceptionInterface.php");
_IncludeCorrect(FRAMEWORK_ROOT."Exceptional".DSEP."BaseException.php");
_IncludeCorrect(FRAMEWORK_ROOT."Exceptional".DSEP."StandardExceptions.php");
_IncludeCorrect(FRAMEWORK_ROOT."Exceptional".DSEP."StandardFaults.php");
_IncludeCorrect(FRAMEWORK_ROOT."Exceptional".DSEP."Handler.php");
_IncludeCorrect(FRAMEWORK_ROOT."Event".DSEP."Autoload.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Core.php");
_IncludeCorrect(FRAMEWORK_ROOT."Modulus".DSEP."Mysql.interface.php");
_IncludeCorrect(FRAMEWORK_ROOT."Modulus".DSEP."PDOdatabases.php");
_IncludeCorrect(FRAMEWORK_ROOT."Modulus".DSEP."Query.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Docket.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Contour.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Receptionist.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Admit.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Euri.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Advent.php");
_IncludeCorrect(FRAMEWORK_ROOT."Core".DSEP."Fiolos.php");

set_error_handler( array('Framework\Exceptional\Handler', 'StandardException') );
register_shutdown_function( array('Framework\Exceptional\Handler', 'FatalException') );
$Docket = \Framework\Core\Docket::getInstance();
$Docket->run();
