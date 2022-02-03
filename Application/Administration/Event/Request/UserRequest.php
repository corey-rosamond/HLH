<?php
/**
 * UserRequest
 *
 * @package App\Event\Main
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * UserRequest
 *
 * This is used for ajax user related functions
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT."Advent".DSEP."Request.php" );
class UserRequest extends \Framework\Advent\Request
{
  
  /**
   * Does this page require that the user be logged in
   * @var     boolean     $requireLogin
   * @access  protected
   * @static
   */
  public static $requiresLogin = false;

  /**
   * Permission group required to view this page or false if none
   * @var     mixed     $permissionGroup  FALSE if none
   * @access  protected
   * @static
   */
  public static $permissionGroup = false;
  
  /**
   * Permission required in the group defined above required to view this page
   * @var     mixed     $permission   FALSE if none
   * @access  protected
   * @static
   */
  public static $permission = false;

  /**
   * The events for this Request object
   * @method events
   * @param  string       $event the event we were asked to run
   * @return void
   * @access protected
   */
  protected function events( $event ){
    /** set result to true since we are here */
    $this->result = true;
    switch( $event ){
      /** will handle forgot passwords */
      case 'forgot':
        $this->result   = false;
        $this->data     = "Not yet Implemented";
      break;
      /** handle attempted logins */
      case "login":
          $outcome = \Framework\Core\Patron::login( 
            $_POST['username'], 
            $_POST['password'], 
            $_POST['remember'] 
          );
          $this->result = $outcome['result'];
          $this->data   = $outcome['message'];
      break;
      /** log the user out */
      case 'logout':
          $outcome      = \Framework\Core\Patron::logout();
          $this->result = $outcome['result'];
          $this->data   = $outcome['message'];
      break;
      /** This is to enable other ajax requests to verify that the user is permitted to do what they requested */
      case 'has-permission':
        if(!\Framework\Core\Patron::isLoggedIn()){
          $this->result = false;
          $this->data   = "Not Logged In!";
          return false;
        }
        if( !\Framework\Core\Patron::hasPermission( 
          $_POST['group'], 
          $_POST['permission'] 
        ) ){
          $this->result = false;
          $this->data   = "Inadequate Permissions!";
          return false;
        }
        $this->result = true;
        $this->data   = "Requirements Meet!";
      break;
      /** if we hit the default it is because a valid event was not specified */
      default:
        $this->result = false;
        $this->data   = "$event Undefined!";
      break;
    }
  }
}
