<?php
/**
 * Advent
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Core;

/**
 * Advent
 *
 * Advent manages running and validating all events and types
 * INFORMATION
 *
 * PREDEFINED EVENTS
 * eEntry             = 0
 * eError             = 1
 * eNotFound          = 2
 * ePermissionDenied  = 3
 * eLogin             = 4
 */
class Advent extends Core
{
  /****************************************************/
  /*                PREDEFINED EVENTS                 */
  /****************************************************/
  /** @const integer eEntry */
  const eEntry = 0;
  /** @const integer eError */
  const eError = 1;
  /** @const integer eNotFound */
  const eNotFound = 2;
  /** @const integer ePermissionDenied */
  const ePermissionDenied = 3;
  /** @const integer eLogin */
  const eLogin = 4;
  /** @const integer eLogout */
  const eLogout = 5;
  /****************************************************/
  /*              REQUIRED CORE OBJECTS               */
  /****************************************************/
  /** @var \Framework\Core\Architect $_coreArchitect */
  private $_coreArchitect;
  /** @var \Framework\Core\Contour $_coreContour */
  private $_coreContour;
  /** @var \Framework\Core\Euri $_coreEuri */
  private $_coreEuri;
  /** @var \Framework\Core\Patron $_corePatron */
  private $_corePatron;
  /** @var array $_dDirectory */
  /****************************************************/
  /*                  DATA MEMBERS                    */
  /****************************************************/
  private $_dDirectory;
  /** @var array $_dNamespace */
  private $_dNamespace;
  /** @var array $_dEventMap */
  private $_dEventMap;
  /** @var array $_dEvents */
  private $_dEvents;
  /** @var mixed $_dActiveEvent This is the event we are currently working on */
  private $_dActiveEvent;

  /**
   * Setup advent with all basic needs
   * @method construct
   * @return \Framework\Core\Advent
   */
  public static function construct()
  {
    /** @var Advent $instance */
    $instance = self::getInstance();
    /** @var Contour _coreContour */
    $instance->_coreContour = Contour::getInstance();
    /** @var Euri _coreEuri */
    $instance->_coreEuri = Euri::getInstance();
    /** @var Architect _coreArchitect */
    $instance->_coreArchitect = Architect::getInstance();
    /** @var string $eventDirectory */
    $eventDirectory = $instance->_coreContour->location('event');
    /** @var array $_dEventMap This is a map of what events are acceptable in what modes and how to match them */
    $instance->_dEventMap = [
      self::eModeGet => [
        'page' => self::eTypePage,
      ],
      self::eModePost => [
        'ajax' =>self::eTypeAjax,
        'form' => self::eTypeForm,
        'command' => self::eTypeCommand,
        'datatable' => self::eTypeDatatable,
        'request' => self::eTypeRequest
      ]
    ];
    /** @var array _dDirectory Array of director's you can find events in */
    $instance->_dDirectory = [
      self::rTypeFramework => [
        self::eTypePage => FRAMEWORK_ROOT."Advent".DSEP."Page".DSEP,
        self::eTypeForm => null,
        self::eTypeAjax => FRAMEWORK_ROOT."Advent".DSEP."Ajax".DSEP,
        self::eTypeCommand => FRAMEWORK_ROOT."Commander".DSEP,
        self::eTypeDatatable => FRAMEWORK_ROOT."Modulus".DSEP."Datatable".DSEP,
        self::eTypeRequest => $eventDirectory."Request".DSEP
      ],
      self::rTypeApplication => [
        self::eTypePage => $eventDirectory."Page".DSEP,
        self::eTypeForm => null,
        self::eTypeAjax => $eventDirectory,
        self::eTypeCommand => FRAMEWORK_ROOT."Commander".DSEP,
        self::eTypeDatatable => FRAMEWORK_ROOT."Modulus".DSEP."Datatable".DSEP,
        self::eTypeRequest => $eventDirectory."Request".DSEP
      ]
    ];
    /** @var array _dNamespace Array of namespaces to use for each system */
    $instance->_dNamespace = [
      self::rTypeFramework => [
        self::eTypePage => FRAMEWORK_ROOT."Advent".DSEP."Page".DSEP,
        self::eTypeForm => '',
        self::eTypeAjax => FRAMEWORK_ROOT."Advent".DSEP."Ajax".DSEP,
        self::eTypeCommand => FRAMEWORK_ROOT."Commander".DSEP,
        self::eTypeDatatable => FRAMEWORK_ROOT."Modulus".DSEP."Datatable".DSEP,
        self::eTypeRequest => FRAMEWORK_ROOT."Modulus".DSEP."Datatable".DSEP,
      ],
      self::rTypeApplication => [
        self::eTypePage => "\\App\\Event\\Page\\",
        self::eTypeForm => '',
        self::eTypeAjax => "\\App\\Event\\",
        self::eTypeCommand => "\\Framework\\Commander\\",
        self::eTypeDatatable => "\\Framework\\Modulus\\Datatable\\",
        self::eTypeRequest => "\\App\\Event\\Request\\"
      ]
    ];
    /** @var array _dEvents This is where we store all of our events */
    $instance->_dEvents = [];
    /** This is the event to run when someone first visits the site */
    $instance->_dEvents[self::eEntry] = $instance->_coreContour->event('entry');
    /** This event is for error handling */
    $instance->_dEvents[self::eError] = $instance->_coreContour->event('error');
    /** This is the 404 event */
    $instance->_dEvents[self::eNotFound] = $instance->_coreContour->event('not-found');
    /** Check if we need Patron */
    if($instance->_coreContour->hasPatron()){
      /** @var \Framework\Core\Patron _corePatron */
      $instance->_corePatron = Patron::getInstance();
      /** The event to run when login is required */
      $instance->_dEvents[self::eLogin] = $instance->_coreContour->event('login');
      /** The event to run for logout */
      $instance->_dEvents[self::eLogout] = $instance->_coreContour->event('logout');
      /** The event to run for permission denied */
      $instance->_dEvents[self::ePermissionDenied] = $instance->_coreContour->event('permission-denied');
    }
    /** @var string $requestedEvent */
    $instance->_dActiveEvent = $instance->_coreEuri->param('event');
    /** Check if the active event is not an array */
    if(!is_array($instance->_dActiveEvent)){
      /** @var array _dActiveEvent Set the active event to the entry page */
      $instance->_dActiveEvent = $instance->_dEvents[self::eEntry];
    }
    /** Configure the event so it is useable */
    $instance->_configureEvent();
    /** Return the configured Advent object */
    return $instance;
  }

