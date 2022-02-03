<?php
/**
* Order
*
* @package Framework\Store
* @version 1.2.0
*/

namespace Framework\Store;

/**
 * Order
 *
 * The order object is my solution to all of my annoying
 * data here data there modal here modal there issues
 */
class Order{

  public function __construct($key)
  {
    $this->__oLoad($key);

  }


  /** Customer Interface */

  private $_cKey;

  private $_cEmail;

  private $_cValueBeforeCost;

  private $_cValueAfterCost;

  private $_cOriginDate;

  private $_OriginFunnel;

  private function __cLoad($key)
  {

  }


  /** Order Interface */

  private $_oKey;

  private $_oFirstName;

  private $_oLastName;

  private $_oPhoneNumber;

  private $_oSessionKey;

  private $_oFunnelKey;

  private $_oCampaignKey;

  private $_oStatus;

  private $_oTotalBeforeCost;

  private $_oTotalAfterCost;

  private $_oBillingAddressKey;

  private $_oShippingAddressKey;

  private $_oOrderDate;

  private $_oIsTest;

  private function __oLoad($key)
  {

  }


  /** Billing Address Interface */

  private $_bKey;

  private $_bName;

  private $_bLineOne;

  private $_bLineTwo;

  private $_bCity;

  private $_btate;

  private $_bZip;

  private function __bLoad($key)
  {


  }


  /** Shipping Address Interface */

  private $_sKey;

  private $_sName;

  private $_sLineOne;

  private $_sLineTwo;

  private $_sCity;

  private $_sState;

  private $_sZip;

  private function __sLoad($key)
  {


  }


  /** Order Items Interface */




  /** Order Payment Interface */




  /** Order Fulfillment Interface */
}