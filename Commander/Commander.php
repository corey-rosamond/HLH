<?php
/**
 * Commander
 *
 * @package Framework\Commander
 * @version 1.2.0
 */
namespace Framework\Commander;

/**
 * Commander
 *
 * SYSTEM OR APPLICATION TYPE TO LOAD
 * sTypeAdministration  = 0
 * sTypeFunnel          = 1
 * sTypeSite            = 2
 * sTypeCron            = 3
 *
 * @todo remove old administration constant references from the command objects
 */
class Commander
{
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
  /** @const integer administration */
  const administration = 0;
  /** @const integer funnel */
  const funnel = 1;
  /** @const integer site  */
  const site = 2;
  /** @const integer cron  */
  const cron = 3;
  /** @var string _prefix */
  protected $_sPrefix;
  /** @var array _instances */
  private static $_instances = array();
  /** @var boolean|string $_dEventAction */
  protected $_dEventAction;
  /** @var array $_dEventData */
  protected $_dEventData;

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
    return true;
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
   * @param $paramaters
   * @return mixed
   */
  protected function _log($paramaters)
  {
    $admit = \Framework\Core\Admit::getInstance();
    return $admit->_log($paramaters);
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

  /**
   * Send the event response for command objects
   * @method _eRequest
   * @param $result
   * @param $message
   */
  protected function _eResponse($result, $message)
  {
    /** Make sure message is an array */
    if(!is_array($message)){
      /** @var array $message Wrap the string in an array */
      $message = [$message];
    }
    /** Return the response to the requester */
    echo json_encode(["result"=>$result,"message"=>$this->_eResponseCleaner($message)]);
    /** Exit to avoid anything happening after this point */
    exit();
  }

  /**
   * Configure the vent if it is valid
   * @method _eConfigure
   */
  protected function _eConfigure()
  {
    /** Check that action was passed */
    if(!isset($_POST['action'])){
      /** Envoke _eResponse to send the error back and exit */
      $this->_eResponse(false, 'Error');
    }
    /** @var string $_dEventAction */
    $this->_dEventAction = $_POST['action'];
    /** @var array $_dEventData */
    $this->_dEventData = $_POST;
    /** Unset the event action in the data to avoid confusion */
    unset($this->_dEventData[$this->_dEventAction]);
  }

  /**
   * Clean the response array
   * @method _eResponseCleaner
   * @param $array
   * @return mixed
   */
  protected function _eResponseCleaner($array)
  {
    array_walk_recursive($array, function(&$item, $key){
      if(!mb_detect_encoding($item, 'utf-8', true)){
        $item = utf8_encode($item);
      }
    });
    return $array;
  }
}