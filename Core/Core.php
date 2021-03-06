<?php
/**
 * Core
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Core;

/**
 * Core
 *
 * This the base for all of the core objects it provides all of the needed methods and functions
 * they need to enable them to do their job. Keep in mind all core objects are singletons with
 * sudo constructors this enables them to have their own setup
 * INFORMATION
 *
 * RESOURCE TYPES
 * rTypeFramework       = 0
 * rTypeApplication     = 1
 *
 * SYSTEM OR APPLICATION TYPE TO LOAD
 * sTypeAdministration  = 0
 * sTypeFunnel          = 1
 * sTypeSite            = 2
 * sTypeCron            = 3
 *
 * FRAMEWORK MODE
 * fModeLive            = 0
 * fModeDevelopment     = 1
 * fModeMaintance       = 2
 * fModeComingsoon      = 3
 * fModeOutofstock      = 4
 *
 * EVENT MODES
 * eModePost            = 0
 * eModeGet             = 1
 * eModeCgi             = 2
 *
 * EVENT TYPES
 * eTypePage            = 0
 * eTypeForm            = 1
 * eTypeAjax            = 2
 * eTypeCommand         = 3
 * eTypeDatatable       = 4
 * eTypeRequest         = 5 ! TEMPORARY SHOULD BE REMOVED !
 *
 * @todo Remove eTypeRequst replace all current uses with eTypeAjax
 */
class Core
{
  /****************************************************/
  /*                 RESOURCE TYPE                    */
  /****************************************************/
  /** @const integer framework */
  const rTypeFramework = 0;
  /** @const integer application */
  const rTypeApplication = 1;
  /****************************************************/
  /*                  SYSTEM TYPE                     */
  /****************************************************/
  /** @const integer administration */
  const sTypeAdministration = 0;
  /** @const integer funnel */
  const sTypeFunnel = 1;
  /** @const integer site  */
  const sTypeSite = 2;
  /** @const integer cron */
  const sTypeCron = 3;
  /** @const integer live  */
  /****************************************************/
  /*                 FRAMEWORK MODE                   */
  /****************************************************/
  /** @const integer fModeLive */
  const fModeLive = 0;
  /** @const integer fModeDevelopment */
  const fModeDevelopment = 1;
  /** @const integer fModeMaintenance */
  const fModeMaintenance = 2;
  /** @const integer fModeComingsoon */
  const fModeComingsoon = 3;
  /** @const integer fModeOutofstock */
  const fModeOutofstock = 4;
  /****************************************************/
  /*                  EVENT MODES                     */
  /****************************************************/
  /** @const integer eModePost */
  const eModePost = 0;
  /** @const integer eModeGet */
  const eModeGet = 1;
  /** @const integer eModeCgi */
  const eModeCgi = 2;
  /****************************************************/
  /*                   EVENT TYPES                    */
  /****************************************************/
  /** @const integer eTypePage */
  const eTypePage = 0;
  /** @const integer eTypeForm */
  const eTypeForm = 1;
  /** @const integer eTypeAjax */
  const eTypeAjax = 2;
  /** @const integer eTypeCommand */
  const eTypeCommand = 3;
  /** @const integer eTypeDatatable */
  const eTypeDatatable = 4;
  /** @const integer eTypeRequest */
  const eTypeRequest = 5;
  /** @var string _prefix */
  protected $_sPrefix;
  /** @var array _instances */
  private static $_instances = array();

  /**
   * Set the construct to private and empty so it cant be used
   * @method __construct
   */
  private function __construct()
  {}

  /**
   * Set the __clone method to final and private so it can not be used
   * @method __clone
   */
  private final function __clone()
  {}

  /**
   * Set the __wakeup method to final and private so it can not be used
   * @method __wakeup
   **/
  private final function __wakeup()
  {}

  /**
   * Capture calls to undefined standard methods and throw them as exceptions
   * @method __call
   * @param $method
   * @param $arguments
   * @return bool
   * @throws \Framework\Exceptional\UndefinedMethodException
   */
  public function __call( $method, $arguments )
  {
    $class = get_called_class();
    $backtrace = debug_backtrace()[0];
    $filename = $backtrace['file'];
    $line = $backtrace['line'];
    $message = "Call to Undefined method $class::$method";
    throw new \Framework\Exceptional\UndefinedMethodException( $message, 0, 1, $filename, $line, null);
  }

  /**
   * Capture calls to undefined static methods and throw them as exceptions
   * @method __callStatic
   * @param $method
   * @param $arguments
   * @throws \Framework\Exceptional\UndefinedStaticMethodException
   */
  public static function __callStatic( $method, $arguments )
  {
    $class = get_called_class();
    $backtrace = debug_backtrace()[0];
    $filename = $backtrace['file'];
    $line = $backtrace['line'];
    $message  = "Call to Undefined static method $class::$method";
    throw new \Framework\Exceptional\UndefinedStaticMethodException( $message, 0, 1, $filename, $line, null);
  }

  /**
   * Return an already created instance or make one and return it
   * @method getInstance
   * @return mixed
   */
  public static function getInstance()
  {
    $class = get_called_class();
    if(!isset(self::$_instances[$class])){
      self::$_instances[$class] = new static;
      self::$_instances[$class]->_sPrefix = "[{$class}]";
      self::$_instances[$class]->construct();
    }
    return self::$_instances[$class];
  }

  /**
   * Log the given information in some way
   * @method _log
   * @param $parameters
   * @return mixed
   */
  protected function _log($parameters)
  {
    $admit = Admit::getInstance();
    return $admit->_log ($parameters );
  }

  /**
   * Return a formatted time stamp
   * @method _timestamp
   * @return string
   */
  protected function _timestamp()
  {
    $time = time();
    return "[{$time}]";
  }
}