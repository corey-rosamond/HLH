<?php
/**
 * User
 *
 * @package Framework\Advent
 * @version 1.2.0
 */
namespace Framework\Commander;

/**
 * User
 *
 * This is the main interface for User interactions
 */
class User extends Commander
{
  /****************************************************/
  /*                   CORE OBJECTS                   */
  /****************************************************/
  /** @var \Framework\Core\Patron $_corePatron */
  private $_corePatron;

  /**
   * Construct the main user controller
   * @method construct
   * @return \Framework\Commander\User
   */
  public static function construct()
  {
    /** @var \Framework\Commander\User $instance */
    $instance = self::getInstance();
    /** @var \Framework\Core\Patron _corePatron */
    $instance->_corePatron = \Framework\Core\Patron::getInstance();
    /** Return the instance */
    return $instance;
  }



  /**
   * This is the event interface for this command object
   * This allows javascript to make calls to this object
   * to preform pre-defined tasks
   * @method event
   */
  public function event()
  {
    /** Configure the event */
    $this->_eConfigure();
    /** Find the requested task */
    switch($this->_dEventAction){
      /** Try and log the user in */
      case 'login':
        $outcome = $this->_corePatron->login($this->_dEventData['username'], $this->_dEventData['password'], $this->_dEventData['remember']);
        $this->_eResponse($outcome['result'], $outcome['message']);
        break;

      /** Log the user out */
      case 'logout':
        $outcome = $this->_corePatron->logout();
        $this->_eResponse($outcome['result'], $outcome['message']);
        break;

      /** Run the forgot user event */
      case 'forgot':
        $this->_eResponse(false, "Not yet Implemented");
        break;

      /** Verify that the user has the permissions */
      case 'has-permission':
        if(!$this->_corePatron->isLoggedIn()){
          $this->_eResponse(false, "Not Logged In!");
        }
        if(!$this->_corePatron->hasPermission($this->_dEventData['group'], $this->_dEventData['permission'])){
          $this->_eResponse(false, "Inadequate Permissions!");
        }
        $this->_eResponse(true, "Requirements Meet!");
        break;
      default: $this->_eResponse(false, 'Invalid'); break;
    }
  }
}
