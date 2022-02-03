<?php
/**
 * Tracking
 *
 * @package Framework\Commander
 * @version 1.2.0
 */
namespace Framework\Commander;

/**
 * Tracking
 *
 * This is the main controller for all tracking data
 */
class Tracking extends \Framework\Commander\Commander
{
  /** @var \Framework\Modulus\Modal\TrackingSessions $_mSessions */
  private $_mSessions;
  /** @var \Framework\Modulus\Modal\TrackingSessionsData $_mSessionsData */
  private $_mSessionsData;
  /** @var \Framework\Modulus\Modal\TrackingViews $_mViews */
  private $_mViews;
  /** @var \Framework\Core\Contour $_coreContour */
  private $_coreContour;
  /** @var \Framework\Core\Euri $_coreEuri */
  private $_coreEuri;
  /** @var \Framework\Commander\MediaBuyEmail $_cMediaBuyEmail */
  private $_cMediaBuyEmail;
  /** @var string $_sPrefix */
  protected $_sPrefix;
  /** @var string $_protocol */
  private $_protocol;
  /** @var string $_cPrefix */
  private $_cPrefix = 'tracking-data';
  /** @var int $_funnelKey */
  private $_funnelKey;
  /** @var int $_campaignKey */
  private $_campaignKey;
  /** @var int $_sessionKey */
  private $_sessionKey;

  /**
   * Construct the tracking controller
   * @method construct
   * @return object
   */
  public static function construct()
  {
    /** @var \Framework\Commander\Tracking $instance */
    $instance = self::getInstance();
    /** @var \Framework\Core\Euri $_coreEuri */
    $instance->_coreEuri = \Framework\Core\Euri::getInstance();
    /** @var \Framework\Core\Contour $_coreContour */
    $instance->_coreContour = \Framework\Core\Contour::getInstance();
    /** @var \Framework\Modulus\Modal\TrackingSessions $_mSessions */
    $instance->_mSessions = \Framework\Core\Receptionist::modal("TrackingSessions", "Holylandhealth", true);
    /** @var \Framework\Modulus\Modal\TrackingSessionsData $_mSessionsData */
    $instance->_mSessionsData = \Framework\Core\Receptionist::modal("TrackingSessionsData", "Holylandhealth", true);
    /** @var \Framework\Modulus\Modal\TrackingViews $_mViews */
    $instance->_mViews = \Framework\Core\Receptionist::modal("TrackingViews", "Holylandhealth", true);
    /** @var \Framework\Commander\MediaBuyEmail $_cMediaBuyEmail */
    $instance->_cMediaBuyEmail= \Framework\Core\Receptionist::controller("MediaBuyEmail", true);
    /** Return the configured instance of the tracking controller */
    return $instance;
  }

  /**
   * Return the session key for this tracking session
   * @method sessionKey
   * @return int
   */
  public function sessionKey()
  {
    /** Check if $_sessionKey is set */
    if(!isset($this->_sessionKey)){
      /** Not set return 0 */
      return 0;
    }
    /** Set return the value */
    return $this->_sessionKey;
  }

  /**
   * Return the campaign key for this session
   * @method campaignKey
   * @return int
   */
  public function campaignKey()
  {
    /** check if the $_campaignKey is set */
    if(!isset($this->_campaignKey)){
      /** return 0 not set */
      return 0;
    }
    /** return the value */
    return $this->_campaignKey;
  }

  /**
   * Initalize the tracking session
   * @method initSession
   * @param $host
   * @param $funnelKey
   */
  public function initSession($host, $funnelKey)
  {
    $this->_funnelKey = $funnelKey;
    /** @var string $_sPrefix Set the system prefix so we can access session vars */
    $this->_sPrefix   = $host;
    /** @var string $_protocol The protocol this page view used */
    $this->_protocol  = $this->_coreEuri->server('method');
    /** Check for information in session */
    if($this->_checkSession()){
      /** We have session data load from there we are done */
      return $this->_loadFromSession();
    }
    /** No session data load from scratch */
    return $this->_makeSession();
  }

  /**
   * Add a page view to our current session
   * @method addView
   * @param $pageType
   * @param $loadTime
   * @return bool
   */
  public function addView($pageType, $loadTime)
  {
    /** Check if the protocal is get we don't want to track post requests */
    if(strtoupper($this->_protocol)!='GET'){
      /** return false we dont track post */
      return false;
    }
    /** return the result of the addView query */
    return $this->_mViews->addView($this->_sessionKey, $pageType, $loadTime);
  }

  /**
   * Make a new session
   * @method _makeSession
   */
  private function _makeSession()
  {
    /** Set the campaign key */
    $this->_setCampaignKey();
    /** Set the session key */
    $this->_setSessionKey();
    /** Add the tracking data to session */
    $this->_addSessionData();
    /** Set the tracking session data */
    $this->_setSession();
  }

