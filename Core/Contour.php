<?php
/**
 * Contour
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Core;
/**
 * Contour
 *
 * The contour core object is our settings settings
 * interface anything you want to do with settings this object does
 * INFORMATION
 *
 * CONFIGURATION TYPES
 * cTypeSetting         = 0
 * cTypeLocation        = 1
 * cTypeWebasset        = 2
 * cTypeEvent           = 3
 * cTypePatron          = 4
 * cTypeService         = 5
 *
 * @todo the fastest way to store an object outside the data base to be reinitialized
 * @todo interface for adding to framework configuration without hand coding it into the file
 * @todo look into how a full php implementation of object caching would work
 */
class Contour extends Core
{
  /****************************************************/
  /*               CONFIGURATION TYPES                */
  /****************************************************/
  /** @const integer cTypeSetting */
  const cTypeSetting = 0;
  /** @const integer cTypeLocation */
  const cTypeLocation = 1;
  /** @const integer cTypeWebasset */
  const cTypeWebasset = 2;
  /** @const integer cTypeEvent */
  const cTypeEvent = 3;
  /** @const integer cTypePatron */
  const cTypePatron = 4;
  /** @const integer cTypeService */
  const cTypeService = 5;
  /****************************************************/
  /*              REQUIRED CORE OBJECTS               */
  /****************************************************/
  /** @var Euri $_coreEuri */
  private $_coreEuri;
  /****************************************************/
  /*                    DATA MEMBERS                  */
  /****************************************************/
  /** @var array $_dCollection */
  private $_dCollection;
  /** @var mixed $_dNavigation The navigation array or null */
  private $_dNavigation;

  /**
   * Setup contour with all basic needs
   * @method construct
   * @return Contour
   */
  public static function construct()
  {
    /** @var Contour $instance */
    $instance = self::getInstance();
    /** Check if $_dCollection is set if it is we are already configured return */
    if(!is_null($instance->_dCollection)){
      /** Already configured return */
      return $instance;
    }
    /** @var Euri _coreEuri */
    $instance->_coreEuri = Euri::getInstance();
    /** Load the system type we determined was requested */
    switch($instance->_coreEuri->systemType()){
      /** Administration */
      case self::sTypeAdministration:
        /** @var string $fileContents */
        $fileContents = CONFIGURATION_ROOT."framework.json";
        /** @var array $settings */
        $settings = json_decode(file_get_contents($fileContents), true);
        /** Run the administration configuration */
        $instance->_administration($settings['hosts']);
        break;
      /** Funnel System */
      case self::sTypeFunnel:
        /** Run the funnel configuration method */
        $instance->_funnel();
        break;
      /** Site */
      case self::sTypeSite:
        /** Load a Funnel Site */
        echo 'Site System';
        echo 'should load coming soon here or cause coming soon event';
        exit();
        break;
      /** Cron Job */
      case self::sTypeCron:
        /** @var string $fileContents */
        $fileContents = CONFIGURATION_ROOT."framework.json";
        /** @var array $settings */
        $settings = json_decode(file_get_contents($fileContents), true);
        /** Run the administration configuration */
        $instance->_cli($settings['hosts']);
        break;
    }
    /** Return the configured Contour instance */
    return $instance;
  }

  /**
   * Configure framework to run in cli mode
   * @method _cli
   * @param $data
   */
  private function _cli($data)
  {
    /** @var array $data set data to the correct position in the configuration array */
    $data = $data[$_GET['system']];
    /** Setup the Collections */
    $this->_setupCollections($data);
    /** Finalize the setup */
    $this->_finalizeSetup();
  }

  /**
   * Setup and configure all needed settings constants and objects for administration
   * @method _administration
   * @param $data
   */
  private function _administration($data)
  {
    /** @var array $data set data to the correct position in the configuration array */
    $data = $data[$this->_serverName()];
    /** Setup the Collections */
    $this->_setupCollections($data);
    /** Finalize the setup  */
    $this->_finalizeSetup();
  }

