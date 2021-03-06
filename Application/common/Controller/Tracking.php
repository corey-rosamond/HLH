<?php
/**
 * Tracking
 *
 * @package Framework\Commander
 * @version 1.0.0
 */
namespace Framework\Commander;

/**
 * 	Tracking
 *
 *	This interface is in charge of all tracking related tasks
 */
class Tracking extends \Framework\Support\Abstracts\Singleton
{
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                         CONTROLLERS                                                 */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private $_cFunnel;


  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                            MODALS                                                   */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private $_mTracking;

  private $_mFunnels;


  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                          DATA OBJECTS                                               */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private $_dSession;

  private $_dFunnel;

  private $_dCampaign;

  private $_dVisit;

  private $_dViews;

  /**
   * The intial public construct is is only an entry point it calls the
   * private constructor and wraps it in the try catch block with the router
   * @method  construct
   * @param   int    $orderKey  Int order key or false
   * @return  Order             An instances of the order controller
   * @access  public
   */
  public static function construct( $key = false )
  {
    try {
      $instance = self::getInstance();
      return $instance->_construct( $key );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      $instance->_eRouter( $exception );
    }
  }

  /**
   * Sub Constructor does all the real constructor work
   * @method  _construct
   * @param   int         orderKey  The order key of the order we want to load the data for
   * @return  Order                 The instance of the order controller
   * @access  private
   */
  private function _construct( $key ){
    try {
      /** MODALS */
      $this->_mTracking = \Framework\Core\Modulus::get( 'Tracking', "Holylandhealth" );
      $this->_mFunnels = \Framework\Core\Modulus::get( 'Funnels', "Holylandhealth" );
      /** CONTROLLERS */
      $this->_cFunnel   = \Framework\Core\Receptionist::controller( 'Funnel', false );
      if( !$key ){
        return $this;
      }
      $this->load( $key );
      return $this;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      $this->_eThrow( self::_emConstructor, self::_eeFatal, $exception );
    }
  }
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                             LOADERS                                                 */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Start the loading process for tracking data
   * @method  load
   * @param   int     $key    Session key to load
   * @return  void
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emload
   * @access  public
   */
  public function load( $key )
  {
    $this->_loadSession( $key );
    $this->_loadFunnel();
    $this->_loadCampaign();
    $this->_loadVisit();
    $this->_loadView();
  }