  /**
   * Load the tracking variable from the session
   * @method _loadFromSession
   */
  private function _loadFromSession()
  {
    /** @var int $_funnelKey clean the value and set the objects funnelKey */
    $this->_funnelKey   = intval($_SESSION[$this->_sPrefix][$this->_cPrefix]['funnel-key']);
    /** @var int $_campaignKey clean the value and set the objects campaignKey */
    $this->_campaignKey = intval($_SESSION[$this->_sPrefix][$this->_cPrefix]['campaign-key']);
    /** @var int $_sessionKey clean teh value and set the sessionKey variable */
    $this->_sessionKey  = intval($_SESSION[$this->_sPrefix][$this->_cPrefix]['session-key']);
  }

  /**
   * Setup the session data this way we don't do alot of work and overwrite our session token on every page load
   * @method _setSession
   */
  private function _setSession()
  {
    /** Check if the site prefix session area is set */
    if(!isset($_SESSION[$this->_sPrefix])){
      /** Site session data area not set make it */
      $_SESSION[$this->_sPrefix] = [];
    }
    /** Check if the site data tracking information tracking data area is set */
    if(!isset($_SESSION[$this->_sPrefix][$this->_cPrefix])){
      /** Tracking data section not created make it */
      $_SESSION[$this->_sPrefix][$this->_cPrefix] = [];
    }
    /** Set the funnel key for this session */
    $_SESSION[$this->_sPrefix][$this->_cPrefix]['funnel-key']   = $this->_funnelKey;
    /** Set the campaign key for this session */
    $_SESSION[$this->_sPrefix][$this->_cPrefix]['campaign-key'] = $this->_campaignKey;
    /** Set the session key for this session */
    $_SESSION[$this->_sPrefix][$this->_cPrefix]['session-key']  = $this->_sessionKey;
  }

  /**
   * Set the _sessionKey variable in the tracking object after making the session
   * @method _setSessionKey
   * @return mixed
   */
  private function _setSessionKey()
  {
    return $this->_sessionKey = $this->_mSessions->makeSession(
      /** Funnel key for this session */
      $this->_funnelKey,
      /** Email tracking link key for this session */
      $this->_campaignKey,
      /** IP Address of visitor */
      $this->_ipAddress()
    );
  }

  /**
   * Set the Campaign key for this session
   * @method _setCampaignKey
   * @return int
   */
  private function _setCampaignKey()
  {
    /** Check if the campaign key is set in the url */
    if(!isset($_GET['c'])){
      /** No get var default to 0 */
      return 0;
    }
    /** Validate the campaign key for existence in this funnel */
    if($this->_cMediaBuyEmail->campaignExists($this->_funnelKey, intval($_GET['c']))){
      /** @var int _campaignKey Key valid clean the key into an int and return it in the campaignKey object var */
      return $this->_campaignKey = intval($_GET['c']);
    }
    /** @var int _campaignKey return 0 campaign not set or not valid */
    return $this->_campaignKey = 0;
  }

  /**
   * Add data about this session to the session data table
   * @method _addSessionData
   * @return mixed
   */
  private function _addSessionData()
  {
    /** @var array $data Scrape as much data about this user that we can*/
    $data = [
      'get'=>$_GET,
      'post'=>$_POST,
      'cookie' =>$_COOKIE,
      'files'=>$_FILES,
      'session'=>$_SESSION,
      'server'=>$_SERVER
    ];
    /** Add the session data to the session json_encoded for easy access */
    return $this->_mSessionsData->addSessionData($this->_sessionKey, json_encode($data));
  }

  /**
   * Check session for data
   * @method _checkSession
   * @return bool
   */
  private function _checkSession()
  {
    /** Check if the session tracking data section is set */
    if(isset($_SESSION[$this->_sPrefix][$this->_cPrefix])){
      /** let them know it is */
      return true;
    }
    /** Not set return false */
    return false;
  }

  /**
   * Obtain the correct visitor ip address
   * @method _ipAddress
   * @return mixed
   */
  public function _ipAddress()
  {
    /** Check for the HTTP_CLIENT_IP  */
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
      /** HTTP_CLIENT_IP set return it this is the best option */
      return $_SERVER['HTTP_CLIENT_IP'];
    }
    /** Check for HTTP_X_FORWARD_FOR */
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      /** HTTP_X_FORWARDED_FOR set this is our second best option */
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    /** Unable to find better option fall back to REMOTE_ADDR */
    return $_SERVER['REMOTE_ADDR'];
  }

  /** ADMINISTRATION METHODS */

  public function funnelCampaignPageViews($startDate, $endDate, $funnelKey, $emailLinkKey)
  {
    /** Return the result of the modal funnelCampaignPageViews($startDate, $endDate, $funnelKey, $emailLinkKey) method */
    return $this->_mViews->funnelCampaignPageViews($startDate, $endDate, $funnelKey, $emailLinkKey);
  }
}
