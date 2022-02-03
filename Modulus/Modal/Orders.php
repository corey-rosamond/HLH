<?php
/**
 * Orders
 *
 * @package Framework\Modulus\Orders
 * @version 1.2.0
 */
namespace Framework\Modulus\Modal;

/**
 * Orders
 *
 * This should contain all queries against the order table
 */
class Orders extends \Framework\Modulus\Query
{

  /**
   * Get a list all order records for a specific customer
   * @method customerOrders
   * @param $customerKey
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function customerOrders($customerKey)
  {
    $query = "SELECT * FROM `funnel-orders` WHERE `customer-key`=:customerKey";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':customerKey', $customerKey, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Get everything in a specific order row
   * @method get
   * @param $key
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function get($key)
  {
    $query = "SELECT * FROM `funnel-orders` WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * Make a new order
   * @method makeOrder
   * @param $customer
   * @param $first
   * @param $last
   * @param $phone
   * @param $session
   * @param $funnel
   * @param $campaign
   * @param $status
   * @param $billing
   * @param $shipping
   * @param $test
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function makeOrder($customer, $first, $last, $phone, $session, $funnel, $campaign, $status, $billing, $shipping, $test)
  {
    $query = "INSERT INTO `funnel-orders`
      (`customer-key`,`first-name`,`last-name`,`phone-number`,`session-key`,`funnel-key`,`campaign-key`,`status`,`billing-address`,`shipping-address`,`is-test`) VALUES
      (:customer,:first,:last,:phone,:session,:funnel,:campaign,:status,:billing,:shipping,:test)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':customer',  $customer,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':first',     $first,     \PDO::PARAM_STR);
    $preparedStatement->bindValue(':last',      $last,      \PDO::PARAM_STR);
    $preparedStatement->bindValue(':phone',     $phone,     \PDO::PARAM_STR);
    $preparedStatement->bindValue(':session',   $session,   \PDO::PARAM_INT);
    $preparedStatement->bindValue(':funnel',    $funnel,    \PDO::PARAM_INT);
    $preparedStatement->bindValue(':campaign',  $campaign,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':status',    $status,    \PDO::PARAM_STR);
    $preparedStatement->bindValue(':billing',   $billing,   \PDO::PARAM_INT);
    $preparedStatement->bindValue(':shipping',  $shipping,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':test',      $test,      \PDO::PARAM_INT);
    $this->Execute( $preparedStatement );
    return $this->get($this->LastInsertID());
  }

  /**
   * Set the order status
   * @method setOrderStatus
   * @param $order
   * @param $status
   * @return mixed
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function setOrderStatus( $order, $status )
  {
    $query = "UPDATE `funnel-orders` SET `status`=:status WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key',     $order,   \PDO::PARAM_INT);
    $preparedStatement->bindValue(':status',  $status,  \PDO::PARAM_INT);
    return $this->Execute( $preparedStatement );
  }

  /**
   * Set order-total-before and order-total-after values for a specific order
   * @method setTotals
   * @param $key
   * @param $before
   * @param $after
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function setTotals( $key, $before, $after )
  {
    $query = "UPDATE `funnel-orders` SET `order-total-before`=:before, `order-total-after`=:after WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key',     $key,     \PDO::PARAM_INT);
    $preparedStatement->bindValue(':before',  $before,  \PDO::PARAM_STR);
    $preparedStatement->bindValue(':after',   $after,   \PDO::PARAM_STR);
    $this->Execute( $preparedStatement );
  }

  public function getAbandonedOrders()
  {
    $query = "select `key` from `funnel-orders` WHERE `order-date`< DATE_SUB(NOW(),INTERVAL 1 DAY) AND `status` = :charged  AND `is-test`=0";
    //$query = "select `key` from `funnel-orders` WHERE `key`= 11349";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':charged', \Framework\Commander\Orders::charged, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() <= 0) {
      return false;
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Return all of the orders that are ready to go to fulfillment
   * @method ordersReadyForFulfillment
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function ordersReadyForFulfillment()
  {
    $query = "select `key` from `funnel-orders` WHERE `order-date`< DATE_SUB(NOW(),INTERVAL 1 DAY) AND`status` IN(:completed, :completedWithBalance) AND `is-test`=0";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':completed', \Framework\Commander\Orders::completed, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':completedWithBalance', \Framework\Commander\Orders::completedWithBalance, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() <= 0) {
      return false;
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Set the complemar transaction key so we can refer back to this order
   * @method setComplemarTransactionKey
   * @param $orderKey
   * @param $transKey
   * @return mixed
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function setComplemarTransactionKey($orderKey, $transKey)
  {
    $query = "UPDATE `funnel-orders` SET `fulfillment-key`=:transKey WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':transKey', $transKey, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':key', $orderKey, \PDO::PARAM_INT);
    return $this->Execute( $preparedStatement );
  }

  // TEMPORARY

  public function getOrdersWithStatusAndKey()
  {
    $query = "select `key`, `status` from `funnel-orders` order by `key` desc";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement = $this->Execute($preparedStatement);
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getMatureOrders(){
    $query = "SELECT `key` FROM `funnel-orders` WHERE `is-test`=0
        AND `order-total-before`>0 AND `order-date` <
        DATE_SUB(NOW(),INTERVAL 24 HOUR) AND `status`
        IN('Created','Captured') limit 10";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() <= 0) {return false;}
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function setOrderApproved($key){
    $query = "UPDATE `funnel-orders` set `status`='Approved' WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
  }


}
