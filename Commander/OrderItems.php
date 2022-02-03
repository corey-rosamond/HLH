<?php

namespace Framework\Commander;

class OrderItems extends \Framework\Support\Abstracts\Singleton
{

  const added       = 0;
  const reconciled  = 1;
  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_mOrderItems = \Framework\Core\Receptionist::modal( "OrderItems", true );
    $instance->_cOrderNotes = \Framework\Core\Receptionist::controller( 'OrderNotes', true );
    $instance->_cProducts   = \Framework\Core\Receptionist::controller( 'Products', true );
    return $instance;
  }

  public function getOrderItems( $order )
  { return $this->_mOrderItems->getOrderItems( $order ); }

  public function addProductToOrder( $order, $product, $status=self::added )
  {
    $pData = $this->_cProducts->getProduct( $product );
    $this->_mOrderItems->addToOrder($order, $pData['key'], $pData['name'], $pData['price'],$pData['cost'], $pData['is-digital'], $status );
    $this->_cOrderNotes->addOrderNote( $order, 0, "Order Item Added: Name: {$pData['name']}<sup>({$pData['key']})</sup>" );
    return true;
  }
  
  public function setStatus($key, $status)
  { return $this->_mOrderItems->setStatus($key, $status); }



  public function markOrderAsReconciled($order)
  {
    $items = $this->_mOrderItems->getOrderItems( $order );
    foreach($items as $item){
      if($item['status']==self::added){
        $this->_mOrderItems->setStatus($item['key'],self::reconciled);
        $this->_cOrderNotes->addOrderNote( $order, 0, "Order Item Marked as Reconciled: {$item['name']}<sup>({$item['key']})</sup>" );
      }
    }
    return true;
  }
}
