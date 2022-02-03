<?php
/**
 * Receptionist
 *
 * @package Framework\Docket
 * @version 1.2.0
 */
namespace Framework\Core;

/**
 * Receptionist
 *
 * This is in change of loading different types of objects getting them configured
 * and returning them to you for use
 * INFORMATION
 *
 * Object Types
 * oTypeController    = 0
 * oTypeCronjob       = 1
 * oTypeModal         = 3
 * oTypeDatatable     = 4
 */
class Receptionist extends Core
{
  /****************************************************/
  /*                RECEPTIONIST TYPES                */
  /****************************************************/
  /** @const integer oTypeController */
  const oTypeController = 0;
  /** @const integer oTypeCronjob */
  const oTypeCronjob = 1;
  /** @const integer oTypeModal */
  const oTypeModal = 2;
  /** @const integer oTypeDatatable */
  const oTypeDatatable = 3;
  /****************************************************/
  /*                   DATA MEMBERS                   */
  /****************************************************/
  /** @var  array _dPath collection of file paths */
  private $_dPath;
  /** @var  array _dCache collection of cached objects by system then type */
  private $_dCache;
  /** @var  array _dNamespace collection of namespaces */
  private $_dNamespace;
  /** @var \Framework\Modulus\Query _dDbObject */
  private $_dDbObject;
  /** @var string _dDbDatabase */
  private $_dDbDatabase;

  /**
   * This builds out the basic information receptionist needs to complete the job
   * @method construct
   * @return object
   */
  public static function construct()
  {
    /** @var Receptionist $instance */
    $instance = self::getInstance();
    if(!is_null($instance->_dCache)){
      /** Stop the ability to run construct more than once */
      return $instance;
    }
    /** Include the database config file for this mode */
    \Framework\_IncludeCorrect(CONFIGURATION."Database".DSEP."Holylandhealth.config.php");
    /** @var array _dPath An array of paths for different resources */
    $instance->_dPath = [
      self::rTypeFramework => [
        self::oTypeController => FRAMEWORK_ROOT."Commander".DSEP,
        self::oTypeCronjob => FRAMEWORK_ROOT."Docket".DSEP,
        self::oTypeModal => FRAMEWORK_ROOT."Modulus".DSEP."Modal".DSEP,
        self::oTypeDatatable => FRAMEWORK_ROOT."Modulus".DSEP."Datatable".DSEP,
      ],
      self::rTypeApplication => [
        self::oTypeController => APP_CONTROLLER,
        self::oTypeCronjob => APP_CRONTAB,
        self::oTypeModal => APP_MODAL,
        self::oTypeDatatable => FRAMEWORK_ROOT."Modulus".DSEP."Datatable".DSEP,
      ]
    ];
    /** @var array _dCache Collection of cached objects */
    $instance->_dCache = [
      self::rTypeFramework => [],
      self::rTypeApplication => []
    ];
    /** @var array _dNamespace Collection of framework namespaces */
    $instance->_dNamespace = [
      self::oTypeController => "\\Framework\\Commander\\",
      self::oTypeCronjob => "\\Framework\\Docket\\",
      self::oTypeModal => "\\Framework\\Modulus\\Modal\\",
      self::oTypeDatatable => "\\Framework\\Modulus\\Datatable\\"
    ];
    /** @var \Framework\Modulus\DBConfig _dDbObject */
    $instance->_dDbObject = new \Framework\Modulus\HolylandhealthConfig();
    /** @var string _dDbDatabase */
    $instance->_dDbDatabase = \Framework\Modulus\HolylandhealthConfig::$database;
    /** Return the instance */
    return $instance;
  }

  /**
   * Try and get a cached version of the requested object
   * @method _isCached
   * @param $name
   * @param $type
   * @param $system
   * @return bool
   */
  private function _isCached($name, $type, $system)
  {
    if(isset($this->_dCache[$system]["{$this->_namespace($type)}{$name}"])){
      return $this->_dCache[$system]["{$this->_namespace($type)}{$name}"];
    }
    return false;
  }