  /**
   * Configure a defined event or the active event this must be done before running the event
   * @method _configureEvent
   * @param null $event
   * @return array
   */
  private function _configureEvent($event = null)
  {
    /** Check if were were given a specific event */
    if(is_null($event)) {
      /** Check if this is already set */
      if(isset($this->_dActiveEvent['eventType'])) {
        /** Remove old event type */
        unset($this->_dActiveEvent['eventType']);
      }
      /** Check if this is already set */
      if(isset($this->_dActiveEvent['eventObject'])) {
        /** Remove old eventObject */
        unset($this->_dActiveEvent['eventObject']);
      }
      /** Check if this is already set */
      if(isset($this->_dActiveEvent['eventPath'])) {
        /** Remove old eventPath */
        unset($this->_dActiveEvent['eventPath']);
      }
      /** Check if this is already set */
      if(isset($this->_dActiveEvent['event'])) {
        /** Remove old event */
        unset($this->_dActiveEvent['event']);
      }
      /** @var array _dActiveEvent No event provided _activeEvent assumed */
      return $this->_dActiveEvent = $this->_configureByType($this->_dActiveEvent);
    }
    /** @var array _dActiveEvent Use the provided event object */
    return $this->_configureByType($event);
  }

  /**
   * Determine which of the event configuration methods we need to start with
   * @method _configureByType
   * @param $event
   * @return array
   */
  private function _configureByType($event)
  {
    /** Check if the active event is an array */
    if(!is_array($event)){
      /** Active event is a string it will need more configuration */
      return $this->_stringEventConfigure($event);
    }
    /** We have an array less work to do. Pass it straight to arrayEventConfigure */
    return $this->_arrayEventConfigure($event);
  }

