<?php
/**
 * Orders
 *
 * @package \Framework\Commander
 * @version 1.2.0
 */
namespace Framework\Commander;

/**
 * Orders
 *
 * This item is the main controller for orders
 * @todo take 90% of this shit out and put it in sub objects
 * @todo Obfuscate the shit out of this
 */
class Orders extends \Framework\Commander\Commander
{
  /** @var \Framework\Modulus\Modal\Orders $_mOrders */
  private $_mOrders;
  /** @var \Framework\Commander\Address $_cCustomer */
  private $_cCustomer;
  /** @var \Framework\Commander\Address $_cAddress */
  private $_cAddress;
  /** @var \Framework\Commander\OrderItems $_cOrderItems */
  private $_cOrderItems;
  /** @var \Framework\Commander\OrderNotes $_cOrderNotes */
  private $_cOrderNotes;
  /** @var \Framework\Commander\PaymentProcessor $_cPaymentProcessor */
  private $_cPaymentProcessor;
  /** Order initially created */
  const created = 0;
  /** They paid for at least part of the order */
  const charged = 1;
  /** The order is paid in full */
  const completed = 2;
  /** The order was closed out but still has a balance */
  const completedWithBalance = 3;
  /** The order was refunded */
  const refund = 4;
  /** The order was submitted to fulfillment */
  const fulfillment = 5;
  /** The order is in transit */
  const shipped = 6;
  /** We are done */
  const closed = 7;
  /** @var array $_statusMessages */
  private $_statusMessages = [
    self::created => 'Order Created',
    self::charged => 'Order Has processed at least 1 successful charge',
    self::completed => 'Order Completed',
    self::completedWithBalance => 'Order Completed with balance',
    self::refund => 'Order Refunded',
    self::fulfillment => 'Order Moved to Fulfillment',
    self::shipped => 'Order Shipped',
    self::closed => 'Order Closed'
  ];
  /** @var array $_activeOrder The active order array */
  private $_activeOrder;
  /** @var array $_activeCustomer The active customer array */
  private $_activeCustomer;
  /** @var array $_activeItems The active order items array */
  private $_activeItems;
  /** @var array $_activeBilling The active billing address */
  private $_activeBilling;
  /** @var array $_activeShipping The active shipping address */
  private $_activeShipping;

