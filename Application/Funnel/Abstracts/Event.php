<?php
/**
 * Event
 *
 * @package App\Abstracts
 * @version 1.2.0
 */
namespace App\Abstracts;

/**
 * Event
 *
 * This is the event base
 */
class Event extends \App\Abstracts\Navigator
{
  /****************************************************/
  /*                    FUNNEL MODE                   */
  /****************************************************/
  /** @const integer fModeLive */
  const fModeLive = 0;
  /** @const integer fModeDevelopment */
  const fModeDevelopment = 1;
  /** @const integer fModeMaintenance */
  const fModeMaintenance = 2;
  /** @const integer fModeComingSoon */
  const fModeComingSoon = 3;
  /** @const integer fModeOutOfStock */
  const fModeOutOfStock = 4;
  /** @var \App\System $_system */
  protected $_system;
  /** @var string $_sPrefix */
  protected $_sPrefix;
  /** @var \Framework\Commander\Funnel */
  protected $_cFunnel;
  /** @var integer $_sFunnelMode */
  protected $_sFunnelMode = self::fModeDevelopment;
  /** @var array $_errors */
  protected $_errors;
  /** @var array $_products */
  protected $_products;

  /**
   * Construct the base page object and setup the environment for other pages
   * @method _construct
   * @param $system
   */
  public function __construct($system)
  {
    /** @var \App\System $_system */
    $this->_system  = $system;
    /** @var string $_sPrefix */
    $this->_sPrefix = $system->_dFunnelHost;
    /** @var \Framework\Commander\Funnel _cFunnel */
    $this->_cFunnel = $system->_cFunnel;
    $this->_sFunnelMode = $this->_cFunnel->getMode();
    /** @var array _products */
    if(isset($_POST['products'])){
      $this->_products = $_POST['products'];
    }
    /** Return the parent elements construct */
    return parent::__construct($system);
  }

  /**
   * Try and find a product in a list of order items
   * @method _inOrderItems
   * @param $orderItems
   * @param $product
   * @return array|bool
   */
  protected function _inOrderItems($orderItems, $product)
  {
    /** @var array $item loop through the products */
    foreach($orderItems as &$item){
      /** Check if the product key matches the on referenced product */
      if($item['product-key']==$product){
        /** return the item */
        return $item;
      }
    }
    /** return false. We dident find it */
    return false;
  }
}