  /**
   * Start the loading the session data into the _dSession datamember
   * @method  _loadSession
   * @param   int     $key    Session key to load
   * @return  void
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emLoadSession
   * @access  private
   */
  private function _loadSession( $key )
  {
    try {
      $this->_dSession = $this->_mTracking->session( $key );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emLoadSession, self::_eeFatal, $exception );
    }
  }

  /**
   * Load the funnel data into the _dFunnel datamember
   * @method  _loadFunnel
   * @return  void
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emLoadFunnel
   * @access  private
   */
  private function _loadFunnel()
  {
    try {
      $this->_dFunnel = $this->_mFunnels->data( $this->_dSession['funnel-key'] );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emLoadFunnel, self::_eeFatal, $exception );
    }
  }

  /**
   * Load the campaign data into the _dCampaign datamember
   * @method  _loadCampaign
   * @return  void
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emLoadCampaign
   * @access  private
   */
  private function _loadCampaign()
  {
    try {
      $this->_dCampaign = $this->_mTracking->link( $this->_dSession['campaign-key'] );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emLoadCampaign, self::_eeFatal, $exception );
    }
  }

  /**
   * Load the session visit data into the _dVisit datamember
   * @method  _loadVisit
   * @return  void
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emLoadVisit
   * @access  private
   */
  private function _loadVisit()
  {
    try {
      $this->_dVisit = [];
      $this->_dVisit['ip-address'] = $this->_dSession['ip-address'];
      $this->_dVisit['site-visited'] = "<a target=\"_blank\" href=\"{$this->_dFunnel['address']}\"}>{$this->_dFunnel['name']}</a>";
      $this->_dVisit['link'] = $this->_trackingLink();
      //$this->_dVisit['affiliate'] = $this->_affiliate();
      return $this->_dVisit;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emLoadVisit, self::_eeFatal, $exception );
    }
  }

  /**
   * Load the session view data into the _dViews datamember
   * @method  _loadView
   * @return  void
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emLoadView
   * @access  private
   */
  private function _loadView()
  {
    try {
      $temp         = $this->_mTracking->viewsForSession( $this->_dSession['key'] );
      $this->_dViews= [];
      $pStamp       = false;
      $index        = ( sizeof( $temp ) - 1 );
      foreach( $temp as &$row ){
        $row['time-on-page'] = $this->_trackingTimeOnPage( $row['timestamp'], $pStamp );
        $pStamp = $row['timestamp'];
        $row['timestamp'] = date( "l jS \of F Y h:i:s A", strtotime($row['timestamp']) );
        $this->_dViews[$index] = $row;
        $index--;
      }
      return $this->_dViews;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emLoadView, self::_eeFatal, $exception );
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                         COUNT METHODS                                               */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Get and return the total page views for this session key
   * @method  countSessionViews
   * @param   int     $key  Session key to count the page views for
   * @return  int           Total page views for this session key
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emloadVisit
   * @access  public
   */
  public function countSessionViews( $key )
  {
    try {
      return $this->_mTracking->viewCountForSession( $key );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emCountSessionViews, self::_eeFatal, $exception );
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        FORMATTED RETURN                                             */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Returns a formated tracking link message
   * @method  _trackingLink
   * @return  string         A formated representation of the tracking link for the active session
   * @access  private
   */
  private function _trackingLink()
  {
    return "{$this->_dCampaign['name']}<sup>(key: {$this->_dCampaign['key']})</sup>";
  }

  /**
   * Returns a formated representation of the affiliate for the link
   * @method  _affiliate
   * @return  string         A formated representation of the affiliate for this above link
   * @access  private
   */
  private function _affiliate()
  {
    if( $this->_dSession['campaign-key'] ){
      return "No Associated Affiliate";
    }
    return "Support Me!";
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                           BLIND RETURN                                              */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Reutrn a pointer to the active session view array
   * @method  sessionView
   * @return  array           Array that contains the session views nicely formatted or nothing
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emSessionView
   * @access  private
   */
  public function sessionView()
  {
    try {
      return $this->_dViews;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emSessionView, self::_eeFatal, $exception );
    }
  }

  /**
   * Reutrn a pointer to the active visit array
   * @method  sessionVisit
   * @return  array           Array that contains the session visit array
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emSessionVisit
   * @access  public
   */
  public function sessionVisit()
  {
    try {
      return $this->_dVisit;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emSessionVisit, self::_eeFatal, $exception );
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                    CONTROLLER FORMAT METHODS                                        */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Take the previous page view time and the current page view
   * time and determine the amount of minutes they stayed on a single page
   * @method  _trackingTimeOnPage
   * @param   timestamp              $cTime     The time stamp of the current page view
   * @param   timestamp              $pTime     The time stamp of the previous page view
   * @return  string                            Formatted string representation of minutes stayed on page
   * @uses    \Framework\Commander\Tracking->_toMinutes()
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emTrackingTimeOnPage
   * @access  private
   */
  private function _trackingTimeOnPage( $cTime, $pTime )
  {
    try {
      if( !$pTime ){
        return 'Unknown';
      }
      return $this->_toMinutes(strtotime($pTime)-strtotime($cTime));
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emTrackingTimeOnPage, self::_eeFatal, $exception );
    }
  }

  /**
   * Convert the passed value to minutes
   * @method  _toMinutes
   * @param   int         $data   Interger number of seconds
   * @return  string              Formatted conversion to minues with decimal place
   * @uses    \Framework\Commander\Tracking->_eThrow()
   * @throws  \Framework\Exceptional\TrackingControllerException()  CODE - self::_emToMinutes
   * @access  private
   */
  private function _toMinutes( $data )
  {
    try {
      return number_format(( $data / 60 ), 2).' Minutes';
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_eThrow( self::_emToMinutes, self::_eeFatal, $exception );
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       EXCEPTION HANDLING                                            */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Constant for all exception message this will be used the the controller is put into verbose model
   * Verbose mode basicly causes the order object to write a VERY detailed log of everything it does not matter
   * How large or small this should never be used in production
   */
  private $_ePrefix = '[TrackingController]: ';

  /**
   * EXCEPTION MESSAGE CONSTANTS
   * This is a list of all exception messages that can be thrown by the
   * controller and the constants used to throw them
   *    100   : Contructor failure
   * 101 - 120: Loader failure specic to the loader that failed
   * 121 - 140: Bad selector used to access some method or data specic to that method or data object
   * 141 - 160: Undefined index of specic to the type that was undefined
   * 161 - 180: Method Failure faiilure message
   * 181 - 201: Validation Errors
   */
  const _emConstructor                = 100;
  /** LOADER FAILURE */
  const _emLoadSession                = 101;
  const _emloadFunnel                 = 102;
  const _emloadCampaign               = 103;
  const _emloadVisit                  = 104;
  const _emloadView                   = 105;

  /** BAD SELECTOR */

  /** UNDEFINED INDEX */

  /** METHOD FAILURE */
  const _emCountSessionViews          = 162;
  const _emTrackingTimeOnPage         = 163;
  const _emToMinutes                  = 164;
  const _emSessionView                = 165;
  const _emSessionVisit               = 166;

  /** VALIDATION */


  /**
   * EXCEPTION EVENT CODES
   * These are codes that relate to an event to be
   * ran in the case of a specific exception these are
   * when the controller is trying to fix an error or
   * simply failing
   */
  const _eeFatal                      = 200;

  /**
   * Return the exception message for the code that was provided
   * @method  _eMessage
   * @param   integer     $code     Code we want the message for
   * @return  string                The exception message specific to that error code or the default
   * @access  private
   */
  private function _eMessage( $code = 0 )
  {
    switch( $code ){
      /**                                          CONSTRUCTOR                                               */
      case self::_emConstructor:          return "{$this->_ePrefix} Construtor Exception fatal!";        break;
      /**                                        LOADER FAILURE                                              */


      /**                                         BAD SELECTOR                                               */


      /**                                        UNDEFINED INDEX                                             */


      /**                                         METHOD FAILURE                                             */


      /**                                           VALIDATION                                               */


      /**                                         DEFAULT MESSAGE                                            */
      default:                            return "{$this->_ePrefix} Default Exception fatal!";           break;
    }
  }

  /**
   * Route a thrown exception to an event
   * @method  _eRouter
   * @param   \Framework\Exceptional\BaseException  $exception
   * @return  void
   * @access  private
   * @todo    Finish the router for the order Controller
   */
  private function _eRouter( $exception )
  {
    switch( $exception->getCode() ){
      default:
        \Framework\Core\Architect::fallenException(
          $this->_ePrefix, 'Tracking Controller Exception', $exception->getMessage(), $exception->getFile(), $exception->getLine() );
      break;
    }
  }

  /**
   * Rethrow an exception to an exception more specific to the
   * @method  _eThrow
   * @param   string                                $message   The exception message you want to rethrow with
   * @param   int                                   $code      The controller specific error code you want to throw
   *                                                           this exception with this will most likely related to an
   *                                                           exception event. Allowing the controller to recover from
   *                                                           exceptions without ever needing to ask for help
   * @param   \Framework\Exceptional\BaseException  $exception The core exception object that was orinally thrown
   * @return  void
   * @access  private
   */
  private function _eThrow( $message, $code, $exception )
  {
    /** Check if this is an interger */
    if( is_int( $message ) ){
      /** This is an integer that means it is a message for this sytem get it from the message function */
      $message = $this->_eMessage( $message );
    }
    throw new \Framework\Exceptional\TrackingControllerException(
      $message,
      $code,
      (( $exception !== false )? $exception->getCode() : 0),
      (( $exception !== false )? $exception->getFile() : "/Framework/Appliaction/common/Controller/Tracking.php"),
      (( $exception !== false )? $exception->getLine() : 0),
      (( $exception !== false )? $exception : false )
    );
  }

}