  /**
   * Setup and configure all needed settings constants and objects for a funnel
   * @method _funnel
   */
  private function _funnel()
  {
    /** @var array $data */
    $data = [];
    /** Setup the funnel resource locations */
    $data['locations'] = [
      'event' => FRAMEWORK_APPLICATION.'Event/',
      'request' => FRAMEWORK_APPLICATION.'Event/Request',
      'modal' => FRAMEWORK_APPLICATION.'common/Modal/',
      'crontab' => FRAMEWORK_APPLICATION.'common/Cron/',
      'controller' => FRAMEWORK_APPLICATION.'common/Controller/'
    ];
    /** Configure the funnel default settins */
    $data['settings'] = [
      'mode' => 1,
      'entry' => 'System',
      'directory' => 'Funnel',
      'site' => 2,
      'chunk-size' => 4096
    ];
    /** Setup the collection objects */
    $this->_setupCollections($data);
    /** Finalize the configuration */
    $this->_finalizeSetup();
  }

  /**
   * Setup all of the available collections that are needed for framework
   * @method _setupCollections
   * @param $data
   */
  private function _setupCollections( $data )
  {
    /** Make sure settings were provided */
    if(isset($data['settings'])){
      /** Use the settings array to initialize the collection */
      $this->_dCollection[self::cTypeSetting] = new \Framework\Support\Object\ArrayCollection($data['settings']);
    }
    /** Make sure locations were provided */
    if(isset($data['locations'])) {
      /** Use the locations array to initialize the collection */
      $this->_dCollection[self::cTypeLocation] = new \Framework\Support\Object\ArrayCollection($data['locations']);
    }
    /** Make sure web assets were provided  */
    if(isset($data['web-assets'])){
      /** Use the web assest array to initialize the collection */
      $this->_dCollection[self::cTypeWebasset] = new \Framework\Support\Object\ArrayCollection($data['web-assets']);
    }
    /** Make sure events were provided */
    if(isset($data['events'])){
      /** Use the events array to initialize the collection */
      $this->_dCollection[self::cTypeEvent] = new \Framework\Support\Object\ArrayCollection($data['events']);
    }
    /** Make sure services were provided */
    if(isset($data['services'])){
      /** Use the services array to initialise the collection */
      $this->_dCollection[self::cTypeService] = new \Framework\Support\Object\ArrayCollection($data['services']);
    }
    /** Check if a patron configuration array was provided */
    if(isset($data['patron'])){
      /** use the provided patron configuration array to initialize the collection */
      $this->_dCollection[self::cTypePatron] = new \Framework\Support\Object\ArrayCollection($data['patron']);
    }
    /** Check if a navigation configuration array was provided */
    if(isset($data['patron'])) {
      /** @var array _navigation */
      $this->_dNavigation = $data['navigation'];
    }
  }

  /**
   * Setup the defines, process the path tokens and include the application entry file
   * @method _finalizeSetup
   */
  private function _finalizeSetup()
  {
    /** Define the app root for this system */
    define("APP_ROOT", FRAMEWORK_APPLICATION.\Framework\_FixPath($this->setting('directory')).DSEP );
    /** Define the configuration directory based on the mode we are in */
    define("CONFIGURATION", CONFIGURATION_ROOT.(($this->isDevelopment())?'Development':'Live').DSEP );
    /** Process any path tokens that may exist */
    $this->_processPathTokens();
    /** Define the location for the system specific modals */
    define("APP_MODAL", \Framework\_FixPath( $this->location('modal')));
    /** Define the location for the system specific controllers */
    define("APP_CONTROLLER", \Framework\_FixPath( $this->location('controller')));
    /** Define the location for the system specific cron jobs */
    define("APP_CRONTAB", \Framework\_FixPath( $this->location('crontab')));
    /** Include the system Entry Point */
    \Framework\_IncludeCorrect(FRAMEWORK_APPLICATION.$this->setting('directory').DSEP.$this->setting('entry').".php");
  }

  /**
   * Development check
   * @method isDevelopment
   * @return bool
   */
  public static function isDevelopment()
  { return ((self::getInstance()->setting('mode')==self::fModeDevelopment)?true:false); }

  /**
   * Get the navigation array
   * @method navigation
   * @return array
   */
  public function navigation()
  { return $this->_dNavigation; }

  /**
   * Handles getting and setting collection values for all sections
   * @method _getSetSection
   * @param $section
   * @param $key
   * @param $value
   * @return mixed
   */
  private function _getSetSection($section, $key, $value)
  {
    /** Make sure we have a collection */
    if(!isset($this->_dCollection[$section])||is_null($this->_dCollection[$section])){
      /** Something is very wrong just return false */
      return false;
    }
    /** Check if a value was provided */
    if(!is_null($value)){
      /** A value was given set the key to the value */
      $this->_dCollection[$section]->set($key, $value);
      /** return the new value */
      return $this->_dCollection[$section]->get($key);
    }
    /** Return the current value */
    return $this->_dCollection[$section]->get($key);
  }

