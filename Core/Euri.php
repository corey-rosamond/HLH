<?php
/**
 * Euri
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Core;

/**
 * Euri
 *
 * This is in charge of parsing the url and server variables
 * as well as determining the correct system and event types
 */
class Euri extends Core
{
  /****************************************************/
  /*                  DATA MEMBERS                    */
  /****************************************************/
  /** @var \Framework\Support\Object\ArrayCollection $_dServer */
  private $_dServer;
  /** @var \Framework\Support\Object\ArrayCollection $_dData */
  private $_dData;
  /** @var integer $_dSystemType */
  private $_dSystemType;
  /** @var integer $_dEventMode */
  private $_dEventMode;

  /**
   * Configure Euri for the current system
   * @method construct
   * @return \Framework\Core\Euri
   */
  public static function construct()
  {
    /** @var Euri $instance */
    $instance = self::getInstance();
    /** Check if $_dSystemType is set if it is we are already configured return */
    if(!is_null($instance->_dSystemType)){
      /** Already configured return */
      return $instance;
    }
    /** @var \Framework\Support\Object\ArrayCollection $_dServer */
    $instance->_dServer = new \Framework\Support\Object\ArrayCollection();
    /** @var \Framework\Support\Object\ArrayCollection $_dData */
    $instance->_dData = new \Framework\Support\Object\ArrayCollection();
    /** Use array map to quickly iterate the server variables bind to instance so we can use Euri */
    array_map(function($name, $value) use (&$instance){
      /** @var string $name Force all names to lower case so we can match against this */
      $name = strtolower($name);
      /** @var string $parts explode the string on _ so we can break the variables into logical groups */
      $parts = explode( "_", $name, 2);
      /** @var string $section Determine the section to add this variable to */
      $section = ((isset($parts[1]))?$parts[1]:$parts[0]);
      /** Set the variable in the server collection */
      $instance->_dServer->set($section, $value);
    }, array_keys($_SERVER), $_SERVER);
    /** Configure the base event */
    $instance->_configureEvent();
    /** Set the system type */
    $instance->_dSystemType = $instance->_systemType();
    /** Set the event mode */
    $instance->_dEventMode = $instance->_eventMode();
    return $instance;
  }

  /**
   * Return the set system type
   * @method systemType
   * @return int
   */
  public function systemType()
  { return $this->_dSystemType; }

  /**
   * Return the set event mode
   * @method eventMode
   * @return int
   */
  public function eventMode()
  { return $this->_dEventMode; }

  /**
   * Use the key to try and access a value stored in the data collection
   * @method param
   * @param null $key
   * @return array|mixed
   */
  public function param($key = null){
    /** Check if key is null */
    if(!$key){
      /** No key specified return the colleciton as an array */
      return $this->_dData->toArray();
    }
    /** Return the requested value */
    return $this->_dData->get( $key );
  }

  /**
   * Use the key to try and access a value stored in the server collection
   * @method server
   * @param null $key
   * @return array|mixed
   */
  public function server($key = null){
    /** Check if key is null */
    if(!$key){
      /** No key set return the collection as an array */
      return $this->_dServer->toArray();
    }
    /** Return the requested value  */
    return $this->_dServer->get( $key );
  }

  /**
   * Return the correct event mode
   * @method _eventMode()
   * @return int
   */
  private function _eventMode()
  {
    /** Check if we are in CGI mode */
    if(strpos(php_sapi_name(), 'cgi')!==false) {
      /** Return CGI */
      return self::eModeCgi;
    }
    /** Validate if this is a post */
    if(strtolower($this->server('method'))==="post"){
      /** Return POST */
      return self::eModePost;
    }
    /** Return the default GET */
    return self::eModeGet;
  }

  /**
   * Return the correct system type
   * @method _systemType
   * @return int
   */
  private function _systemType()
  {
    /** Check if this is a CGI script */
    if(strpos(php_sapi_name(), 'cgi')!==false) {
      /** Only cron jobs can run in CGI mode */
      return self::sTypeCron;
    }
    /** Check if SystemType was not set in the virtual host */
    if(!isset($_SERVER['SystemType'])) {
      echo "{$this->_timestamp()}[Simplicity]{$this->_sPrefix}: Please set the system type EnVar in your virtual host and restart apache!";
      exit();
    }
    /** Find the matching system */
    switch($_SERVER['SystemType']) {
      /** Administration */
      case self::sTypeAdministration: return self::sTypeAdministration; break;
      /** Funnel */
      case self::sTypeFunnel: return self::sTypeFunnel; break;
      /** Site */
      case self::sTypeSite: return self::sTypeSite; break;
    }
    /** We should never get here but just in case */
    echo "{$this->_timestamp()}[Simplicity]{$this->_sPrefix}: System type not valid!";
    exit();
  }

  /**
   * Setup framework data
   * @method  _configureEvent
   * @return  boolean
   * @access  private
   */
  private function _configureEvent()
  {
    /** Break the return into type and request */
    list($type, $request) = $this->request();
    switch($type){
      case 0:
        /** Check if we have a leading / */
        if($request === '/'){
          /** This is the base entry event */
          $this->_dData->set('event', false );
          /** return we are done */
          return true;
        }
        /** @var array $parts break the request apart on / */
        $parts = explode("/", $request);
        /** Check if the initial part is an empty string */
        if($parts[0]===''){
          /** Shift the empty value out of the array */
          array_shift($parts);
        }
        /** Set the event to everything in the current position up to the ? */
        $parts['event'] = current(explode('?',substr($request, 1)));
        /** Set the data into event */
        $this->_dData->set('event', $parts);
        /** Return to break out of the method */
        return true;
      break;
      case 1: /** SHOULD NEVER HAPPEN */ break;
      default:
        /** Set the event to false nothing was given */
        $this->_dData->set('event', false );
        /** Return to break out of the method */
        return true;
      break;
    }
  }

  /**
   * Find the best candidate for the request
   * @method request
   * @return array
   */
  private function request(){
    /** Check if uri was provided */
    if($this->_dServer->get('uri')){
      /** Return the uri this is our best candidate */
      return [0,$this->_dServer->get('uri')];
    }
    /** Check if url was provided */
    if($this->_dServer->get('url')){
      /** Return the url fallback */
      return [1,$this->_dServer->get('url')];
    }
    /** Return blank */
    return [3,''];
  }
}
