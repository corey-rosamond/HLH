<?php
/**
 * Event
 *
 * @package Framework\Advent
 * @version 1.0.0
 */
namespace Framework\Advent;

/**
 * Event
 *
 * This is the base for all events run through the advent system
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT . "Advent" . DSEP . "Object" . DSEP . "MainCollection.php" );
abstract class Event extends \Framework\Support\Abstracts\Singleton{
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                          CONSTANTS                                                  */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /** Path file constants */
  const application = 0;  /** Application */
  const framework   = 1;  /** Framework */
  const absolute    = 2;  /** Absolute */

  /** Asset types contants */
  const image   = 0;      /** Images */
  const style   = 1;      /** Style Sheets */
  const script  = 2;      /** Script Files */

  /**
   * This is the main host for the site we need it for the web assets to avoid path issues
   * @var     string      $host
   * @access  protected
   */
  protected $host;

  /**
   * Initalize the event object
   * @method  Initalize
   * @return  \Framework\      An instance of this page object
   * @access  public
   * @static
   */
  public static function initalize(array $paramaters = array() ){
    try{
      /** @var $instance Instances of the page object */
      $instance = self::getInstance();
      /** @var \Framework\Core\Euri  $Euri  Euri is what handles all of our uri issues */
      $Euri = \Framework\Core\Euri::getInstance();
      /** Set the main host to avoid issues with asset includes */
      $instance->host = $Euri->server('host');
      /** Initalize all of the web asset paths */
      $instance->initalizeAssets();
      /** Initalize all the requested modules */
      $instance->initalizeModules();
      /** Setup the buffer */
      $instance->initalizeBuffer();
      if( count( $paramaters ) > 0 ){
        /**  If paramaters were set make sure we save them */
        $instance->paramaters = $paramaters;
      }
    } catch(\Framework\Exceptional\BaseException $exception){
      /**
       * We failed to load the page throw an exception so someone
       * Smarter than us can fix the issue and load a page
       */
      throw new \Framework\Exceptional\AdventFault(
        $instance->exceptionMessage[self::excptError],
        self::eventError,
        self::eventError,
        $exception->getFile(),
        $exception->getLine(),
        $exception
      );
    }
    return $instance;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                    EVENT EXCEPTIONS                                                 */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /** Exception Codes */
  const excptAdventFatal  = 10;
  const excptAccessDenied = 11;
  const excptError        = 12;
  const excptNotFound     = 13;
  /** Event Codes */
  const eventEntry        = 0;
  const eventError        = 1;
  const eventNotFound     = 2;
  const eventPermDenied   = 3;
  const eventLogin        = 4;
  const eventLogout       = 5;
  const adventFailure     = 6;

  /**
   * This is your exception messages array. This way if we need to add or change exception / messsages
   * All we have to do is change it up here and those values get propagated downward to the rest of those
   * module
   * @var     array     $exceptionMessage
   * @access  protected
   */
  protected $exceptionMessage = [
    self::excptAdventFatal  => "[AdventEventManagement]: Has failed in a way we can not recover from!",
    self::excptAccessDenied => "[AdventEventManagement]: Access Denied insufficent permissions!",
    self::excptError        => "[AdventEventManagement]: Event Error encountered event not salvageable!",
    self::excptNotFound     => "[AdventEventManagement]: 404 Event not found!"
  ];

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                    EVENT PERMISSIONS                                                */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Does this page require that the user be logged in
   * @var     boolean     $requireLogin
   * @access  protected
   */
   public static $requiresLogin = false;

  /**
   * Permission group required to view this page or false if none
   * @var     mixed     $permissionGroup  FALSE if none
   * @access  protected
   */
   public static $permissionGroup = false;

  /**
   * Permission required in the group defined above required to view this page
   * @var     mixed     $permission   FALSE if none
   * @access  protected
   */
   public static $permission = false;

  /**
   * This will tell you if a user meets the correct criteria to view this page event
   * @method  userCanView
   * @return  boolean      TRUE if the user meets the requirements to view this page event
   * @uses    Patron::getInstance()
   * @uses    Patron->isLoggedIn()
   * @uses    Patron->checkGroup()
   * @uses    Patron->checkPermission()
   * @throws  \Framework\Exceptional\AdventException()
   * @access  public
   * @static
   */

  public static function userMeetsRequirements(){
    $instance = \Framework\Core\Advent::getInstance();
    /** @var \Framework\Core\Patron An instance of the patron Core Module */
    $Patron = \Framework\Core\Patron::getInstance();
    if( self::$requiresLogin && !$Patron->isLoggedIn() ){
      /** The Event required the user to be logged in but they are not */
      throw new \Framework\Exceptional\AdventException(
        $instance->exceptionMessage[self::excptAccessDenied],
        self::eventLogin,
        self::excptAccessDenied,
        __FILE__,
        __LINE__,
        null
      );
    }
    if( !self::$permissionGroup ){
      /** No premission group required we are done */
      return true;
    }
    if( !$Patron->checkGroup( self::$permissionGroup ) ){
      /** The event required a permission group and the user does not have it */
      throw new \Framework\Exceptional\AdventException(
        $instance->exceptionMessage[self::excptAccessDenied],
        self::eventPermDenied,
        self::excptAccessDenied,
        __FILE__,
        __LINE__,
        null
      );
    }
    if( !self::$permission ){
      /** No permission required we are done */
      return true;
    }
    if( !$Patron->checkPermission( self::$permission ) ){
      /** The user does not have the required permission */
      throw new \Framework\Exceptional\AdventException(
        $instance->exceptionMessage[self::excptAccessDenied],
        self::eventPermDenied,
        self::excptAccessDenied,
        __FILE__,
        __LINE__,
        null
      );
    }
    /** The user meets the requirements */
    return true;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        ASSET METHODS                                                */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * This is an array struct of aset locations
   * @var     [][][]    Array of sub arrays that contain all the paths
   * @access  protected
   */
  protected $location  = [
    self::application =>  [ self::image => [], self::style => [], self::script => [] ],
    self::framework =>    [ self::image => [], self::style => [], self::script => [] ]
  ];

  /**
   * Setup the asset location array
   * @method  initalizeAssets
   * @return  Setup all the locations for the assets
   * @uses    Contour->webasset()
   * @access  private
   */
  protected function initalizeAssets(){
    $Contour = \Framework\Core\Contour::getInstance();
    $this->location[ self::application ][ self::image ] = $Contour->webasset( 'images' );
    $this->location[ self::application ][ self::style ] = $Contour->webasset( 'styles' );
    $this->location[ self::application ][ self::script ] = $Contour->webasset( 'scripts' );
    $this->location[ self::framework ][ self::image ] = "WebAssets/Images/";
    $this->location[ self::framework ][ self::style ] = "WebAssets/Styles/";
    $this->location[ self::framework ][ self::script ] = "WebAssets/Scripts/";
  }

  /**
   * Get the asset path with the system and type constants
   * @method  assetPath
   * @param   integar    $system
   * @param   integar    $type
   * @access  private
   */
  protected function assetPath( $system, $type  ){
    if( $system === 2 ){
      return "";
    }
    return '//'.$this->host.'/'.$this->location[ $system ][ $type ];
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                            REQUESTED MODALS / CONTROLLERS / TEMPLATES                               */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * These are the modals this page will use. Nothing needs to be done
   * Beyond this to get them the framework will just do it for you
   * @var     array       $modal
   * @access  protected
   */
  protected $modal = [];

  /**
   * Controllers are more function intensive they are in charge of controlling
   * groups of methods that logicly belong together
   * @var     array      $controller
   * @access  protected
   */
  protected $controller = [];

  /**
   * Templates are a snipet oh html that we break into parts then
   * we transpose the data on top of the html and give it back
   * @var     array       $modal
   * @access  protected
   */
  protected $template = [];

  /**
   * Initalize all the needed modules
   * @method  initalizeModules
   * @return  void
   * @access  protected
   */
  protected function initalizeModules(){
    if( count( $this->modal ) > 0) {
      /** Setup the requested Modals for this event */
      $this->setupModal();
    }
    if( count( $this->template ) > 0 ){
      /** Setup the requested Templates for this event */
      $this->setupTemplate();
    }
    if( count( $this->controller ) > 0 ){
      /** Setup the requested Controllers for the event */
      $this->setupController();
    }
  }

  /**
   * Setup requested modals in the modal collection for this event
   * @method  setupModal
   * @return  void
   * @uses    \Framework\Core\Receptionist::modal()
   * @access  protected
   */
  protected function setupModal(){
    /** Use array map to iterate over the Modal array */
    array_map(
      function( $arguments ){
        /** check how many dots */
        $count = substr_count( $arguments, "." );
        switch( $count ){
          case 1:
            $isFramework = false;
            list( $object, $selector ) = explode(".", $arguments );
          break;
          case 2:
            list( $object, $selector, $isFramework ) = explode(".", $arguments );
            $isFramework = (($isFramework=='true')?true:false);
          break;
        }
        /** Use the object and slector to get the requested modal and add it to the array */
        $this->modal[ $arguments ] = \Framework\Core\Receptionist::modal( $object, $isFramework );
    }, array_keys( $this->modal ) );
  }

  /**
   * Setup the requested template objects in the template collection for the event
   * @modal   setupTemplate
   * @return  void
   * @access  protected
   * @todo    Implement the template system
   */
  protected function setupTemplate(){


  }

  /**
   * Setup requested controllers in the controller collection for this page event
   * @method  setupController
   * @return  void
   * @uses    \Framework\Core\Receptionist::controller()
   * @access  protected
   */
  protected function setupController()
  {
    /** Map out the array keys and loop through them */
    array_map(
      function( $arguments ){
        /** Split the argument passed into object and selector */
        list( $object, $selector ) = explode(".", $arguments );
        /** Use the selector to determine if its a framework controller */
        $isFramework = (( $selector !== "Framework" )? true : false );
        /** Add the Controller to the array  */
        $this->controller[ $arguments ] = \Framework\Core\Receptionist::controller( $object, $isFramework );
      }, array_keys( $this->controller )
    );
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                     BUFFER RELATED METHODS                                          */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * This is the size of the write buffer
   * @var     string    $buffer
   * @access  protected
   */
  protected $chunkSize;

  /**
   * This is the write buffer this will hold the page during the render process
   * @var     string  $buffer
   * @access  protected
   */
  protected $buffer;

  /**
   * Initalize the write buffer
   * @method initalizeBuffer
   * @return void
   * @access protected
   */
  protected function initalizeBuffer(){
    $this->clear();
    $this->chunkSize = \Framework\Core\Contour::getInstance()->setting("chunk-size");
  }

  /**
   * Cut the output into chunks and output each chunk this keeps apache from haveing to
   * pole through slowly and greatly increases render speed
   * @method  chunkRender
   * @return  void
   * @access  protected
   */
  protected function chunkRender(){
    array_map(
      function($chunk){
        echo $chunk;
      }, str_split( $this->buffer, $this->chunkSize )
    );
  }

  /**
   * Write to the page buffer
   * @method  write
   * @param   string  $content  Content to add to the page buffer
   * @return  void
   * @access  protected
   */
  public function write($content){
    $this->buffer .= $content;
  }

  /**
   * Get the buffer this is mainly for the token system
   * @method  getBuffer
   * @return  string
   * @access  public
   */
  public function getBuffer(){
    return $this->buffer;
  }

  /**
   * Set the buffer to the new value this is mainly for the token system
   * @method  setbuffer
   * @param   string      $buffer
   * @return  void
   * @access  public
   */
  public function setbuffer($buffer){
    $this->buffer = $buffer;
  }

  /**
   * Clear the page buffer
   * @method  clear
   * @return  void
   * @access  protected
   */
  protected function clear(){
    unset($this->buffer);
    $this->buffer = "";
  }
}