  /**
   * Configure the event array
   * @method _configureEventType
   * @param $event
   * @return mixed
   */
  private function _configureEventType($event)
  {
    /** @var integer $partCount */
    $partCount = sizeof($event);
    /** Check if there is only 1 part */
    if($partCount===1){
      /** Add type to the event array */
      $event['eventType'] = self::eTypePage;
      /** return the event Array */
      return $event;
    }
    /** @var mixed $matchZero event type key or false */
    $matchZero = $this->_matchEventTypeString($event[0]);
    /** Check if no event type was matched */
    if($matchZero===false){
      /** Add the default type to the event array */
      $event['eventType'] = self::eTypePage;
      /** return the event Array */
      return $event;
    }
    /** Remove the first element from the array */
    array_shift($event);
    /** Set the matchZero type */
    $event['eventType'] = $matchZero;
    /** Return the typed Event */
    return $event;
  }

  /**
   * Match the event type string to an event type
   * @method _matchEventTypeString
   * @param $eType
   * @return mixed
   */
  private function _matchEventTypeString($eType)
  {
    /** @var string $eType clean the event type */
    $eType = rtrim(ltrim(strtolower($eType)));
    /** @var integer $eMode */
    $eMode = $this->_coreEuri->eventMode();
    /** Check if we are in cgi mode */
    if($eMode===self::eModeCgi){
      /** We should never get here but just in case */
      echo "{$this->_timestamp()}[Simplicity]{$this->_sPrefix}: CGI Events are not permitted to run via the browser!";
      exit();
    }
    /** Validate the event mode is valid */
    if(!isset($this->_dEventMap[$eMode])){
      /** We should never get here but just in case */
      echo "{$this->_timestamp()}[Simplicity]{$this->_sPrefix}: Event type invalid!";
      exit();
    }
    /** @var array $map The event map for this mode */
    $map = $this->_dEventMap[$eMode];
    if(!isset($map[$eType])){
      /** Check if the event mode is GET */
      if($eMode==self::eModeGet) {
        /** Return the page self::eTypePage */
        return false;
      }
      return false;
    }
    /** Return the event type */
    return $map[$eType];
  }

  /**
   * Start the configuration of the event from a string
   * @method _stringEventConfigure
   * @param $event
   * @return array
   */
  private function _stringEventConfigure($event)
  {
    /** @var string $event Normalize the separators so we know what we are looking for */
    $event = str_replace("\\", "/", $event);
    /** @var array $eventParts break the folders into sections */
    $eventParts = explode("/", $event);
    /** Let the _arrayEventConfiguration do the rest of the work */
    return $this->_arrayEventConfigure($eventParts);
  }

  /**
   * Start the configuration of the event from a string
   * @method _stringEventConfigure
   * @param $event
   * @return array
   */
  private function _arrayEventConfigure($event)
  {
    /** @var array $event Set the eventType for this event */
    $event = $this->_configureEventType($event);
    /** @var integer $objectIndex This is the index where we can find the event object */
    $objectIndex = (sizeof($event)-2);
    /** Check if we still have 2 parts */
    if(!isset($event[$objectIndex])){
      /** We only have 1 part left move the pointer back one */
      $objectIndex--;
    }
    /** Check for a question mark */
    if(strpos($event[$objectIndex],'?') !== false) {
      /** Strip off trailing characters after the question mark */
      $event[$objectIndex] = substr($event[$objectIndex], 0, strpos($event[$objectIndex],"?"));
    }
    /** Set eventObject in the event array */
    $event['eventObject'] = $event[$objectIndex];
    /** Remove the old event object container  */
    unset($event[$objectIndex]);
    /** Add the event path to the array */
    $event['eventPath'] = "";
    /** @var integer $partCount Get the count of parts */
    $partCount = (sizeof($event)-3);
    /** @var int $index loop through the remaining orignal event parts and use them to make the path */
    for($index = 0; $index<$partCount; $index++){
      /** Append the value with slash to the end of the eventPath */
      $event['eventPath'] .= "{$event[$index]}/";
    }
    /** return our configured Event array */
    return $event;
  }

  /**
   * Qualify and run the event via is specific methods
   * @method run
   * @param array $parameters
   * @param bool  $isFramework
   * @return bool|int|void
   */
  public function run(array $parameters=array(), $isFramework=false)
  {
    if($parameters!==false){

    }
    /** If we can not find the specified event then give them a 404 */
    if(!$this->_eventInclude($this->_dActiveEvent, $isFramework)){
      $this->_coreArchitect->notFoundPage();
    }
    /** @var string $qualifiedName Get the fully qualified event name */
    $qualifiedName = $this->_qualifiedName($this->_dActiveEvent, $isFramework);
    /** Validate the event */
    if($this->_validEvent($qualifiedName, $this->_dActiveEvent)){
      /** Run the event with its type specific function */
      return $this->_runType($qualifiedName, $this->_dActiveEvent, $parameters);
    }
  }