  /**
   * Adds an instance of the object to the cache
   * @method _addCache
   * @param $system
   * @param $qualifiedName
   * @param $instance
   * @return mixed
   */
  private function _addCache($system, $qualifiedName, $instance )
  { return $this->_dCache[$system][$qualifiedName] = $instance; }

  /**
   * Return the system key
   * @method _system
   * @param $isFramework
   * @return int
   */
  private function _system($isFramework)
  {
    if(!$isFramework){
      return self::rTypeApplication;
    }
    return self::rTypeFramework;
  }

  /**
   * Returns the namespace string for the specified object type
   * @method _namespace
   * @param   $type
   * @return  mixed
   */
  private function _namespace($type)
  { return $this->_dNamespace[$type]; }

  /**
   * Include the file
   * @method _include
   * @param $system
   * @param $type
   * @param $name
   * @return bool
   */
  private function _include($system, $type, $name)
  {
    try {
      \Framework\_IncludeCorrect("{$this->_dPath[$system][$type]}{$name}.php");
    } catch ( \Framework\Exceptional\IncludeOnceException $exception ){
      return false;
    }
    return true;
  }

  /**
   * Process the request doing all the leg work to get the requested resource
   * @method _process
   * @param $system
   * @param $type
   * @param $name
   * @return bool|mixed
   */
  private function _process( $system, $type, $name )
  {
    $cached = $this->_isCached( $name, $type, $system );
    if($cached === false){
      if(!$this->_include($system, $type, $name)){
        /** @todo Add a thrown exception pointing to where we were called here */
        return false;
      }
      $instance = "{$this->_namespace($type)}{$name}";
      return $this->_addCache($system, $instance, $instance::getInstance());
    }
    return $cached;
  }

  /**
   * Get the and construct the requested controller
   * @method controller
   * @param $name
   * @param bool $isFramework
   * @return bool
   */
  public static function controller( $name, $isFramework = false )
  {
    $instance = self::getInstance();
    $result = $instance->_process( $instance->_system( $isFramework ), self::oTypeController, $name );
    if($result === false){
      return false;
    }
    if(method_exists($result, 'construct')){
      return $result::construct();
    }
    return $result;
  }

  /**
   * Get the and construct the requested cronjob
   * @method cronjob
   * @param $name
   * @param bool $isFramework
   * @return bool
   */
  public static function cronjob( $name, $isFramework = false )
  {
    $instance = self::getInstance();
    $result = $instance->_process( $instance->_system( $isFramework ), self::oTypeCronjob, $name );
    if($result === false){
      return false;
    }
    if(method_exists($result, 'construct')){
      return $result::construct();
    }
    return $result;
  }


  public static function modal( $name, $isFramework = false )
  {
    $instance = self::getInstance();
    $system = $instance->_system($isFramework);
    $cached = $instance->_isCached($name, self::oTypeModal, $system);
    if ($cached === false) {
      if (!$instance->_include($system, self::oTypeModal, $name)) {
        /** @todo Add a thrown exception pointing to where we were called here */
        return false;
      }
      $qualifiedName = "{$instance->_namespace(self::oTypeModal)}{$name}";
      return $instance->_addCache($system, $qualifiedName, new $qualifiedName($instance->_dDbDatabase, $instance->_dDbObject));
    }
    return $cached;
  }

  public static function datatable( $name, $isFramework = true )
  {
    $instance = self::getInstance();
    $system = $instance->_system($isFramework);
    $cached = $instance->_isCached($name, self::oTypeDatatable, $system);
    if ($cached === false) {
      if (!$instance->_include($system, self::oTypeDatatable, $name)) {
        /** @todo Add a thrown exception pointing to where we were called here */
        return false;
      }
      $qualifiedName = "{$instance->_namespace(self::oTypeDatatable)}{$name}";
      return $instance->_addCache($system, $qualifiedName, new $qualifiedName($instance->_dDbDatabase, $instance->_dDbObject));
    }
    return $cached;
  }

  public static function printCacheMembers()
  {
    $instance = self::getInstance();
    foreach( $instance->_dCache[self::rTypeFramework] as $name => $value ){
      echo $name."\n";
    }
    foreach( $instance->_dCache[self::rTypeApplication] as $name => $value ){
      echo $name."\n";
    }
  }

}
