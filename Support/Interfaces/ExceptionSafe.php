<?php
/**
 * ExcpetionSafe
 *
 * @package Framework\Support\Interfaces
 * @version 1.0.0
 */
namespace Framework\Support\Interfaces;

/**
 * Singleton
 *
 * ExcpetionSafe Better programming with exceptions
 **/
interface ExceptionSafe
{
  /**
   * Make sure all undefined method calls are cause exceptions and do not get
   * To php for it to throw a fatal exception we can not recover from
   * @method    __call
   * @param     string  $method
   * @param     array   $arguments
   * @return    true
   * @throws    \Framework\Exceptional\UndefineUndefinedMethodException
   * @access    public
   */
   public function __call($method, $arguments);

   /**
    * Catch all undefined static method calls and throw an exception for them rather
    * than letting php cause a fatal error that we can not recover from
    * @method   __call
    * @param    string  $method
    * @param    array   $arguments
    * @return   true
    * @throws   \Framework\Exceptional\UndefinedStaticMethodException
    * @access   public
    */
   public static function __callStatic($method, $arguments);
}
