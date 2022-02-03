<?php
/**
 * Checkout
 *
 * @package App\Event
 * @version 1.2.0
 */
namespace App\Event;

/**
 * Checkout
 *
 * This is the Checkout event
 */
class Checkout extends \App\Abstracts\Event
{
  /** @var int Include the eventType */
  protected $_eventType = self::checkout;
  /** @var array $_doNotSave These are post vars that should never ever be saved in any way shape or form */
  protected $_doNotSave = ['card-number','cvv','expiration-month','expiration-year'];

  protected $_errors;

  /**
   * Checkout constructor.
   * @method _construct
   * @param $system
   */
  public function __construct($system)
  {
    $this->_errors = [];
    return parent::__construct($system);
  }

  /**
   * Run the event
   * @method run
   */
  public function run()
  {
    /** Run pre Qualify this will make sure we end up where we need to be */
    $this->_preQualify();
    /** Add the the checkout form data to the session so we can repopulate the form */
    $this->_addDataToSession();
    /** Validate the addresses */
    //$this->_validateAddress();
    /** Check if we have errors in the array */
    if(sizeof($this->_errors)>=1){
      /** _completeAndmove will in this case log the error and kick us back to our fail action */
      $this->_completeAndMove(self::failureNext);
    }
    /** @var array $customer The getCustomer method will try and retrieve and existing customer if it can not
     * it will make a new one and give us that
     */
    $customer = $this->_cFunnel->getCustomer( $_SESSION[$this->_sPrefix]['order-data']['email-address'] );
    /** Load the order if it already exists */
    if(!$this->_cFunnel->loadOrder()){
      /** If there is not an order yet make one */
      $this->_makeOrder($customer);
    }
    /** @var array $outcome This array holds a result boolean and a collection of error messages if we failed */
    $outcome = $this->_cFunnel->processCart($_SESSION[$this->_sPrefix]['order-data']['products']);
    /** If failse was returned then we have something we need to go back and fix */
    if(!$outcome['result']){
      /** @var array _errors array of error messages */
      $this->_errors = $outcome['messages'];
      /** _completeANdmove will in this case log the error and kick us back to our fail action */
      $this->_completeAndMove(self::failureNext);
    }
    /**
     * _completeAndMove will go ahead and complete the event for us
     * including keeping track of the fact that this page type has been completed
     */
    $this->_completeAndMove(self::successNext);
  }

  /**
   * Make an order
   * @method _makeOrder
   * @param $customer
   */
  private function _makeOrder($customer)
  {
    $billingAddress=[
      'name'=> $_SESSION[$this->_sPrefix]['order-data']['first-name'].' '.$_SESSION[$this->_sPrefix]['order-data']['last-name'],
      'line-one'=> $_SESSION[$this->_sPrefix]['order-data']['billing-address-one'],
      'line-two'=> $_SESSION[$this->_sPrefix]['order-data']['billing-address-two'],
      'city'=> $_SESSION[$this->_sPrefix]['order-data']['billing-city'],
      'state'=> $_SESSION[$this->_sPrefix]['order-data']['billing-state'],
      'zip'=> $_SESSION[$this->_sPrefix]['order-data']['billing-zip']
    ];
    /** Check if shipping and billing are the same */
    if(isset($_POST['billing-is-shipping'])&&$_POST['billing-is-shipping']=='off'){
      $shippingAddress = [
        'name'=> $_SESSION[$this->_sPrefix]['order-data']['shipping-name'],
        'line-one'=> $_SESSION[$this->_sPrefix]['order-data']['shipping-address-one'],
        'line-two' => $_SESSION[$this->_sPrefix]['order-data']['shipping-address-two'],
        'city' => $_SESSION[$this->_sPrefix]['order-data']['shipping-city'],
        'state' => $_SESSION[$this->_sPrefix]['order-data']['shipping-state'],
        'zip' => $_SESSION[$this->_sPrefix]['order-data']['shipping-zip']
      ];
    } else { $shippingAddress = $billingAddress; }
    /** make the order */
    $this->_cFunnel->makeOrder(
      $customer['key'],
      $_SESSION[$this->_sPrefix]['order-data']['first-name'],
      $_SESSION[$this->_sPrefix]['order-data']['last-name'],
      $_SESSION[$this->_sPrefix]['order-data']['phone-number'],
      $billingAddress,
      $shippingAddress,
      (($this->_sFunnelMode==self::fModeDevelopment)?true:false)
    );
  }

  /**
   * Validate a physical Address
   * @method _validateAddress
   */
  private function _validateAddress()
  {
    /** @var array $result */
    $result = $this->_cFunnel->validateAddress(
      $_POST['billing-address-one'],$_POST['billing-address-two'],$_POST['billing-city'],$_POST['billing-state'],$_POST['billing-zip']
    );
    /** Check if the result is a failure */
    if(!$result['result']){
      /** @var string $message Loop over the error messages */
      foreach($result['messages'] as $message){
        /** Push the error message into the error array */
        array_push($this->_errors, "<strong>Billing Address: </strong>".$message);
      }
    }
    /** Check if we need to validate the shipping address */
    if(isset($_POST['billing-is-shipping'])&&$_POST['billing-is-shipping']=='off'){
      /** @var array $result */
      $result = $this->_cFunnel->validateAddress(
        $_POST['shipping-address-one'],$_POST['shipping-address-two'],$_POST['shipping-city'],$_POST['shipping-state'],$_POST['shipping-zip']
      );
      /** Check if the result was a failure */
      if(!$result['result']){
        /** @var string $message Loop over the error messages */
        foreach($result['messages'] as $message ){
          /** Push each error message into the error array */
          array_push($this->_errors, "<strong>Shipping Address: </strong>".$message);
        }
      }
    }
  }

  /**
   * Add the form data to the session so that we can
   * repopulate the form if they fail for some reason
   */
  private function _addDataToSession()
  {
    /** Make sure to unset the order data so we dont have old data */
    unset($_SESSION[$this->_sPrefix]['order-data']);
    /** Sometimes unset was missing things so I added this as a secondary precaution */
    if(!isset($_SESSION[$this->_sPrefix]['order-data'])){
      /** Unset each element individualy */
      $_SESSION[$this->_sPrefix]['order-data'] = [];
    }
    /** Check if billing and shipping are the same */
    if(!isset($_POST['billing-is-shipping'])){
      /** Set billing is shipping to off. Just to make it simple to understand */
      $_POST['billing-is-shipping']='off';
    }
    /**
     * Loop over the post vars and as long as they are not in the doNotSave
     * array push them into the session
     * @var  $key
     * @var  $value
     */
    foreach($_POST as $key => $value ){
      /** check if this is in the doNotSave array */
      if(!in_array($key, $this->_doNotSave)){
        /** We are clear to save it write it to the session */
        $_SESSION[$this->_sPrefix]['order-data'][$key] = $value;
      }
    }
  }
}
