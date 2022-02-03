<?php

namespace Framework\Commander;

class Customer extends \Framework\Commander\Commander
{

  private $_mOrders;

  private $_mCustomer;


  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_mCustomer = \Framework\Core\Receptionist::modal( "Customer", true );
    $instance->_cOrders   = \Framework\Commander\Orders::getInstance();
    return $instance;
  }

  public function get( $key )
  { return $this->_mCustomer->get( $key ); }


  public function emailLookup( $email )
  { return $this->_mCustomer->emailLookup( $email ); }

  public function customerOrders( $customerKey )
  {
    $this->_mOrders   = \Framework\Core\Receptionist::controller("Orders", true);
    return $this->_mOrders->customerOrders( $customerKey );
  }

  public function createCustomer( $email, $funnel, $valueBefore=0, $valueAfter=0 )
  { return $this->_mCustomer->createCustomer($email,$funnel,$valueBefore,$valueAfter); }


  public function updateCustomerValue($key)
  {
    $orders = $this->_cOrders->customerOrders($key);
    $tBefore  = 0;
    $tAfter   = 0;
    foreach($orders as $order){
      if(
        $order['status']==\Framework\Commander\Orders::completed ||
        $order['status']==\Framework\Commander\Orders::completedWithBalance
      ){
        $tBefore += $order['order-total-before'];
        $tAfter += ($order['order-total-before'] - $order['order-total-after']);
      }
    }
    return $this->_mCustomer->updateCustomerValue($key, $tBefore, $tAfter);
  }
}
