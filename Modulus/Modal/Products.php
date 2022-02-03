<?php

namespace Framework\Modulus\Modal;

class Products extends \Framework\Modulus\Query
{
  public function getProduct( $key )
  {
    $query = "SELECT `key`,`sku`,`name`,`description`,`price`,`cost`,`weight`,`is-digital`,`image` FROM `funnel-products` WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if($preparedStatement->rowCount() <= 0){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }


  /**
   * Get a list of products associated with an order
   * @method getOrderProducts
   * @param $orderKey
   * @return boolean
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function getOrderProducts($orderKey)
  {
    $query = "SELECT `fp`.* FROM
      `funnel-products` as `fp`
      RIGHT JOIN `funnel-order-items` as `oi`
      ON `fp`.`key` = `oi`.`product-key`
      WHERE `oi`.`order-key`=:key
      AND `oi`.`status`=1";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $orderKey, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if($preparedStatement->rowCount() <= 0){
      return false;
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getProducts($limit = 50)
  {
    //$query = "SELECT `sku`, `name`, `description`, `price`, `cost`, `weight`, `is-digital` FROM `funnel-products` LIMIT 0, 50";
    $query = "SELECT `key`, `sku`, `name`, `description`, `price`, `cost`, `weight`, `is-digital` FROM `funnel-products` LIMIT 0, $limit";
    $preparedStatement = $this->Prepare( $query );
    //$preparedStatement = $this->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $preparedStatement =$this->Execute($preparedStatement);
    if($preparedStatement->rowCount()  == 0){
      return false;
    }
    $resultArray = $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);

    return $resultArray;
  }
}
