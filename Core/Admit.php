<?php
/**
 * Admit
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Core;

/**
 * Admit
 *
 * This is in charge of logging errors
 */
class Admit extends Core
{
  /****************************************************/
  /*                   CONTROLLERS                    */
  /****************************************************/
  /** @var \Framework\Commander\ExceptionLog $_cExceptionLog **/
  private $_cExceptionLog;
  /****************************************************/
  /*                   DATA MEMBERS                   */
  /****************************************************/
  /** @var  \Framework\Exceptional\BaseException $_dLastException */
  private $_dLastException;
  /** @var array $_dExceptionHistory */
  private $_dExceptionHistory = [];

  /**
   * Construct the Admit object
   * @method construct
   * @return mixed
   */
  public static function construct()
  {
    /** @var Contour $instance */
    $instance = self::getInstance();
    /** @var \Framework\Commander\ExceptionLog $_cExceptionLog **/
    $instance->_cExceptionLog = Receptionist::controller("ExceptionLog", true);
    /** Return the configured Admit instance */
    return $instance;
  }

  /**
   * Set the last exception variable
   * @method _setLastException
   * @param $exception
   * @return mixed
   */
  public static function _setLastException($exception)
  {
    $instance = self::getInstance();
    $instance->_dLastException = $exception;
    return $instance->_updateExceptionHistory($exception);
  }

  /**
   * Add the most recent exception to the history
   * @method _updateExceptionHistory
   * @param $exception
   * @return int
   */
  private function _updateExceptionHistory($exception)
  {
    /** Push the new exception into the exception history */
    array_push($this->_dExceptionHistory, $exception);
    /** Return the index of this exception in the log */
    return (sizeof($this->_dExceptionHistory)-1);
  }

  /**
   * Log an exception
   * @method log
   * @param $exception
   * @param $host
   */
  public function log($exception, $host)
  { $this->_cExceptionLog->log($exception, $host); }
}
