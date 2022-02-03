<?php

namespace Framework\Modulus\Modal;


class OrderItems extends \Framework\Modulus\Query
{


  public function addToOrder($order, $product, $name, $price, $cost, $digital, $status )
  {
    $query = "INSERT INTO `funnel-order-items`
      (`order-key`,`product-key`,`name`,`price`,`cost`,`is-digital`,`status`) VALUES
      (:order,:product,:name,:price,:cost,:digital,:status)";
      $preparedStatement = $this->Prepare($query);
      $preparedStatement->bindValue(':order',   $order,   \PDO::PARAM_INT);
      $preparedStatement->bindValue(':product', $product, \PDO::PARAM_INT);
      $preparedStatement->bindValue(':name',    $name,    \PDO::PARAM_INT);
      $preparedStatement->bindValue(':price',   $price,   \PDO::PARAM_INT);
      $preparedStatement->bindValue(':cost',    $cost,    \PDO::PARAM_INT);
      $preparedStatement->bindValue(':digital', $digital, \PDO::PARAM_INT);
      $preparedStatement->bindValue(':status',  $status,  \PDO::PARAM_INT);
      return $this->Execute( $preparedStatement );
  }

  public function setStatus($key, $status)
  {
    $query = "UPDATE `funnel-order-items` SET `status`=:status WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':status',  $status,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':key',     $key,     \PDO::PARAM_INT);
    return $this->Execute( $preparedStatement );
  }

  public function getOrderItems( $order )
  {
    $query = 'SELECT * FROM `funnel-order-items` WHERE `order-key`=:order';
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':order', $order, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return [];
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }
}
