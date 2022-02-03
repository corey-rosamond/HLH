<?php

namespace Framework\Modulus\Modal;


class Customer extends \Framework\Modulus\Query
{
  public function emailLookup($email)
  {
    $query = "SELECT `key`,`email-address`,`value-before-cost`,`value-after-cost`,`origin-date`,`origin-funnel` FROM `funnel-customers` WHERE `email-address`=:email";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':email', $email, \PDO::PARAM_STR);
    $preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  public function createCustomer($email,$funnel,$valueBefore,$valueAfter)
  {
    $query = "INSERT INTO `funnel-customers`
      (`email-address`,`value-before-cost`,`value-after-cost`,`origin-funnel`) VALUES
      (:email,:valueBefore,:valueAfter,:funnel)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':email', $email, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':valueBefore', $valueBefore, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':valueAfter', $valueAfter, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':funnel', $funnel, \PDO::PARAM_INT);
    $this->Execute( $preparedStatement );
		return $this->get($this->LastInsertID());
  }

  public function get($key)
  {
    $query = "SELECT `key`,`email-address`,`value-before-cost`,`value-after-cost`,`origin-date`,`origin-funnel` FROM `funnel-customers` WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  public function updateCustomerValue($key, $before, $after)
  {
    $query = "UPDATE `funnel-customers` SET `value-before-cost`=:before,`value-after-cost`=:after WHERE `key` = :key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key',     $key,     \PDO::PARAM_INT);
    $preparedStatement->bindValue(':before',  $before,  \PDO::PARAM_STR);
    $preparedStatement->bindValue(':after',   $after,   \PDO::PARAM_STR);
    return $this->Execute( $preparedStatement );
  }
}