  /**
   * Run the specific event type
   * @method _runType
   * @param $qualifiedName
   * @param $eventConfiguration
   * @param $parameters
   * @return bool|int|void
   * @todo Remove request and replace with Ajax type
   */
  private function _runType($qualifiedName, $eventConfiguration, $parameters)
  {
    /** Find the correct validation method for this event type */
    switch(intval($eventConfiguration['eventType'])){
      /** Run Page Event */
      case self::eTypePage: return $this->_runPage($qualifiedName, $parameters); break;
      /** Run Form Event */
      case self::eTypeForm: return $this->_runForm($qualifiedName, $parameters); break;
      /** Run Ajax Event */
      case self::eTypeAjax: return $this->_runAjax($qualifiedName, $parameters); break;
      /** Run Command Event */
      case self::eTypeCommand: return $this->_runCommand($qualifiedName, $parameters); break;
      /** Run Datatable Event */
      case self::eTypeDatatable: return $this->_runDatatable($qualifiedName, $parameters); break;
      /** Run Request Event */
      case self::eTypeRequest: return $this->_runRequest($qualifiedName, $parameters); break;
      /** Return false this should never happen */
      default: return false; break;
    }
  }

  /**
   * Run the page event
   * @method _runPage
   * @param $qualifiedName
   * @param $parameters
   * @return bool
   */
  private function _runPage($qualifiedName, $parameters)
  {
    /** @var \Framework\Advent\Page $EventObject */
    $EventObject = $qualifiedName::initalize($parameters);
    /** Take the initialized event and run it */
    $EventObject->run();
    /** Exit to make sure we don't try and double render */
    exit();
  }

  /**
   * Run the form event
   * @method _runForm
   * @param $qualifiedName
   * @param $parameters
   * @return bool
   */
  private function _runForm($qualifiedName, $parameters)
  {

    return true;
  }

  /**
   * Run the Ajax event
   * @method _runAjax
   * @param $qualifiedName
   * @param $parameters
   * @return bool
   */
  private function _runAjax($qualifiedName, $parameters)
  {

    return true;
  }

  /**
   * Run the Command Event
   * @method _runCommand
   * @param $qualifiedName
   * @param $parameters
   * @return bool
   */
  private function _runCommand($qualifiedName, $parameters)
  {
    /** Run the event with qualified domain name */
    $qualifiedName::construct($parameters)->event();
    /** return true */
    return true;
  }

  /**
   * Run the Datatable Event
   * @method _runDatatable
   * @param $qualifiedName
   * @param $parameters
   * @return bool
   */
  private function _runDatatable($qualifiedName, $parameters)
  {
    $qualifiedName = $this->_dActiveEvent['eventObject'];
    $EventObject = \Framework\Core\Receptionist::datatable($qualifiedName, true);
    $EventObject->run();
    return true;
  }

  /**
   * Run the Request Event
   * @method _runRequest
   * @param $qualifiedName
   * @param $parameters
   * @return bool
   */
  private function _runRequest($qualifiedName, $parameters)
  {
    $EventObject = new $qualifiedName($parameters);
    $EventObject->run();
    return true;
  }

  /**
   * Run and return the event specific validation functoin
   * @method _validEvent
   * @param $qualifiedName
   * @param $eventConfiguration
   * @return bool|int|void
   */
  private function _validEvent($qualifiedName, $eventConfiguration)
  {
    /** Find the correct validation method for this event type */
    switch(intval($eventConfiguration['eventType'])){
      /** Validate Page Event */
      case self::eTypePage: return $this->_validatePage($qualifiedName); break;
      /** Validate Form Event */
      case self::eTypeForm: return $this->_validateForm($qualifiedName); break;
      /** Validate Ajax Event */
      case self::eTypeAjax: return $this->_validateAjax($qualifiedName); break;
      /** Validate Command Event */
      case self::eTypeCommand: return $this->_validateCommand($qualifiedName); break;
      /** Validate Datatable Event */
      case self::eTypeDatatable: return $this->_validateDatatable($qualifiedName); break;
      /** Validate Request Event */
      case self::eTypeRequest: return $this->_validRequest($qualifiedName); break;
      /** Return false this should never happen */
      default: return false; break;
    }
  }