  /**
   * Return or set a setting value
   * @method setting
   * @param      $key
   * @param null $value
   * @return bool
   */
  public function setting($key, $value = null)
  { return $this->_getSetSection(self::cTypeSetting, $key, $value); }

  /**
   * Return or set a location value
   * @method location
   * @param      $key
   * @param null $value
   * @return mixed
   */
  public function location($key, $value = null)
  { return $this->_getSetSection(self::cTypeLocation, $key, $value); }

  /**
   * Get a web asset paramter
   * @method  webasset
   * @param   string        $key
   * @return  mixed
   */
  public function webasset($key, $inject = null)
  {
    /** Make sure we have a collection */
    if(is_null($this->_dCollection[self::cTypeWebasset])){
      /** Something is very wrong just return false */
      return false;
    }
    /** Check if the remote option has been set */
    if($this->_dCollection[self::cTypeWebasset]->get("{$key}-remote")){
      /** @var string $directory */
      $directory = $this->_dCollection[self::cTypeWebasset]->get("{$key}-directory");
      /** @var string $location */
      $location = $this->_dCollection[self::cTypeWebasset]->get("{$key}-location");
      /** Check if the inject option was passed */
      if(!is_null($inject)){
        /** Inject was passed in push the injected valued into the center of the url */
        return $this->_makeUrl($location.$inject."/".$directory);
      }
      /** Return the url */
      return $this->_makeUrl($location.$directory);
    }

    /** return the path to this webasset */
    return "Application/".$this->setting("directory")."/".$this->_dCollection[self::cTypeWebasset]->get("{$key}-directory");
  }

  /**
   * Convert a directory string to a usable url
   * @method _makeUrl
   * @param $path
   * @return mixed
   */
  private function _makeUrl($path)
  { return str_replace("\\", "/", $path ); }

  /**
   * Return or set a event value
   * @method event
   * @param      $key
   * @param null $value
   * @return mixed
   */
  public function event($key, $value = null)
  { return $this->_getSetSection(self::cTypeEvent, $key, $value); }

  /**
   * Check if patron settings were in the configuration for this App
   * @method hasPatron
   * @return boolean
   */
  public function hasPatron()
  { return ((isset($this->_dCollection[self::cTypePatron]))?true:false); }

  /**
   * Return or set a patron value
   * @method patron
   * @param      $key
   * @param null $value
   * @return mixed
   */
  public function patron($key, $value = null)
  { return $this->_getSetSection(self::cTypePatron, $key, $value); }

  /**
   * Return the server name we are currently operating from
   * @method _serverName
   * @param bool $serverName
   * @return bool
   */
  private function _serverName( $serverName = false )
  {
    /** Check if we were given a server name */
    if($serverName === false){
      /** Get the server name and return it */
      return $_SERVER['SERVER_NAME'];
    }
    /** return the provided name */
    return $serverName;
  }

  /**
   * Process all of the tokens in the collections
   * @method _processPathTokens
   */
  private function _processPathTokens()
  {
    /** @var array $tokens Possible tokens that can be used in the collections */
    $tokens = [
      "%APP%" => APP_ROOT,
      "%APPR%" => FRAMEWORK_APPLICATION,
      "%FRW%" => FRAMEWORK_ROOT,
      "%CFG%" => CONFIGURATION
    ];
    /**
     * @var  $key The key for the section we are currently looping through
     * @var  $section The section array we are currently processing
     */
    foreach($this->_dCollection as $key => $section){
      /** Use the collection map method to iterate over the values */
      $this->_dCollection[$key] = $section->map(
        /** Bind our closeure to the $tokens array so we can use it */
        function($value) use (&$tokens){
          /** Map the array into another close so we don't need to return bind it */
          array_map(
            /** Pass the token and value into the closeure */
            function($tok, $val) use (&$value){
              /** @var string $value since we bound ourselves to the value variable any changes here effect the root object */
              $value = str_replace($tok, $val, $value);
            },
            /** Map the array keys */
            array_keys($tokens),
            /** Pass in the corresponding values */
            $tokens
          );
          /** Return the new value */
         return $value;
        }
      );
    }
  }
}
