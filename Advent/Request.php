<?php
/**
 * Request
 *
 * @package Framework\Advent
 * @version 1.0.0
 */
namespace Framework\Advent;

/**
 * Request
 *
 * This is the main request event it will enable all other request events to complete their tasks
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT . "Advent" . DSEP . "Object" . DSEP . "MainCollection.php" );
\Framework\_IncludeCorrect( FRAMEWORK_ROOT . "Advent" . DSEP . "Event.php" );
abstract class Request extends \Framework\Advent\Event
{

  /**
   * These are the modals this page will use. Nothing needs to be done
   * Beyond this to get them the framework will just do it for you
   * @var     array       $modal
   * @access  protected
   */
  protected $modal = [];

  /**
   * Templates are a snipet oh html that we break into parts then
   * we transpose the data on top of the html and give it back
   * @var     array       $modal
   * @access  protected
   */
  protected $template = [];

  /**
   * Controllers are more function intensive they are in charge of controlling
   * groups of methods that logicly belong together
   * @var     array      $controller
   * @access  protected
   */
  protected $controller = [];

  /**
   * Are the requirements for this request object met
   * @var boolean $requirementsMet
   * @access protected
   */
  protected $requirementsMet = false;

  /**
   * Status of the request true = success
   * @var boolean $status
   * @access protected
   */
  protected $result = false;

  /**
   * The data to be returned from the request
   * @var string $data
   * @access protected
   */
  protected $data = 'Insufficient data provided!';

  /**
   * Initalize the request event
   * @param   array  $paramaters  Optional paramters
   * @return  void                An instance of this request object
   */
  public static function initalize( array $paramaters = array() ){
    try{
      /** @var /Framework/Advent/Request Instance of the request object */
      $instance = self::getInstance();
      /** @var /Framework/Advent/Event $instance This is an instance of the base event */
      parent::initalize( $paramaters );
      /** Return an instance of the Request object */
      return $instance;
    } catch ( \Framework\Exceptional\AdventException $exception ){
      /** Pass the advent exceptions to their handler */
      $this->handleException( $exception ) ;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      /** Request Error Exception */
      throw new \Framework\Exceptional\AdventException(
        $this->messages[ self::exceptionError ]." : {$exception->getMessage()}",
        self::exceptionError,
        self::exceptionError,
        $exception->getFile(),
        $exception->getLine(),
        $exception
      );
    }
  }

  /**
   * Run the request event
   * @method  run
   * @return  void
   * @uses    \Framework\Advent\Request->requirements()
   * @uses    \Framework\Advent\Request->handleException()
   * @uses    \Framework\Exceptional\BaseException->getMessage()
   * @uses    \Framework\Exceptional\BaseException->getCode()
   * @uses    \Framework\Exceptional\BaseException->getSeverity()
   * @uses    \Framework\Exceptional\BaseException->getFile()
   * @uses    \Framework\Exceptional\BaseException->getLine()
   * @access  public
   */
  public function run(){
    try {
      /** First make sure they have all the required information */
      $this->requirementsMet = $this->requirements();
      /** Make sure it passed */
      if( !$this->requirementsMet ){
        /** Something failed */
        throw new \Framework\Exceptional\AdventException(
          $this->messages[ self::exceptionError],
          self::exceptionError,
          self::exceptionError,
          __FILE__,
          __LINE__,
          NULL
        );
        return false;
      }
      /** Verify the user related checks */
      $this->requirementsMet = $this->patronChecks( self::$requiresLogin, self::$permissionGroup, self::$permission );
      /** Make sure it passed */
      if( !$this->requirementsMet ){
        /** Something failed */
        throw new \Framework\Exceptional\AdventException(
          $this->messages[ self::exceptionError],
          self::exceptionError,
          self::exceptionError,
          __FILE__,
          __LINE__,
          NULL
        );
        return false;
      }
      if( !isset($_POST['action'])){
        return $this->events();
      }
      /** Run the events action */
      $this->events( $_POST['action'] );
      /** Output the results */
      $this->output();
    } catch ( \Framework\Exceptional\AdventException $exception ){
      /** Pass the advent exceptions to their handler */
      $this->handleException( $exception ) ;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      /** Request Error Exception */
      throw new \Framework\Exceptional\AdventException(
        $this->messages[ self::exceptionError ]." : {$exception->getMessage()}",
        self::exceptionError,
        self::exceptionError,
        $exception->getFile(),
        $exception->getLine(),
        $exception
      );
    }
  }

  /**
   * Run all of the patron related checks
   * @method  patronChecks
   * @param   mixed       $login
   * @param   mixed       $group
   * @param   mixed       $permission
   * @return  boolean
   * @uses    \Framework\Advent\Request->patronLoginCheck()
   * @uses    \Framework\Advent\Request->patronGroupCheck()
   * @uses    \Framework\Advent\Request->patronPermissionCheck()
   * @uses    \Framework\Exceptional\BaseException->getFile()
   * @uses    \Framework\Exceptional\BaseException->getLine()
   * @throws  \Framework\Exceptional\AdventException()
   * @access  protected
   */
  protected function patronChecks( $login, $group, $permission ){
    try {
      /** Check if login is required and satisfyed  */
      $loginCheck = $this->patronLoginCheck( $login );
      /** Check a group is required and satisfyed  */
      $groupCheck = $this->patronGroupCheck( $group );
      /** Check a permission is required and satisfyed  */
      $permissionCheck = $this->patronPermissionCheck( $permission );
      /** If we havent thrown an exception check that all checks passed */
      if( $loginCheck &&  $groupCheck && $permissionCheck ){
        return true;
      }
    } catch ( \Framework\Exceptional\AdventException $exception ){
      /** Request Error Exception */
      throw $exception;
    }
    return true;
  }

  /**
   * Runs a check to see if login is required and if it is if they are logged in
   * @method  patronLoginCheck
   * @param   mixed       $login
   * @return  boolean
   * @uses    \Framework\Core\Patron::getInstance();
   * @uses    \Framework\Core\Patron::patronLoginCheck();
   * @uses    \Framework\Exceptional\BaseException->getFile()
   * @uses    \Framework\Exceptional\BaseException->getLine()
   * @throws  \Framework\Exceptional\AdventException()
   * @access  protected
   */
  protected function patronLoginCheck( $login ){
    if( !$login ){
      /** No premission group required we are done */
      return true;
    }
    /** @var \Framework\Core\Patron An instance of the patron Core Module */
    $Patron = \Framework\Core\Patron::getInstance();
    if( $login && !$Patron->isLoggedIn() ){
      /** The Event required the user to be logged in but they are not */
      throw new \Framework\Exceptional\AdventException(
        $this->messages[ self::exceptionLoginCheckFail ],
        self::exceptionLoginCheckFail,
        self::exceptionLoginCheckFail,
        __FILE__,
        __LINE__,
        null
      );
    }
  }

  /**
   * Runs a check to see if login is required and if it is if they are logged in
   * @method  patronGroupCheck
   * @param   mixed       $group
   * @return  boolean
   * @uses    \Framework\Core\Patron::getInstance();
   * @uses    \Framework\Core\Patron::checkGroup();
   * @uses    \Framework\Exceptional\BaseException->getFile()
   * @uses    \Framework\Exceptional\BaseException->getLine()
   * @throws  \Framework\Exceptional\AdventException()
   * @access  protected
   */
  protected function patronGroupCheck( $group ){
    if( !$group ){
      /** No premission group required we are done */
      return true;
    }
    /** @var \Framework\Core\Patron An instance of the patron Core Module */
    $Patron = \Framework\Core\Patron::getInstance();
    /** Check if they have the required group */
    if( !$Patron->checkGroup( $group ) ){
      /** The event required a permission group and the user does not have it */
      throw new \Framework\Exceptional\AdventException(
        $this->messages[ self::exceptionGroupCheckFail ],
        self::exceptionGroupCheckFail,
        self::exceptionGroupCheckFail,
        __FILE__,
        __LINE__,
        null
      );
    }
  }

  /**
   * Runs a check to see if login is required and if it is if they are logged in
   * @method  patronPermissionCheck
   * @param   mixed       $permission
   * @return  boolean
   * @uses    \Framework\Core\Patron::getInstance();
   * @uses    \Framework\Core\Patron::checkPermission();
   * @uses    \Framework\Exceptional\BaseException->getFile()
   * @uses    \Framework\Exceptional\BaseException->getLine()
   * @throws  \Framework\Exceptional\AdventException()
   * @access  protected
   */
  protected function patronPermissionCheck( $permission ){
    if( !$permission ){
      /** No permission required we are done */
      return true;
    }
    if( !$Patron->checkPermission( $permission ) ){
      /** The user does not have the required permission */
      throw new \Framework\Exceptional\AdventException(
        $this->messages[ self::exceptionPermissionCheckFail ],
        self::exceptionPermissionCheckFail,
        self::exceptionPermissionCheckFail,
        __FILE__,
        __LINE__,
        null
      );
    }
  }

  /**
   * Requirements for this request object
   * @method requirements
   * @return void
   * @uses    \Framework\Exceptional\BaseException->getFile()
   * @uses    \Framework\Exceptional\BaseException->getLine()
   * @throws  \Framework\Exceptional\AdventException()
   * @access protected
   */
  protected function requirements(){
    /** Check for post */
    //if( empty( $_POST ) ){
      /** No post fail the request */
      /** Could not include the event file throw exception */
      /*throw new \Framework\Exceptional\AdventException(
        $this->messages[ self::exceptionPostNotSet ],
        self::exceptionPostNotSet,
        self::exceptionPostNotSet,
        __FILE__,
        __LINE__,
        null
      );
      return false;
    }*/
    /** Check if action is set in post */
    /*if( !isset( $_POST['action'] ) ){
      throw new \Framework\Exceptional\AdventException(
        $this->messages[ self::exceptionActionNotSet ],
        self::exceptionActionNotSet,
        self::exceptionActionNotSet,
        __FILE__,
        __LINE__,
        null
      );
      return false;
    } */
    /** All went well */
    return true;
  }
  /**
   * Outpout the result of this request
   * @method output
   * @return void
   * @access private
   */
  private function output(){
    echo json_encode( [ "result" => $this->result, "message" => $this->data ] );
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                   REQUEST EXCEPTION HANDLE                                          */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**          EXCEPTION CONSTANTS         */
  const exceptionError                  = 15;
  const exceptionPostNotSet             = 16;
  const exceptionActionNotSet           = 17;
  const exceptionUndefinedAction        = 18;
  const exceptionPermissionDenied       = 19;
  const exceptionLoginCheckFail         = 20;
  const exceptionGroupCheckFail         = 21;
  const exceptionPermissionCheckFail    = 22;

  /**
   * These are the request exception messages
   * @var     array       $messages
   * @access  protected
   */
  protected $messages = [
    self::exceptionError                  => "[AdventRequest-Exception]: Error",
    self::exceptionPostNotSet             => "[AdventRequest-Exception]: Post Not Set",
    self::exceptionActionNotSet           => "[AdventRequest-Exception]: Action Not Set",
    self::exceptionUndefinedAction        => "[AdventRequest-Exception]: Undefined Action",
    self::exceptionPermissionDenied       => "[AdventRequest-Exception]: Premission Denied",
    self::exceptionLoginCheckFail         => "[AdventRequest-Exception]: Failed Login Requirement",
    self::exceptionGroupCheckFail         => "[AdventRequest-Exception]: Failed Group Requirement",
    self::exceptionPermissionCheckFail    => "[AdventRequest-Exception]: Failed Permission Requirement"
  ];

  /**
   * Handle request based exceptions
   * @method  handleException
   * @param   \Framework\Exceptional\AdventException $exception
   * @return  void
   * @access  private
   */
  private function handleException( $exception ){
    /** @var boolean $status If we are here we failed so set it to failure */
    $this->status = false;
    switch( $exception->getCode() ){
      case self::exceptionError:
        /** Set the error Exception Message */
        $this->data = $exception->getMessage();
      break;
      case self::exceptionPostNotSet:
        /** Set the Post Not Set Exception Message */
        $this->data = $exception->getMessage();
      break;
      case self::exceptionActionNotSet:
        /** Set the Action Not Set Exception Message */
        $this->data = $exception->getMessage();
      break;
      case self::exceptionUndefinedAction:
        /** Set the Undefined Action Exception Message */
        $this->data = $exception->getMessage();
      break;
      case self::exceptionPermissionDenied:
      case self::exceptionLoginCheckFail:
      case self::exceptionGroupCheckFail:
      case self::exceptionPermissionCheckFail:
        /** Set the error Permission Deined Exception Message */
        $this->data = $this->messages[ self::exceptionPermissionDenied ];
      break;
    }
    /** Output the exception */
    $this->output();
    /** Log the Exception we are done */
    $exception->log();
    /** Return false */
    return false;
  }
}
