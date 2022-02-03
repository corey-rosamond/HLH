<?php
/**
 * ExcpetionSafe
 *
 * @package Framework\Support\Abstracts
 * @version 1.0.0
 */
namespace Framework\Support\Abstracts;

/**
 * ExcpetionSafe
 *
 * The exception saffe object is made for one purpose. We dont want errors to get
 * to php so we wrap these functions so that when something happens an exception gets
 * thrown and we can handle it without apache or php getting involved
 **/
\Framework\_IncludeCorrect( FRAMEWORK_INTERFACE."ExceptionSafe.php" );
abstract class ExceptionSafe implements \Framework\Support\Interfaces\ExceptionSafe
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
   public function __call( $method, $arguments )
   {
     $class    = get_called_class();
     $bt       = debug_backtrace()[0];
     $filename = $bt['file'];
     $lineno   = $bt['line'];
     $message  = "Call to Undefined method $class::$method";
     throw new \Framework\Exceptional\UndefinedMethodException( $message, 0, 1, $filename, $lineno, null);
     return true;
   }

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
   public static function __callStatic( $method, $arguments )
   {
     $class    = get_called_class();
     $bt       = debug_backtrace()[0];
     $filename = $bt['file'];
     $lineno   = $bt['line'];
     $message  = "Call to Undefined static method $class::$method";
     throw new \Framework\Exceptional\UndefinedStaticMethodException( $message, 0, 1, $filename, $lineno, null);
   }
 }