  /**
   * Constructor to get all of our needed controllers and modals
   * @method construct
   * @return mixed
   */
  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_mOrders = \Framework\Core\Receptionist::modal("Orders", true);
    /** Controllers */
    $instance->_cProducts = \Framework\Core\Receptionist::controller('Products', true);
    $instance->_cCustomer = \Framework\Core\Receptionist::controller('Customer', true);
    $instance->_cAddress = \Framework\Core\Receptionist::controller('Address', true);
    $instance->_cOrderItems = \Framework\Core\Receptionist::controller('OrderItems', true);
    $instance->_cOrderNotes = \Framework\Core\Receptionist::controller('OrderNotes', true);
    $instance->_cPaymentProcessor = \Framework\Core\Receptionist::controller('PaymentProcessor', true);
    return $instance;
  }

  /**
   * Get an order item
   * If order key is given return that order
   * If no order key is sent try using the active order
   * If no active order return false
   * @method order
   * @param null $order
   * @return array|bool|mixed
   */
  public function order($order = null)
  {
    /** Check if an order was specified */
    if(!is_null($order)){
      /** Return the requested order */
      return $this->_mOrders->get($order);
    }
    /** No order specified see if we have an active order */
    if(is_null($this->_activeOrder)){
      /** No active order fail */
      return false;
    }
    /** return the active order */
    return $this->_activeOrder;
  }

  /**
   * Get the order items for an order
   * If order key is given return that orders items
   * If no order key is sent try using the active order
   * If no active order return false
   * @param null $order
   *
   * @return array|bool
   */
  public function orderItems($order = null)
  {
    /** Check if we were given an order key */
    if(!is_null($order)){
      /** Return the order items for that order */
      return $this->_cOrderItems->getOrderItems( $order );
    }
    /** No order specified check for active order items */
    if(is_null($this->_activeItems)){
      /** No active order items return false */
      return false;
    }
    /** Return the order items */
    return $this->_activeItems;
  }

  /**
   * Return the active billing address
   * @method billingAddress
   * @return array|bool
   */
  public function billingAddress()
  {
    /** Check that we have an active billing address */
    if(is_null($this->_activeBilling)){
      /** No address return false */
      return false;
    }
    /** Return the active billing address */
    return $this->_activeBilling;
  }

  /**
   * Return the active shipping address
   * @method shippingAddress
   * @return array|bool
   */
  public function shippingAddress()
  {
    /** Check if we have an active shipping addres */
    if(is_null($this->_activeShipping)){
      /** No address found return false */
      return false;
    }
    /** Retun the active shipping address */
    return $this->_activeShipping;
  }

  /**
   * Get a list of the customers orders
   * @method customerOrders
   * @param $customerKey
   * @return bool
   */
  public function customerOrders($customerKey)
  { return $this->_mOrders->customerOrders($customerKey); }

  /**
   * Add a product to the order and update the active order items
   * @method addProductToOrder
   * @param $product
   */
  public function addProductToOrder($product)
  {
    $this->_cOrderItems->addProductToOrder($this->_activeOrder['key'], $product);
    $this->_activeItems = $this->_cOrderItems->getOrderItems($this->_activeOrder['key']);
  }

  /**
   * Check if the given product is in the active order items
   * @method inOrderItems
   * @param $product
   * @return bool
   */
  public function inOrderItems($product)
  {
    if($this->_activeItems){
      foreach($this->_activeItems as $item){
        if($item['product-key'] == $product){ return true; }
      }
    }
    return false;
  }


  /**
   * @return mixed
   */
  public function getAbandonedOrders()
  {
    return $this->_mOrders->getAbandonedOrders();
  }

  /**
   * @return mixed
   */
  public function ordersReadyForFulfillment()
  {
    /** @var array $return This is where we will store the return order data */
    $return = [];
    $orders = $this->_mOrders->ordersReadyForFulfillment();
    if($orders===false){
      return false;
    }
    foreach($orders as $order){
      $orderRecord = $this->_mOrders->get($order['key']);
      $return[$order['key']] = [
        'Order' => $orderRecord,
        'OrderItems' => $this->_cProducts->getOrderProducts($order['key']),
        'BillingAddress' => $this->_cAddress->get($orderRecord['billing-address']),
        'ShippingAddress' => $this->_cAddress->get($orderRecord['shipping-address'])
      ];
    }
    return $return;
  }


  public function ordersInFulfillment()
  {

  }


  public function setComplemarTransactionKey($orderKey, $transKey)
  { $this->_mOrders->setComplemarTransactionKey($orderKey, $transKey); }

  /**
   * Load the whole order int the active objects and return
   * the whole thing as an array
   * @method loadKey
   * @param $key
   * @return array
   */
  public function loadOrder($key)
  {
    /** @var array $this->_activeOrder */
    $this->_activeOrder = $this->_mOrders->get($key);
    /** @var array $this->_activeOrder */
    $this->_activeCustomer = $this->_cCustomer->get($this->_activeOrder['customer-key']);
    /** @var array $this->_activeOrder */
    $this->_activeItems = $this->_cOrderItems->getOrderItems($this->_activeOrder['key']);
    /** @var array $this->_activeOrder */
    $this->_activeBilling = $this->_cAddress->get($this->_activeOrder['billing-address']);
    /** @var array $this->_activeOrder */
    $this->_activeShipping = $this->_cAddress->get($this->_activeOrder['shipping-address']);
    /** @var array the whole order */
    return [
      'Customer' => $this->_activeCustomer,
      'Order' => $this->_activeOrder,
      'OrderItems' => $this->_activeItems,
      'BillingAddress' => $this->_activeBilling,
      'ShippingAddress' => $this->_activeShipping
    ];
  }

  /**
   * Make an order from scratch and return the new order key
   * @method makeOrder
   * @param $customer
   * @param $first
   * @param $last
   * @param $phone
   * @param $session
   * @param $funnel
   * @param $campaign
   * @param $billing
   * @param $shipping
   * @param $test
   * @return mixed
   */
  public function makeOrder($customer, $first, $last, $phone, $session, $funnel, $campaign, $billing, $shipping, $test)
  {
    /** @var array _activeBilling Make an addres and store it in active billing address */
    $this->_activeBilling   = $this->_cAddress->makeAddress( $billing['name'], $billing['line-one'], $billing['line-two'], $billing['city'], $billing['state'], $billing['zip'] );
    /** @var array _activeShipping Make an address and store it in active shipping address */
    $this->_activeShipping  =  $this->_cAddress->makeAddress( $shipping['name'], $shipping['line-one'], $shipping['line-two'], $shipping['city'], $shipping['state'], $shipping['zip'] );
    /** @var array _activeOrder Make the order and store it in the active order object */
    $this->_activeOrder = $this->_mOrders->makeOrder(
      $customer, $first, $last, $phone, $session, $funnel, $campaign, self::created, $this->_activeBilling['key'], $this->_activeShipping['key'], $test
    );
    /** Add an order not */
    $this->_cOrderNotes->addOrderNote( $this->_activeOrder['key'], 0, 'Order Created' );
    return $this->_activeOrder['key'];
  }

  /**
   * Mark an order with the correct complete status
   * @markOrderCompleted
   * @param $orderKey
   * @return array
   */
  public function markOrderCompleted( $orderKey )
  {
    /** @var array $items */
    $items = $this->orderItems($orderKey);
    /** @var integer $totalItems */
    $totalItems = 0;
    /** @var integer $itemsPaidFor */
    $itemsPaidFor = 0;
    /** @var array $item Loop through all the order items */
    foreach($items as $item){
      /** Check if this item is reconciled */
      if($item['status']==\Framework\Commander\OrderItems::reconciled){
        /** Keep track of how many items were paid for */
        $itemsPaidFor++;
      }
      /** Keep track of the total items */
      $totalItems++;
    }
    /** Check if nothing has been paid for */
    if($itemsPaidFor==0){
      /** return false result saying order has no paid for items */
      return ['result'=>false,'messages'=>['Order cant complete no items paid for']];
    }
    /** Check if we have no items */
    if($totalItems==0){
      /** return order no items error */
      return ['result'=>false,'messages'=>['Order cant complete no items']];
    }
    /** Check if some items are still not paid for */
    if($itemsPaidFor<$totalItems){
      /** Mark the order as completed with balance */
      $this->_setOrderStatus($orderKey,self::completedWithBalance);
      /** return true */
      return ['result'=>true,'messages'=>['Completed with balance']];
    }
    /** Mark the order as completed */
    $this->_setOrderStatus($orderKey,self::completed);
    /** return true */
    return ['result'=>true,'messages'=>['Completed']];
  }

  /**
   * Reconsile all order additions update order totals and order status
   * @method reconcileOrderAdditions
   * @param $processorToken
   * @param $product
   * @return array
   */
  public function reconcileOrderAdditions($processorToken, $product)
  {
    if($this->_activeOrder['customer-key']==''){
      return ['result'=>false,'messages'=>['No Order Present']];
    }
    /** @var array $data Data object to send to the payment processor */
    $data = [
      'customer'      => $this->_activeOrder['customer-key'],
      'order'         => $this->_activeOrder['key'],
      'product'       => $product,
      'order-total'   => $this->_unpaidTotal($this->_activeOrder['key'])
    ];
    /** @var array $outcome Charge the customer defined in the data object */
    $outcome = $this->_cPaymentProcessor->charge( $data, $processorToken );
    /** Check for a false result */
    if(!$outcome['result']){
      /** We failed return the messages */
      return $outcome;
    }
    /** Mark the items as reconciled */
    $this->_cOrderItems->markOrderAsReconciled( $this->_activeOrder['key'] );
    /** Update the order status totals */
    $this->_setOrderTotals($this->_activeOrder['key']);
    /** Set the order status */
    $this->_setOrderStatus($this->_activeOrder['key'],self::charged);
    /** Return success */
    return ['result'=>true,'messages'=>[]];
  }

  /**
   * Take all the needed information and charge the customer for their
   * unpaid balance also updates the order items as paid and sets the order to
   * charged
   * @method reconcileOrder
   * @param $processorToken
   * @param $ccData
   * @param $product
   * @return array
   */
  public function reconcileOrder($processorToken, $ccData, $product)
  {
    $this->loadOrder($this->_activeOrder['key']);
    /** @var int $total Get the unpaid balance for the ordr */
    $total = $this->_unpaidTotal($this->_activeOrder['key']);
    /** Check that we have an order total */
    if($total >= 0){
      $this->loadOrder($this->_activeOrder['key']);
      $data = [
        'customer' => $this->_activeOrder['customer-key'],
        'order' => $this->_activeOrder['key'],
        'product' => $product,
        'order-total' => $total,
        'cc-data' => $ccData,
        'first-name' => $this->_activeOrder['first-name'],
        'last-name' => $this->_activeOrder['last-name'],
        'address-one' => $this->_activeBilling['address-line-one'],
        'address-two' => $this->_activeBilling['address-line-two'],
        'city' => $this->_activeBilling['city'],
        'state' => $this->_activeBilling['state'],
        'zip' => $this->_activeBilling['zip'],
        'phone-number' => $this->_activeOrder['phone-number'],
        'email-address' => $this->_activeCustomer['email-address']
      ];
      /** @var array $outcome use the data object given to try and charge the customer */
      $outcome = $this->_cPaymentProcessor->createCustomer($data, $processorToken);
      /** Check for a fail result */
      if(!$outcome['result']){
        /** check if the fail result is for an existing customer */
        if($outcome['messages'][1]!="Duplicate Customer Vault Id"){
          /** Return the error */
          return $outcome;
        }
        /** @var array $outcome run an update the customer already exists */
        $outcome = $this->_cPaymentProcessor->updateCustomer( $data, $processorToken );
        /** check for an error  */
        if(!$outcome['result']){
          /** Return the error something went wrong */
          return $outcome;
        }
      }
      /** @var array $outcome Customer done lets charge them */
      $outcome = $this->_cPaymentProcessor->charge($data, $processorToken);
      /** Check if we have an error */
      if(!$outcome['result']){
        /** We have an an error return it */
        return $outcome;
      }
      /** Mark order as reconciled */
      $this->_cOrderItems->markOrderAsReconciled( $this->_activeOrder['key'] );
      /** Update the order status totals */
      $this->_setOrderTotals($this->_activeOrder['key']);
      /** Set the order status */
      $this->_setOrderStatus($this->_activeOrder['key'],self::charged);
      /** We are done return the right result */
      $this->loadOrder($this->_activeOrder['key']);
      return ['result'=>true,'messages'=>[]];
    }
    return ['result'=>false,'messages'=>['Order has no billable balance']];
  }

  /**
   * FUNNEL USE ONLY
   * This is a shortcut to determine an orders status during funnel progression
   * @method _determineCorrectOrderStatus
   * @param $orderKey
   * @return bool|int
   */
  private function _determineCorrectOrderStatus($orderKey)
  {
    /** @var array $order */
    $order = $this->_mOrders->get($orderKey);
    /** Check that the order exists */
    if(!$order) {
      /** None existent orders don't have a status */
      return false;
    }
    /** @var array $orderItems array of the order items */
    $orderItems = $this->_cOrderItems->getOrderItems( $orderKey );
    /** Check that we have items */
    if(!$orderItems){
      /** Return the base status */
      return self::created;
    }
    /** @var integer $totalItems */
    $totalItems = 0;
    /** @var integer $itemsPaidFor */
    $itemsPaidFor = 0;
    /** @var array $item Loop through all the order items */
    foreach($orderItems as $item){
      /** Check if this item is reconciled */
      if($item['status']==\Framework\Commander\OrderItems::reconciled){
        /** Keep track of how many items were paid for */
        $itemsPaidFor++;
      }
      /** Keep track of the total items */
      $totalItems++;
    }
    /** Check if nothing is paid for */
    if($itemsPaidFor==0&&$totalItems==0){
      /** Return the created status */
      return self::created;
    }
    /** Check if nothing is paid for */
    if($itemsPaidFor==0){
      /** Return the created status */
      return self::charged;
    }
    /** Check if both totals are the same */
    if($totalItems==$itemsPaidFor){
      /** Return the complete status */
      return self::completed;
    }

    /** Check if items paid for is less than total items */
    if($itemsPaidFor<$totalItems){
      /** Return completed with balance */
      return self::completedWithBalance;
    }
    /** SHOULD NEVER HAPPEN */
    return false;
  }

  /**
   * Return the total dollar amount paid for the order
   * @method _unpaidTotal
   * @param null $orderKey
   * @return int
   */
  private function _unpaidTotal($orderKey = null)
  { return $this->_total($orderKey, \Framework\Commander\OrderItems::added); }

  /**
   * Return the total dollar amount paid for in the order
   * @method _paidTotal
   * @param null $orderKey
   * @return int
   */
  private function _paidTotal($orderKey = null)
  { return $this->_total($orderKey, \Framework\Commander\OrderItems::reconciled); }

  /**
   * Return the total base on the type and order number
   * This method is the worker for _unpaidTotal and _paidTotal
   * @method _total
   * @param $orderKey
   * @param $type
   * @return int
   */
  private function _total( $orderKey, $type )
  {
    /** @var array $items default the order items to the active order */
    $items = $this->_activeItems;
    /** Check if another key was passed */
    if( $orderKey != null ){
      /** @var array $items Load the requested order items */
      $items = $this->_cOrderItems->getOrderItems( $orderKey );
    }
    /** @var int $total default total to 0 */
    $total = 0;
    /** @var array $item Iterate over the items */
    foreach( $items as &$item ){
      /** Check if the item  */
      if($item['status'] == $type){
        /** status matches our type add it to the total */
        $total += $item['price'];
      }
    }
    /** return the total */
    return $total;
  }

  /**
   * Set a new order status awi
   * @method _setOrderStatus
   * @param $orderKey
   * @param $status
   */
  public function _setOrderStatus($orderKey, $status, $message=null)
  {
    /** First set the order status */
    $this->_mOrders->setOrderStatus($orderKey, $status);
    $note = $this->_statusMessages[$status];
    if(!is_null($message)){
      $note = $message;
    }
    /** Second set the order know to let people know when we did this */
    $this->_cOrderNotes->addOrderNote($orderKey, 0, $note);
  }

  /**
   * Set the order totals for an order
   * @method _setOrderTotals
   * @param null $orderKey
   * @return boolean
   */
  public function _setOrderTotals($orderKey = null)
  {
    /** @var array $items */
    $items = $this->orderItems($orderKey);
    /** Make sure we were given items */
    if($items===false){
      /** False was return no order found */
      return false;
    }
    /** @var integer $totalBefore $totalBefore */
    $totalBefore  = 0;
    /** @var integer $totalAfter */
    $totalAfter = 0;
    /** @var array $item  */
    foreach($items as $item){
      /** Check if the order status is reconsiled */
      if($item['status'] == \Framework\Commander\OrderItems::reconciled){
        /** Update the order totals  */
        $totalBefore += $item['price'];
        $totalAfter += ($item['price'] - $item['cost']);
      }
    }
    /** Save the updated totals to the order */
    $this->_mOrders->setTotals($orderKey, $totalBefore, $totalAfter);
  }
}
