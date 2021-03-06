<?php


namespace Framework\Core;


class Patron extends \Framework\Core\Core
{
  /** Readability */
  const TRUE      = 1;
  const FALSE     = 0;
  const ACTIVE    = 1;
  const INACTIVE  = 0;

  /**
   * Minimum quantity of capital letters Default = 1
   * @var     int       $minimumCapitals
   * @access  private
   */
  private $minimumCapitals = 1;

  /**
   * Minimum quantity of numbers Default = 1
   * @var     int
   * @access  private
   */
  private $minimumNumbers = 1;

  /**
   * Minimum quantity of symbols Default = 1
   * @var     int       $minimumSymbols
   * @access  private
   */
  private $minimumSymbols = 1;

  /**
   * Minimum password length Default = 8
   * @var     int       $minimumLength
   * @access  private
   */
  private $minimumLength = 8;

  /**
   * The framework user moda
   * l
   * @var     null|object $modal
   * @access  private
   */
  private $modal;

  /**
   * The permissions for this App
   * @var     null|array  $permissions
   * @access  private
   */
  private $permissions;

  /**
   * The prefix for all session variables
   * @var     null|str  $prefix
   * @access  private
   */
  private $prefix;

  /**
   * Construct all the basic requirements for the Patron object
   * @method  init
   * @return  Patron  Returns an instance of Patron
   * @access  public
   */
  public static function construct()
  {
    /** @var \Framework\Core\Patron $instance This is an instance of the Patron Object */
    $instance = self::getInstance();
    /** @var \Framework\Core\Contour $Contour This is an instance of the Contour Object */
    $Contour = Contour::getInstance();
    if( $Contour->hasPatron() ){
      /** @var array $permissions Get the collections of persmissions */
      $instance->permissions = $Contour->patron( 'permissions' );
      /** @var string $prefix This is the session prefix we are going to use while storeing data in the session */
      $instance->prefix = $Contour->setting( 'site' );
      /** @var array $passwordReuirements This is a collection of the minimum password requirements */
      $passwordRequirements = $Contour->patron( 'password-requirements' );
      /** @var integer Minimum Capital letters required in a password */
      $instance->minimumCapitals = $passwordRequirements[ 'minimum-capital' ];
      /** @var integer minimum Numbers in a password */
      $instance->minimumNumbers = $passwordRequirements[ 'minimum-numbers' ];
      /** @var integer minimum Symbols in a password */
      $instance->minimumSymbols = $passwordRequirements[ 'minimum-symbols' ];
      /** @var integer minimum Length A password must be */
      $instance->minimumLength = $passwordRequirements[ 'minimum-length' ];
    }
    /** Unset the data we no longer need it */
    unset( $passwordRequirements );
    /** Get Patrons Modal */
    $instance->modal = \Framework\Core\Receptionist::modal("User", true);

    /** Return an instance of the Patron object */
    return $instance;
  }

  /**
   * Get User data of currently logged in user
   * @method  getLoggedInUserSData
   * @return  \PDOResource
   * @uses    Patron->isLoggedIn()
   * @uses    Patron->getUserKey()
   * @uses    \Framework\Core\Modulus\User->getUserData
   * @access  public
   */
  public function getLoggedInUserData()
  {
    /** Make sure there is someone logged in */
    if( !$this->isLoggedIn() ){
      /** No one is logged in fail */
      return false;
    }
    /** Get the user data from the modal and reutn it */
    return $result = $this->modal->getUserData( $this->getUserKey() );
  }

  /**
   * get the user key for the person logged in
   * @method  getUserKey
   * @return  int         The user key for the person currently logged in
   * @access  public
   */
  public function getUserKey()
  {
    return $_SESSION[ $this->prefix ][ 'user' ][ 'key' ];
  }

  /**
   * Check if the user is logged in and meets all of the requirements to do this
   * @method  hasPermission
   * @param   string        $group        The group to check against
   * @param   string        $permission   The permission to check against
   * @return  boolean                     TRUE if the user has meets all requirements
   * @access  public
   */
  public function hasPermission( $group, $permission )
  {
    /** Check if the session data has some users information in it */
    if( !isset( $_SESSION[ $this->prefix ] ) ){
      /** No one is logged in then can not have a permission */
      return false;
    }
    /** Check if the user Data is set one setp farther in */
    if( !isset( $_SESSION[ $this->prefix ]['user'] ) ){
      /** Wasent set something is wrong with the session fail them */
      return false;
    }
    /** If a group was passed */
    if( !$group ){
      /** No group provided they pass */
      return true;
    }
    /** We have a group make sure the user is permitted to use this group */
    if( !$this->checkGroup( $group ) ){
      /**  They dont have the group fail */
      return false;
    }
    /** Check if a permission was passed */
    if( !$permission ){
      /** no permission passed they pass */
      return true;
    }
    /** Check if they have the required premission and group */
    if( !$this->checkPermission( $group, $permission ) ){
      /** Check failed they dont have the required permissions */
      return false;
    }
    /** Passed all checks */
    return true;
  }

