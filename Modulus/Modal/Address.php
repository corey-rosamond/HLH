<?php

namespace Framework\Modulus\Modal;

class Address extends \Framework\Modulus\Query
{
  public function get( $key )
  {
    $Query = "SELECT * FROM `funnel-address` WHERE `key`=:Key";
    $PreparedStatement = $this->Prepare($Query);
    $PreparedStatement->bindValue(':Key', $key, \PDO::PARAM_STR);
    $PreparedStatement = $this->Execute($PreparedStatement);
    if ($PreparedStatement->rowCount() == 0) {return false;}
    return $PreparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  public function makeAddress( $name, $lineOne, $lineTwo, $city, $state, $zip)
  {
    $query = "INSERT INTO `funnel-address`
      (`name`,`address-line-one`,`address-line-two`,`city`,`state`,`zip`) VALUES
      (:name, :lineOne, :lineTwo, :city, :state, :zip)";
    $PreparedStatement = $this->Prepare($query);
    $PreparedStatement->bindValue(':name',    $name,    \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':lineOne', $lineOne, \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':lineTwo', $lineTwo, \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':city',    $city,    \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':state',   $state,   \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':zip',     $zip,     \PDO::PARAM_STR);
    $PreparedStatement = $this->Execute($PreparedStatement);
    return $this->get($this->LastInsertID());
  }
}
