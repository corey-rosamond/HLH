<?php
/**
 * Singleton
 *
 * @package Framework\Support\Abstract
 * @version 1.0.0
 */
namespace Framework\Support\Abstracts;

/**
 * Singleton
 *
 * This is the main singleton abstract it is written to allow
 * you to create multiple singleton objects with only itself
 **/
\Framework\_IncludeCorrect( FRAMEWORK_ABSTRACT."ExceptionSafe.php" );
\Framework\_IncludeCorrect( FRAMEWORK_INTERFACE."Singleton.php" );
abstract class Singleton extends \Framework\Support\Abstracts\ExceptionSafe implements \Framework\Support\Interfaces\Singleton
{
  /**
   * These are all of the objects the singleton abstract has created
   * @var     array   $instances
   * @access  private
   * @static
   */
  private static $instances = array();

  /**
   * You can't construct an singleton
   * @method   __construct
   * @return  void
   * @access  private
   **/
  private function __construct()
  {}

  /**
   * Set the clone to private so it cant be used
   * @method __clone
   * @return void
   * @access private
   * @final
   **/
  private final function __clone()
  {}

  /**
   * Set the wakeup to private so it cant be used
   * @method  __wakeup
   * @return  void
   * @access  private
   * @final
   **/
  private final function __wakeup()
  {}

  /**
   * Use the get_called_class method to check if we
   * have created an instance of this object if we
   * have not create it
   * @method getInstance
   * @return object
   * @access public
   * @static
   **/
  public static function getInstance()
  {
    $class = get_called_class();
    if(!isset(self::$instances[$class])){
      self::$instances[$class] = new static;
    }
    return self::$instances[$class];
  }
}