  /**
   * Check if the user has the permission and group
   * @method  checkPermission
   * @param   string          $group       The group to check
   * @param   string          $permission  The permission to check
   * @return  boolean                      TRUE if the user has the permission and group
   * @access  public
   */
  public function checkPermission( $group, $permission )
  {
    /** Test the session for a user data set */
    if( !isset( $_SESSION[ $this->prefix ] ) ){
      /** No user data set the fail */
      return false;
    }
    /** Check one layer deeper for the user data set */
    if( !isset( $_SESSION[ $this->prefix ][ 'user' ] ) ){
      /** Layer missing something is wrong with the session fail the permission check */
      return false;
    }
    /** Check if the user data set has the group */
    if( !isset( $_SESSION[ $this->prefix ][ 'user' ][ $group ] ) ){
      /** Group missing they fail */
      return false;
    }
    /** CHeck the user data set for the permission */
    if( !isset( $_SESSION[ $this->prefix ][ 'user' ][ $group ][ $permission ] ) ){
      /** Permission not set so they cant have it */
      return false;
    }
    /** Test the permission to see if they have permission */
    if( !$_SESSION[ $this->prefix ][ 'user' ][ $group ][ $permission ] ){
      /** premission set but false they fail */
      return false;
    }
    /** Passed all checks they are good to go */
    return true;
  }

  /**
   * Check ijf the user has permissions for this group
   * @method  checkGroup
   * @param   string      $group  The group we are checking
   * @return  boolean             TRUE if the user has permissions for this group
   * @access  public
   */
  public function checkGroup( $group )
  {
    return isset( $_SESSION[ $this->prefix ][ 'user' ][ 'permissions' ][ $group ] );
  }

  /**
   * Check if the user is logged in
   * @method  isLoggedIn
   * @return  boolean     TRUE if the user is logged in
   * @access  public
   */
  public function isLoggedIn()
  {
    return isset( $_SESSION[ $this->prefix ][ 'user' ]);
  }

  /**
   * Log an event to the current users history
   * @method  logEvent
   * @param   strin    $message the message we want to log
   * @return  void
   * @access  public
   */
  public function logEvent( $message, $key = false )
  {
    /** Make sure we hvae a user key */
    if( !$key ){
      /** @var User key missing get it */
      $key = $this->getUserKey();
    }
    /** Log the action */
    $this->model->logAction( $key, $message );
  }

  /**
   * Validate all of the user data and determine if they can login
   * @method  login
   * @param   string    Submitted username
   * @param   string    Submitted password
   * @param   boolean   Do we want to save their username to a cookie
   * @return  array     Array of result data [ result => boolean, message => explanation ]
   * @access  public
   */
  public static function login( $username, $password, $remember = false )
  {
    /** @var \Framework\Core\Patron Instance of patron */
    $instance = self::getInstance();
    /** Check if the username is blank */
    if( "" == $username ){
      /** Username return an errop */
      return array( "result" => false, "message" => "Incorrect username!" );
    }
    /** Check that the password is over the minimum length */
    if( strlen( $password ) < $instance->minimumLength ){
      /** Password did not match the minimum length */
      return array( "result" => false, "message" => "Incorrect password!" );
    }
    /** @var \PDOResource $userData The user data for the name requested */
    $userData = $instance->modal->userByName( $username );
    /** Check if we got user data */
    if( !$userData ){
      /** No user data returned return an error message */
      return array( "result" => false, "message" => "Incorrect username!" );
    }
    /** Run the given password against the pbkdf2 algorythm */
    if( self::PBKDF2( $password, $userData['salt'] ) !== $userData['password'] ){
      /** Failed compareison log the action */
      $instance->modal->logAction( $userData['key'], "Login failure with username:$username password:$password incorrect password" );
      /** Reutn an error message  */
      return array("result" => false, "message" => "Incorrect password!");
    }
    /** Make sure the userdata has the user makred as active */
    if( !$userData[ 'active' ] ){
      /** User account is disabled inactive log the action */
      $instance->modal->logAction( $userData['key'], "Login Failure with username:$username password:$password account locked" );
      /** Return an error message */
      return array( "result" => false, "message" => "Account locked!" );
    }
    /** Check if the remeber cookie is set */
    if( $remember ){
      /** Save the username in a cookie */
      setcookie( "username", $username, strtotime( '+30 days' ) );
    }
    /** Set the session data  */
    $instance->setSessionData( $userData );
    /** Update the last Active datetime */
    $instance->modal->setLastActive( $userData['key'] );
    /** Log this action */
    $instance->modal->logAction( $userData['key'], "Logged in" );
    /** Return a sucess message */
    return array( "result" => true, "message" => "Login successful!" );
  }