  /**
   * Validate the Page Event
   * @method _validatePage
   * @param $qualifiedName
   * @return bool
   */
  private function _validatePage($qualifiedName)
  {
    /** Does the user need to be logged in and are they */
    if($qualifiedName::$requiresLogin&&!$this->_corePatron->isLoggedIn()){
      /** @var string _dActiveEvent Set the active event to the long event */
      $this->_dActiveEvent = $this->_dEvents[self::eLogin];
      /** Configure the event for use */
      $this->_configureEvent();
      /** Run the new event */
      return $this->run();
    }
    /** If no permission group is required we are done return true */
    if(!$qualifiedName::$permissionGroup){
      return true;
    }
    /** Check the required permission group */
    if(!$this->_corePatron->checkGroup($qualifiedName::$permissionGroup)){
      /** Redirect the user to the permission denied page */
      echo 'permission denied';
      exit();
    }
    /** If no permission is required we are done return true */
    if(!$qualifiedName::$permission){
      return true;
    }
    /** Check the required permission */
    if(!$this->_corePatron->checkPermission($qualifiedName::$permissionGroup, $qualifiedName::$permission)){
      /** Redirect the user to the permission denied page */
      echo 'permission denied';
      exit();
    }
    /** Return true all checks passed */
    return true;
  }

  /**
   * Validate the Form Event
   * @method _validateForm
   * @param $qualifiedName
   * @return bool
   */
  private function _validateForm($qualifiedName)
  {

    return true;
  }

  /**
   * Validate the Ajax Event
   * @method _validateAjax
   * @param $qualifiedName
   * @return bool
   */
  private function _validateAjax($qualifiedName)
  {

    return true;
  }

  /**
   * Validate the Command Event
   * @method _validateCommand
   * @param $qualifiedName
   * @return bool
   */
  private function _validateCommand($qualifiedName)
  {

    return true;
  }

  /**
   * Validate the Datatable Event
   * @method _validateDatatable
   * @param $qualifiedName
   * @return bool
   */
  private function _validateDatatable($qualifiedName)
  {

    return true;
  }

  /**
   * Validate the Request Event
   * @method _validRequest
   * @param $qualifiedName
   * @return bool
   */
  private function _validRequest($qualifiedName)
  {

    return true;
  }

  /**
   * Return the current active events parts
   * @method activeEvent
   * @return array
   */
  public function activeEvent()
  {
    $event = $this->_qualifiedName($this->_dActiveEvent, false);
    $tmp = str_replace("/", "\\", $event);
    $parts = explode("\\", $tmp);
    $position = array_search ("Page", $parts);
    $parts = array_slice($parts, ($position+1));
    return $parts;
  }

  /**
   * Return the fully qualifiedName for this event object
   * @method _qualifiedName
   * @param $event
   * @param $isFramework
   * @return mixed
   */
  private function _qualifiedName($event, $isFramework)
  { return str_replace("/", "\\", $this->_dNamespace[$this->_system($isFramework)][$event['eventType']].$event['eventPath'].$event["eventObject"]); }

  /**
   * Include the specified event Object
   * @method _eventInclude
   * @param $event
   * @param $isFramework
   * @return boolean
   */
  private function _eventInclude($event, $isFramework)
  {
    try {
      /** Try and include the Event object */
      \Framework\_IncludeCorrect($this->_dDirectory[$this->_system($isFramework)][$event['eventType']].$event['eventPath'].$event["eventObject"].".php");
    } catch (\Framework\Exceptional\IncludeOnceException $exception){
      /** Could not include the object return false */
      return false;
    }
    /** All went wel return true */
    return true;
  }

  /**
   * Return the Resource type identifier
   * @method _system
   * @param $isFramework
   * @return int
   */
  private function _system($isFramework)
  {
    /** Check if isFramework is true */
    if(!$isFramework){
      /** Return the Resource Type self::rTypeApplication */
      return self::rTypeApplication;
    }
    /** Return the Resource Type self::rTypeFramework */
    return self::rTypeFramework;
  }
}

