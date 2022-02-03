<?php
/**
 * ExceptionLog
 *
 * @package Framework\Commander
 * @version 1.2.0
 */
namespace Framework\Commander;

/**
 * ExceptionLog
 *
 * Handles the logging of exceptions
 */
\Framework\_IncludeCorrect(FRAMEWORK_ROOT."Commander".DSEP."Commander.php");
class ExceptionLog extends \Framework\Commander\Commander
{
  /****************************************************/
  /*              REQUIRED CORE OBJECTS               */
  /****************************************************/
  /** @var \Framework\Core\Euri $_coreEuri */
  private $_coreEuri;
  /****************************************************/
  /*                   CONTROLLERS                    */
  /****************************************************/
  /** @var \Framework\Commander\Tracking $_cTracking */
  private $_cTracking;
  /****************************************************/
  /*                     MODALS                       */
  /****************************************************/
  /** @var \Framework\Modulus\Modal\ $_mException */
  private $_mException;
  /****************************************************/
  /*                  DATA MEMBERS                    */
  /****************************************************/
  /** @var array _dModals */
  private $_dModals = [
    self::sTypeAdministration => 'AdministrationException',
    self::sTypeFunnel => 'FunnelException',
    self::sTypeSite => 'SiteException',
    self::sTypeCron => 'CronException'
  ];

  /**
   * Runs all the setup for the ExceptionLogger object
   * @method construct
   * @return \Framework\Commander\ExceptionLog
   */
  public static function construct()
  {
    /** @var \Framework\Commander\ExceptionLog $instance */
    $instance = self::getInstance();
    /** @var \Framework\Core\Euri _coreEuri */
    $instance->_coreEuri = \Framework\Core\Euri::getInstance();
    /** @var \Framework\Commander\Tracking $_cTracking */
    $instance->_cTracking = \Framework\Core\Receptionist::controller('Tracking', true);
    /** @var \Framework\Modulus\Modal\ $_mException */
    $instance->_mException = \Framework\Core\Receptionist::modal($instance->_modal(), true);
    /** Return the instance */
    return $instance;
  }

  /**
   * Log the exception
   * @method log
   * @param $exception
   * @param $host
   */
  public function log($exception, $host)
  {
    /** Log the exception to the database */
    $this->_toDatabase($exception, $host);
    /** Log the exception to a file */
    $this->_toFile($exception, $host);
  }

  /**
   * Log the exception to the database
   * @method _toDatabase
   * @param $exception
   * @param $host
   * @todo add other system type base exception logs
   */
  private function _toDatabase($exception, $host)
  {
    switch($this->_coreEuri->systemType()){
      case self::administration: $this->_administrationToDatabase($exception, $host); break;
      case self::funnel: $this->_funnelToDatabase($exception, $host); break;
    }
  }

  private function _administrationToDatabase($exception, $host)
  {
    /** @var mixed $previous */
    $previous = $this->_getPrevious($exception);
    /** Write the exception to ethe database */
    $this->_mException->write(
      $this->_getName($exception),
      $this->_getMessage($exception),
      $this->_getCode($exception),
      0,
      $this->_getFile($exception),
      $this->_getLine($exception),
      $this->_serialize($exception),
      $previous,
      $host
    );
  }

  /**
   * Log funnel exception to the database
   * @method _funnelToDatabase
   * @param $exception
   * @param $host
   */
  private function _funnelToDatabase($exception, $host)
  {
    /** @var mixed $previous */
    $previous = $this->_getPrevious($exception);
    /** Write the exception to ethe database */
    $this->_mException->write(
      $this->_getName($exception),
      $this->_getMessage($exception),
      $this->_getCode($exception),
      0,
      $this->_getFile($exception),
      $this->_getLine($exception),
      $this->_serialize($exception),
      $previous,
      $this->_cTracking->sessionKey(),
      $this->_cTracking->_ipAddress(),
      $host
    );
  }

  /**
   * Log the exception to a flat file
   * @method _toFile
   * @param $exception
   * @param $host
   */
  private function _toFile($exception, $host)
  {
    /** @var mixed $previousException get the value returned */
    $previous = $this->_getPrevious($exception);
    /** @var string $message Write the message to the error log */
    $message = "EXCEPTION NAME:{$this->_getName($exception)}\n
      MESSAGE: {$this->_getMessage($exception)}\n
      CODE: {$this->_getCode($exception)}\n
      SEVERITY 0\n
      FILE: {$this->_getFile($exception)}\n
      LINE: {$this->_getLine($exception)}\n
      STACKTRACE: {$this->_serialize($exception)}\n
      PREVIOUS: {$previous}\n
      SESSION KEY: {$this->_cTracking->sessionKey()}\n
      IP ADDRESS: {$this->_cTracking->_ipAddress()}\n
      HOST: {$host}\n";
    error_log($message);
  }

  /**
   * Get Exception name
   * @method _getName
   * @param $exception
   * @return string
   */
  private function _getName($exception)
  {
    if(method_exists($exception,'getName')){
      return $exception->getName();
    }
    return "UnNamed";
  }

  /**
   * Get Exception message
   * @method _getMessage
   * @param $exception
   * @return string
   */
  private function _getMessage($exception)
  {
    if(method_exists($exception,'getMessage')){
      return $exception->getName();
    }
    if(isset($exception['message'])){
      return $exception['message'];
    }
    return 'No Message';
  }

  /**
   * Get Exception Code
   * @method _getCode
   * @param $exception
   * @return int
   */
  private function _getCode($exception)
  {
    if(method_exists($exception,'getCode')){
      return $exception->getCode();
    }
    if(isset($exception['type'])){
      return $exception['type'];
    }
    return 0;
  }

  /**
   * Get Exception File
   * @method _getFile
   * @param $exception
   * @return string
   */
  private function _getFile($exception)
  {
    if(method_exists($exception,'getFile')){
      return $exception->getFile();
    }
    if(isset($exception['file'])){
      return $exception['file'];
    }
    return 'undefinedFile';
  }

  /**
   * Get Exception Line
   * @method _getLine
   * @param $exception
   * @return int
   */
  private function _getLine($exception)
  {
    if(method_exists($exception,'getLine')){
      return $exception->getLine();
    }
    if(isset($exception['line'])){
      return $exception['line'];
    }
    return 0;
  }

  /**
   * Compact the error into a simple form
   * @method _serialize
   * @param $exception
   * @return mixed|null
   */
  private function _serialize($exception)
  {
    if(method_exists($exception,'serialize')){
      return $exception->serialize();
    }
    if(is_array($exception)){
      return print_r($exception,true);
    }
    return null;
  }

  /**
   * Get the previous exception safely
   * @method _getPrevious
   * @param $exception
   * @return mixed|null
   */
  private function _getPrevious($exception)
  {
    /** Check if this is null */
    if(is_null($exception)){
      /** Return null */
      return null;
    }
    /** Check if this is an array */
    if(is_array($exception)){
      /** Return a print_r string of the array */
      return print_r($exception,true);
    }
    /** CHeck if the getPrevious method exists */
    if(method_exists($exception,'getPrevious')){
      /** @var mixed $previous Get the previous exception */
      $previous = $exception->getPrevious();
      /** Check if previous is null */
      if(is_null($previous)){
        /** Return previous */
        return $previous;
      }
      /** Return the output of serialize the exception will do the rest */
      return $previous->serialize();
    }
    /** Not sure how this would happen */
    return null;
  }

  /**
   * Return the modal name
   * @method _modal
   * @return mixed
   */
  private function _modal()
  { return $this->_dModals[$this->_coreEuri->systemType()]; }
}
