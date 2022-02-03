<?php
/**
 * A PERFECT EXAMPLE OF THE WRONG WAY FOR SPEED
 */
namespace Framework\Modulus\Modal;

class Complemar extends \Framework\Modulus\Query
{
  public function GetAddress( $Key ){
		$Query = "SELECT * FROM `funnel-address` WHERE `key`=:Key";
    $PreparedStatement = $this->Prepare($Query);
		$PreparedStatement->bindValue(':Key', $Key, \PDO::PARAM_INT);
    $PreparedStatement = $this->Execute($PreparedStatement);
    return $PreparedStatement->fetch(\PDO::FETCH_ASSOC);
	}
  public function GetOrderItems( $Key ){
		$Query = "SELECT
				`funnel-products`.`sku`,
				`funnel-order-items`.*
			FROM `funnel-order-items`
			left join `funnel-products`
			ON `funnel-order-items`.`product-key`=`funnel-products`.`key`
			WHERE `funnel-order-items`.`order-key`=:Key;";
    $PreparedStatement = $this->Prepare($Query);
		$PreparedStatement->bindValue(':Key', $Key, \PDO::PARAM_INT);
    $PreparedStatement = $this->Execute($PreparedStatement);
    return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
	}
	public function GetOrder( $Key ){
		$Query = "select * from `funnel-orders` WHERE `key`=:Key ";
    $PreparedStatement = $this->Prepare( $Query );
		$PreparedStatement->bindValue(':Key', $Key, \PDO::PARAM_INT );
    $PreparedStatement = $this->Execute( $PreparedStatement );
    return $PreparedStatement->fetch( \PDO::FETCH_ASSOC );
	}
	public function GetByStatus( $Status ){
		$Query = "SELECT * FROM `funnel-orders` WHERE `status`=:Status ORDER BY `order-date` DESC";
    $PreparedStatement = $this->Prepare($Query);
		$PreparedStatement->bindValue(':Status', $Status, \PDO::PARAM_STR);
    $PreparedStatement = $this->Execute($PreparedStatement);
    if($PreparedStatement->rowCount()<=0){ return false; }
    return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
	}
	public function GetRecent(){
		$Query = "SELECT * FROM `funnel-orders` ORDER BY `order-date` DESC limit 0,10";
    $PreparedStatement = $this->Prepare($Query);
    $PreparedStatement = $this->Execute($PreparedStatement);
    if($PreparedStatement->rowCount()<=0){ return false; }
    return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
	}
	public function updateStatus($key, $status){
		$Query = "update `funnel-orders` set `status`=:status WHERE `key`=:key";
		$PreparedStatement = $this->Prepare($Query);
		$PreparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
		$PreparedStatement->bindValue(':status', $status, \PDO::PARAM_STR);
		return $this->Execute($PreparedStatement);
	}
	public function updateFulfillment($key, $fulfillmentkey){
		$Query = "update `funnel-orders` set `fulfillment-key`=:fulfillmentkey WHERE `key`=:key";
		$PreparedStatement = $this->Prepare($Query);
		$PreparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
		$PreparedStatement->bindValue(':fulfillmentkey', $fulfillmentkey, \PDO::PARAM_STR);
		return $this->Execute($PreparedStatement);
	}
  public function Search( $Needle ){
		$Query = "
			SELECT
				`order`.`key` as `order-key`,
				`customer`.`key` as `customer`,
				`order`.*,
				`customer`.*
			FROM `customer`
			LEFT JOIN `order`
			ON `customer`.`key`=`order`.`customer-key`
			WHERE `customer`.`email-address`=:EmailAddress
			OR CONCAT(`order`.`first-name`,' ',`order`.`last-name`) LIKE :Name
			OR `order`.`key`=:OrderKey
			ORDER BY `order`.`order-date` DESC
		";
		$PreparedStatement = $this->Prepare($Query);
		$PreparedStatement->bindValue(':EmailAddress', $Needle, \PDO::PARAM_STR);
		$PreparedStatement->bindValue(':Name', '%'.$Needle.'%', \PDO::PARAM_STR);
		$PreparedStatement->bindValue(':OrderKey', $Needle, \PDO::PARAM_STR);
        $PreparedStatement = $this->Execute($PreparedStatement);
        return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
	}

}