  /**
   * Sets the user data into session and removes the more sensitive information
   * @method  setSessionData
   * @param   array   $data   The userdata we need to populate into the array
   * @return  void
   * @access  private
   */
  private function setSessionData( $data )
  {
    /** @var array $permissions unserialize the user-permissions so we can use them */
    $permissions = unserialize( $data['user-permissions'] );
    /** Unset the more sensative parts of the userdata to keep it safe */
    unset( $data['user-permissions'] );
    unset( $data['password'] );
    unset( $data['salt'] );
    /** Loop through the user data and push it into the session  */
    foreach( $data as $key => $value ){
      /** push each value into its spot in the session */
      $_SESSION[ $this->prefix ][ 'user' ][ $key ] = $value;
    }
    /** Push the permissions into the session */
    $_SESSION[ $this->prefix ][ 'user' ][ 'permissions' ] = $permissions;
  }

  /**
   * Log the user out and destroy their session
   * @method  logout
   * @return  array     Array of result data [ result => boolean, message => explanation ]
   * @access  public
   * @static
   */
  public static function logout()
  {
    $instance = self::getInstance();
    /** Check if the instance prefix is set to avoid odd errors */
    if( !isset( $_SESSION[ $instance->prefix ] ) ){
      /** Return an error message they dont appear to be logged in */
      array( "result" => false, "message" => "Not logged in!" );
    }
    /** Log the logout action */
    $instance->modal->logAction(
      $_SESSION[ $instance->prefix ][ 'user' ][ 'key' ],
      "Logged Out"
    );
    /** unset the session data */
    unset( $_SESSION );
    /** Destory the session completely */
    session_destroy();
    /** Pass the a succesfull log out message */
    return array( "result" => true, "message" => "Logged out!" );
  }

  /**
   * Check if this is a valid useable username
   * @method  validUsername
   * @param   string  $username   Validate this username
   * @return  boolean             TRUE if the username is valid
   * @access  public
   */
  public function validUsername( $username )
  {
    /** Check if its empty */
    if( '' == $username ){
      /** Its empty that cant be valid */
      return false;
    }
    /** Make sure the username is available */
    return( ( $this->modal->usernameFree( $username ) )? true : false );
  }

  /**
   * Validate that the requestd password meets all the minimum requirements
   * @method  validPassword
   * @param   string $password
   * @return  boolean
   * @access  public
   */
  public static function validPassword( $password )
  {
    $instance = self::getInstance();
    /** Make sure it is at least the minimum length */
    if( strlen( $password ) < $instance->minimumLength ){
      /** Password does not match minimum length */
      return false;
    }
    /** @var integer $numCount Total numbers in the password */
    $numCount = 0;
    /** @var integer $capCount Total Capitols in the password */
    $capCount = 0;
    /** @var integer $symCount $total Symbols in the password */
    $symCount = 0;
    /** Loop through the password counting how many of each requirement they have */
    for( $index = 0; $index < strlen( $password ); $index++ ){
      /** Check if its numeric */
      if( is_numeric( $password[ $index ] ) ){
        /** add one to the number count */
        $numCount++;
      /** Check if i ts uppercase */
      }else if( ctype_upper( $password[ $index ] ) ){
        /** Add one to the capital count */
        $capCount++;
      /** Check if its lower case */
      }else if( !ctype_lower( $password[ $index ] ) ){
        /** None of the above so its a symbol */
        $symCount++;
      }
    }
    /** Check if they matched the minimum Capital letters */
    if( $capCount < $instance->minimumCapitals ){
      /** Failed to reach minimum */
      return false;
    }
    /** Check if they met the minimum number count */
    if( $numCount < $instance->minimumNumbers ){
      /** Did not meet the minimum numbers */
      return false;
    }
    /** Check if they have the minimum nuumber of symbols */
    if( $symCount < $instance->minimumSymbols ){
      /** Did not meet the minimum number of symnbols */
      return false;
    }
    /** Passed all checks */
    return true;
  }

  /**
   * Generate the password hash
   * @method  PBKDF2
   * @param   $password
   * @param   $salt
   * @param   $iter_count
   * @param   $key_length
   * @param   $algorithm
   * @return  string
   * @access  public
   * @static
   */
  public static function PBKDF2( $password, $salt, $iter_count = 2764, $key_length = 32, $algorithm = 'sha256' )
  {
    $hash_length  = strlen( hash( $algorithm, null, true ) );
    $key_blocks   = ceil( $key_length / $hash_length );
    $key          = '';
    for( $block_count  = 1; $block_count <= $key_blocks; $block_count++ ){
      $iterated_block = $block  =  hash_hmac( $algorithm, $salt.pack( 'N', $block_count ), $password, true );
      for( $iterate = 1; $iterate <= $iter_count; $iterate++ ){
        $iterated_block ^= ( $block = hash_hmac( $algorithm, $block, $password, true ) );
      }
      $key .= $iterated_block;
    }
    return substr( $key, 0, $key_length );
  }
}
