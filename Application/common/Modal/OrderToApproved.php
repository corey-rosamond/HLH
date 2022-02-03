<?php
/**
 * OrderToApproved
 *
 * @package Framework\Modulus\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Model;

/**
 * OrderToApproved
 *
 * All the querys needed for the campaignKeyFixer
 */
class OrderToApproved extends \Framework\Modulus\Query
{
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
