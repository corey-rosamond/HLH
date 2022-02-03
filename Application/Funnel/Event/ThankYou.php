<?php
/**
 * ThankYou
 *
 * @package App\Event
 * @version 1.2.0
 */
namespace App\Event;

/**
 * ThankYou
 *
 * This is the ThankYou
 */
class ThankYou extends \App\Abstracts\Event
{
  /** @var int $_eventType */
  protected $_eventType = self::thankYou;
  /** @var \Framework\Core\Euri $_coreEuri */
  protected $_coreEuri;
  /** @var \Framework\Core\Admit $_coreAdmit */
  protected $_coreAdmit;
  /** @var \Framework\Core\Contour $_coreContour */
  protected $_coreContour;
  /** @var \Framework\Commander\Tracking $_cTracking */
  protected $_cTracking;
  /** @var \App\System $_cSystem */
  protected $_cSystem;
  /** @var \Framework\Commander\Funnel $_cFunnel */
  protected $_cFunnel;

  /**
   * ThankYou constructor.
   * @method __construct
   * @param $system
   */
  public function __construct($system)
  {
    /** @var \App\System _cSystem */
    $this->_cSystem = $system;
    /** @var \Framework\Core\Euri _coreEuri */
    $this->_coreEuri = $system->_coreEuri;
    /** @var \Framework\Core\Admit _coreAdmit */
    $this->_coreAdmit = $system->_coreAdmit;
    /** @var \Framework\Core\Admit _coreContour */
    $this->_coreContour = $system->_coreContour;
    /** @var \Framework\Commander\Tracking _cTracking */
    $this->_cTracking = $system->_cTracking;
    /** Return whatever the page abstract gives us back */
    return parent::__construct($system);
  }

  /**
   * Run the event
   * @method run
   */
  public function run()
  {
    $this->_dOrder = $this->_cFunnel->order();
    if(!isset($this->_dOrder['customer-key'])){
      $this->_redirectTo(self::typePage, self::checkout);
    }
    /** Run pre Qualify this will make sure we end up where we need to be */
    $outcome = $this->_cFunnel->closeoutOrder();
    /** if it worked send them to the thank you page */
    if($outcome){
      $this->_completeAndMove(self::successNext);
      exit();
    }
    $this->_completeAndMove(self::failureNext);
  }
}
