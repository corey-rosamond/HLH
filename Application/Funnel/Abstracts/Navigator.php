<?php
/**
 * Navigator
 *
 * @package App\Abstracts
 * @version 1.2.0
 */
namespace App\Abstracts;

/**
 * Navigator
 *
 * This helps traverse the funnel system swiftly
 */
abstract class Navigator
{
  const ajaxPost = 0;
  /** @const vsl */
  const vsl = 1;
  /** @const checkout */
  const checkout = 2;
  /** @const upsell1 */
  const upsell1 = 3;
  /** @const upsell2 */
  const upsell2 = 4;
  /** @const upsell3 */
  const upsell3 = 5;
  /** @const upsell4 */
  const upsell4 = 6;
  /** @const upsell5 */
  const upsell5 = 7;
  /** @const antiSpamPolicy */
  const antiSpamPolicy = 8;
  /** @const contactUs */
  const contactUs = 9;
  /** @const disclaimer */
  const disclaimer = 10;
  /** @const privacyPolicy */
  const privacyPolicy = 11;
  /** @const refunds */
  const refunds = 12;
  /** @const termsAndConditions */
  const termsAndConditions = 13;
  /** @const thankYou */
  const thankYou = 14;
  /** @const download */
  const download = 15;
  /** @const downsell1 */
  const downsell1 = 16;
  /** @const downsell2 */
  const downsell2 = 17;
  /** @const downsell3 */
  const downsell3 = 18;
  /** @const downsell4 */
  const downsell4 = 19;
  /** @const downsell5 */
  const downsell5 = 20;
  /** @const advertorial */
  const advertorial = 21;
  /** @const error */
  const error = 22;
  /** @const notFound */
  const notFound = 23;
  /** @const tsl */
  const tsl = 24;
  /** @const contentPage */
  const contentPage = 25;
  /** @const successNext */
  const successNext = 0;
  /** @const failureNext */
  const failureNext = 1;
  /** @const requirements */
  const requirements = 3;
  /** @const back */
  const back = 4;
  /** @const typePage */
  const typePage = 1;
  /** @const typeEvent */
  const typeEvent = 2;
  /** @const pageMode */
  const pageMode = 0;
  /** @const eventMode */
  const eventMode = 1;
  /** @var array $_dFunnelEventMap This is a map of the directions you can go in a funnel from each event  */
  protected $_dFunnelEventMap=[
    self::checkout=>[
      self::successNext=>[[self::typePage,self::upsell1]],
      self::failureNext=>[[self::typePage,self::checkout]],
    ],
    /** ONE */
    self::upsell1=>[
      self::successNext=>[[self::typePage,self::upsell2],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    self::downsell1=>[
      self::successNext=>[[self::typePage,self::upsell2],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    /** TWO */
    self::upsell2=>[
      self::successNext=>[[self::typePage,self::upsell3],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    self::downsell2=>[
      self::successNext=>[[self::typePage,self::upsell3],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    /** THREE */
    self::upsell3=>[
      self::successNext=>[[self::typePage,self::upsell4],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    self::downsell3=>[
      self::successNext=>[[self::typePage,self::upsell4],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    /** FOUR */
    self::upsell4=>[
      self::successNext=>[[self::typePage,self::upsell5],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    self::downsell4=>[
      self::successNext=>[[self::typePage,self::upsell5],[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    /** FIVE */
    self::upsell5=>[
      self::successNext=>[[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    self::downsell5=>[
      self::successNext=>[[self::typeEvent,self::thankYou]],
      self::failureNext=>[[self::typeEvent,self::thankYou]],
    ],
    /** END PATH */
    self::thankYou=>[
      self::successNext=>[[self::typePage,self::thankYou]],
      self::failureNext=>[[self::typePage,self::thankYou]],
    ]
  ];
  /** @var array $_dFunnelEventMap This is a map of the directions you can go in a funnel from each event  */
  protected $_dFunnelPageMap=[
    self::vsl=>[
      self::successNext=>[
        [self::typePage,self::checkout]
      ],
      self::failureNext=>[
        [self::typePage,self::tsl],
        [self::typePage,self::vsl]
      ]
    ],
    self::checkout=>[
      self::successNext=>[
        [self::typeEvent,self::checkout,"Checkout"],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ],
    ],
    /** ONE */
    self::upsell1=>[
      self::successNext=>[
        [self::typeEvent,self::upsell1,"Upsell1"],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ],
      self::failureNext=>[
        [self::typePage,self::downsell1],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ]
    ],
    self::downsell1=>[
      self::successNext=>[
        [self::typeEvent,self::downsell1,"Downsell1"],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ],
      self::failureNext=>[
        [self::typePage,self::upsell2],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ]
    ],
    /** TWO */
    self::upsell2=>[
      self::successNext=>[
        [self::typeEvent,self::upsell2,"Upsell2"],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ],
      self::failureNext=>[
        [self::typePage,self::downsell2],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ]
    ],
    self::downsell2=>[
      self::successNext=>[
        [self::typeEvent,self::downsell2,"Downsell2"],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ],
      self::failureNext=>[
        [self::typePage,self::upsell3],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ]
    ],
    /** THREE */
    self::upsell3=>[
      self::successNext=>[
        [self::typePage,self::upsell3,"Upsell3"],
        [self::typeEvent,self::thankYou,"ThankYou"]
      ]
    ],
    self::downsell3=>[
      self::successNext=>[
        [self::typePage,self::upsell3],
        [self::typeEvent,self::thankYou]
      ]
    ],
    /** FOUR */
    self::upsell4=>[
      self::successNext=>[
        [self::typePage,self::upsell5],
        [self::typeEvent,self::thankYou]
      ]
    ],
    self::downsell4=>[
      self::successNext=>[
        [self::typePage,self::upsell5],
        [self::typeEvent,self::thankYou]
      ]
    ],
    /** FIVE */
    self::upsell5=>[
      self::successNext=>[
        [self::typeEvent,self::thankYou]
      ]
    ],
    self::downsell5=>[
      self::successNext=>[
        [self::typeEvent,self::thankYou]
      ]
    ],
    /** END FUNNEL PATH */
    self::thankYou=>[
      self::back=>[self::typePage,self::privacyPolicy]
    ],
    self::contactUs=>[
      self::successNext=>[
        [self::typeEvent,self::ajaxPost,"AjaxPost"]
      ]
    ]
  ];
  /** @var int $_pageType This is the page type for the page we are currently on */
  protected $_pageType;
  /** @var int $_eventType this is the event type for the event we are currently on */
  protected $_eventType;
  /** @var int $_navigatorMode This is the mode the navigator is curently in. Page/Event */
  protected $_navigatorMode;
  /** @var array $_dOrder */
  protected $_dOrder;
  /** @var \Framework\Modulus\Modal\FunnelErrorMessages $_mFunnelErrorMessages */
  protected $_mFunnelErrorMessages;
  /** @var \Framework\Commander\Tracking $_cTracking */
  protected $_cTracking;

  protected $_sPrefix;

  /**
   * Navigator constructor.
   * @method __construct
   * @param $system
   */
  public function __construct($system)
  {

    $this->_sPrefix = $system->_dFunnelHost;
    /** @var integer _navigatorMode */
    $this->_navigatorMode = $this->_findMode();
    /** Check if the Session area for this funnel is set */
    if(!isset($_SESSION[$this->_sPrefix])){
      /** Set the session var for this funnel */
      $_SESSION[$this->_sPrefix] = [];
    }
    /** Check if the funnel progress index is set in session */
    if(!isset($_SESSION[$this->_sPrefix]['funnel-progress'])){
      /** set the funnel progress section */
      $_SESSION[$this->_sPrefix]['funnel-progress'] = [];
    }
    /** Check if the page funnel progress index is set */
    if(!isset($_SESSION[$this->_sPrefix]['funnel-progress'][self::pageMode])){
       /** Make the page funnel progress sub array */
      $_SESSION[$this->_sPrefix]['funnel-progress'][self::pageMode] = [];
    }
    /** Check if the event funnel progress sub array is set */
    if(!isset($_SESSION[$this->_sPrefix]['funnel-progress'][self::eventMode])){
      /** Make the event funnel progress sub array */
      $_SESSION[$this->_sPrefix]['funnel-progress'][self::eventMode] = [];
    }
    /** @var \Framework\Modulus\Modal\FunnelErrorMessages _mFunnelErrorMessages */
    $this->_mFunnelErrorMessages = \Framework\Core\Receptionist::modal('FunnelErrorMessages',true);
    /** @var \Framework\Commander\Tracking $_cTracking */
    $this->_cTracking = \Framework\Core\Receptionist::controller('Tracking',true);
    /** @var array _dOrder */
    $this->_dOrder = $system->_cFunnel->order();

  }

  /**
   * Return the correct mode identifier
   * @method _findMode
   * @return int
   */
  protected function _findMode()
  {
    /** Check if this object is an instance of the base page object */
    if($this instanceof \App\Abstracts\FunnelPage){
      /** Return the page identifier */
      return self::pageMode;
    }
    /** Return the event Identifier */
    return self::eventMode;
  }

  /**
   * This function in charge of cleaning up after events before
   * they close out or get redirected. Good or bad this method is in charge
   * of the cleanup
   * @method _completeAndMove
   * @param $selector
   */
  protected function _completeAndMove($selector)
  {
    /** CHeck if the selector is for success */
    if($selector==self::successNext){
      /** Run the success version of the method */
      $this->_completeAndMoveSuccess();
      /** exit just so that nothing funny can happen */
      exit();
    }
    /** Run the fail version of the method */
    $this->_completeAndMoveFailure();
    /** Tell the program to exit just in case */
    exit();
  }

  /**
   * Complete the event and and move them to their
   * NextSuccess destination
   * @method _completeAndMoveSuccess
   */
  protected function _completeAndMoveSuccess()
  {
    /** Clear out all the errors so the next page does not think this was a failure */
    $this->_sessionErrorsClear();
    /** Set the event progress */
    $this->_addProgress();
    /** Move the user to their successNext */
    $this->_sendToFirstViable(self::successNext);
    /** Add an exit to prevent anything after the redirect */
    exit();
  }

  /**
   * Complete the failed event clean up and
   * move them to the correct place
   * @method _completeAndMOveFailure
   */
  protected function _completeAndMoveFailure()
  {
    /** Check if errors are set */
    if(!isset($this->_errors)){
      /** @var array _errors If the errors array is not set set it to avoid erros */
      $this->_errors = [];
    }
    /** Get any errors into the session so the page we send them to can tell them why */
    $this->_errorsToSession($this->_errors);
    /** Kick them back to their next fail action */
    $this->_sendToFirstViable(self::failureNext);
    /** Add an exit to prevent anything after the redirect */
    exit();
  }

  /**
   * Send them to the first viable destination in the array
   * @method _sendToFirstViable
   * @param $selector
   */
  protected function _sendToFirstViable($selector)
  {
    /** @var array $map get the map for the type */
    $map = $this->_map();
    /** @var integer $type get the id for this type */
    $type = $this->_type();
    /** @var integer $dest loop over the dest ids */
    foreach($map[$type][$selector] as $dest){
      /** Convert the dest into a type, destination pair */
      list($type, $destination) = $dest;
      /** @var mixed $dest Try and get the destination */
      $page = $this->_getPageByType($destination);
      /** Test if page is false */
      if($page!==false){
        /** @var string $base Setup the base for the redirect */
        $base = (($type==self::typePage)?"/":"/Event/");
        /** Redirect the user to the given destination */
        header("location: {$base}{$page['name']}");
        /** Call exit to prevent any funny business */
        exit();
      }
    }
    exit();
    /** If we got here something has gone very wrong */
    \Framework\Core\Architect::getInstance()->_downForMaintenance();
    /** exit the app there nothing more we can do here */
    exit();
  }

  /**
   * Send the user back to this navigationTypes configured back action
   * @method _goBack
   */
  protected function _goBack()
  {
    /** Push the 2 items in the array into type and destination */
    list($type, $destination) = $this->_dFunnelEventMap[$this->_eventType][self::back];
    /** Redirect them to that type and destination */
    $this->_redirectTo($type, $destination);
    /** Exit just in case something odd is going on */
    exit();
  }

  /**
   * Redirect the user to a destination and event type
   * @method _redirectTo
   * @param $type
   * @param $destination
   */
  protected function _redirectTo($type, $destination)
  {
    /** @var string $base the base url location */
    $base = (($type==self::typePage)?"/":"/Event/");
    /** @var string $dest the ending part of the destination */
    $dest = $this->_getPageByType($destination);
    /** Redirect the browser to that location */
    header("location: {$base}{$dest['name']}");
    /** Exit just in case something odd is going on */
    exit();
  }

  /**
   * Pre Qualify that this is the correct page or event
   * we should be on at this moment. if it is not move us
   * to the right place
   * @method _preQualify
   */
  protected function _preQualify()
  {
    /** Test if cart status is set and if it is set to complete */
    if(isset($this->_dOrder['status'])&&$this->_dOrder['status']==\Framework\Commander\Orders::completed){
      /** @var array $page get the thank you page*/
      $page = $this->_getPageByType(self::thankYou);
      /** Redirect the user to the thank you page */
      header("location: /{$page['name']}");
      /** Exit just in case something odd is going on */
      exit();
    }
    /** Test if cart status is set and if it is set to completedWithBalance */
    if(isset($this->_dOrder['status'])&&$this->_dOrder['status']==\Framework\Commander\Orders::completedWithBalance){
      /** @var array $page get the thank you page*/
      $page = $this->_getPageByType(self::thankYou);
      /** Redirect the user to the thank you page */
      header("location: /{$page['name']}");
      /** Exit just in case something odd is going on */
      exit();
    }
    /** Check if this page was completed already */
    if($this->_alreadyCompleted()){
      /** Move them to the first viable success destination */
      $this->_sendToFirstViable(self::successNext);
      /** Exit just in case something odd is going on */
      exit();
    }
    /** Clear out all the old session errors before we return */
    if($this->_navigatorMode==self::eventMode){
      $this->_sessionErrorsClear();
    }
    /** Return just to end the method */
    return true;
  }

  /**
   * Check if the requirements have been met
   * @method _requirementsMet
   * @return bool
   */
  protected function _requirementsMet()
  {
    /** @var array $map The current modes map */
    $map = $this->_map();
    /** @var int $type The current modes type */
    $type = $this->_type();
    /** @var boolean $pass Initialize pass to true but it only takes one fail to make it false */
    $pass = true;
    /** Check if it has any requirements */
    if(!isset($map[$type][self::requirements])){
      /** No requirements return true */
      return $pass;
    }
    /** @var integer $required Loop through the required mode types */
    foreach($map[$type][self::requirements] as $required ){
      /** Check if the required mode type is in the array */
      if(!in_array($required, $_SESSION[$this->_sPrefix]['funnel-progress'][$this->_navigatorMode])){
        /** @var boolean $pass We found it in the array set pass to false */
        $pass = false;
      }
    }
    /** Return Pass */
    return $pass;
  }

  /**
   * Check if this mode type has already been completed
   * @methodod _alreadyCompleted
   * @return bool
   */
  protected function _alreadyCompleted()
  {
    /** @var array $completedModeTypes This is a collection of all the completed types for this mode */
    $completedModeTypes = $_SESSION[$this->_sPrefix]['funnel-progress'][$this->_navigatorMode];
    /** Check if the current mode type is in the completed mode types */
    if(in_array($this->_type(), $completedModeTypes)){
      /** return true */
      return true;
    }
    /** return false */
    return false;
  }

  /**
   * Add this navigatorMode to the progress track
   * @method _addProgress
   * @param bool $mode
   * @param bool $type
   */
  protected function _addProgress($mode=false, $type=false)
  {
    /** Check if mode is false */
    if($mode===false){
      /** @var integer $mode eventType not provided use the navigatorMode */
      $mode = $this->_navigatorMode;
    }
    /** Check if type is false */
    if($type===false){
      /** @var integer $type */
      $type = $this->_type();
    }
    /** @var int $index Get the next usable index */
    $index = sizeof($_SESSION[$this->_sPrefix]['funnel-progress'][$mode]);
    /** push the progress marker into that open spot */
    $_SESSION[$this->_sPrefix]['funnel-progress'][$mode][$index] = $type;
  }

  /**
   * Clear the errors out of session
   * @method _sessionErrorsClear
   */
  protected function _sessionErrorsClear()
  {
    /** Unset the error data array itself  */
    unset($_SESSION[$this->_sPrefix]['error-data']);
    /** Remake the error data array */
    $_SESSION[$this->_sPrefix]['error-data'] = [];
  }

  /**
   * Move the errors to the session
   * @method _errorsToSession
   * @param $errors
   */
  protected function _errorsToSession($errors)
  {
    /** @var string $error Loop over the errors */
    foreach($errors as $error){
      /** log the error */
      $this->_mFunnelErrorMessages->write(get_called_class(), $error, $this->_cTracking->sessionKey());
      /** Add the error to the error-data array */
      array_push($_SESSION[$this->_sPrefix]['error-data'], $error);
    }
  }

  /**
   * Return the type selector for the mode we are in
   * @method _type
   * @return int
   */
  protected function _type()
  {
    /** Check if we are in pageMode */
    if($this->_navigatorMode==self::pageMode){
      /** Return the pateType */
      return $this->_pageType;
    }
    /** Return the eventType */
    return $this->_eventType;
  }

  /**
   * Return the map for the mode we are in
   * @method _map
   * @return mixed
   */
  protected function _map()
  {
    /** Check if we are in pageMode */
    if($this->_navigatorMode==self::pageMode){
      /** Return the pageMap */
      return $this->_dFunnelPageMap;
    }
    /** Return the eventMap */
    return $this->_dFunnelEventMap;
  }

  /**
   * Get a page from its type
   * @method _getPageByType
   * @param $type
   * @return mixed
   */
  protected function _getPageByType($type)
  { return $this->_cFunnel->getPageByType($type); }
}
