<?php
/**
 * Architect
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Core;

/**
 * Architect
 *
 * Architect is the framework traffic controller coordinator baby sitter
 * Librarian and all around Director of operations. When another object of any type
 * fails architect deals with it
 */
class Architect extends Core
{
  /** @var Euri _coreEuri */
  public $_coreEuri;
  /** @var Contour _coreContour */
  public $_coreContour;
  /** @var Receptionist _coreReceptionist */
  public $_coreReceptionist;
  /** @var Admit _coreAdmit */
  public $_coreAdmit;
  /** @var Advent _coreAdvent */
  public $_coreAdvent;
  /** @var Proteus _coreProteus */
  public $_coreProteus;
  /** @var Patron _corePatron */
  public $_corePatron;

  /**
   * Setup architect with all basic needs
   * @method construct
   * @return mixed
   */
  public function construct()
  {
    /** @var Architect $instance */
    $instance = self::getInstance();
    try{
      /** @var Euri _coreEuri */
      $instance->_coreEuri = Euri::getInstance();
      /** @var Contour _coreContour */
      $instance->_coreContour = Contour::getInstance();
      /** @var Receptionist _coreReceptionist */
      $instance->_coreReceptionist = Receptionist::getInstance();
      /** @var Admit _coreAdmit */
      $instance->_coreAdmit = Admit::getInstance();
      /** @var Advent _coreAdvent */
      $instance->_coreAdvent = Advent::getInstance();
      /** @var Proteus _coreProteus */
      $instance->_coreProteus = Proteus::getInstance();
      /** Check if we need Patron */
      if($instance->_coreContour->hasPatron()){
        /** @var Patron _corePatron */
        $instance->_corePatron = Patron::getInstance();
      }
      /** Return the configured instance of Architect */
      return $instance;
    } catch( \Framework\Exceptional\BaseException $exception){
      /** Something went wrong here we can not can not recover pass control to framework failure */
      $instance->_frameworkFailure($exception);
    }
  }

  /**
   * Run the application
   * @method run
   */
  public function run()
  {
    try{
      /** Call the application entrance */
      call_user_func(array("App\\{$this->_coreContour->setting('entry')}", 'initialize'));
    } catch(\Framework\Exceptional\StandardFault $exception){
      $this->errorPage($exception);
    } catch(\Framework\Exceptional\FrameworkException $exception){
      $this->errorPage($exception);
    } catch(\PDOException $exception){
      $this->errorPage($exception);
    }
  }

  /**
   * The framework not found page
   * @method notFoundPage
   */
  public function notFoundPage()
  {
    echo 'Framework not found!';
    exit();
  }

  /**
   * Framework errorPage
   * @method errorPage
   * @param $exception
   */
  public function errorPage($exception)
  {
    echo '<pre>';
    print_r($exception);
    echo '</pre>';
    /** Log the exception message */
    $this->_coreAdmit->log($exception,$this->_coreEuri->server('host'));
    echo 'Framework Error Page';
    exit();
  }

  /**
   * Framework downForMaintenance page
   * @method downForMaintenance()
   */
  public function downForMaintenance()
  {
    echo 'Framework Down For Maintenance';
    exit();
  }

  /**
   * Framework Failure page
   * @method _frameworkFailure
   * @param $exception
   */
  private function _frameworkFailure($exception)
  {
    echo '<pre>';
    print_r($exception);
    echo '</pre>';
    /** Log the exception message */
    $this->_coreAdmit->log($exception,$this->_coreEuri->server('host'));
    echo 'Framework Error 2';
    exit();
  }

  /**
   * Fatal Error Handler
   * @method fatalError
   * @param $exception
   */
  public function fatalError($exception)
  {
    echo '<pre>';
    print_r($exception);
    echo '</pre>';
    /** Log the exception message */
    $this->_coreAdmit->log($exception,$this->_coreEuri->server('host'));
    echo 'Framework Error 3';
    exit();
  }
}
