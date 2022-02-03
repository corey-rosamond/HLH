<?php
/**
 * Customer
 *
 * @package Framework\Modulus\Modal
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * Customer
 *
 * This is your main interface to the user table
 */
class Customer extends \Framework\Modulus\Query
{

  public function get( $key )
  {
    $Query = "SELECT * FROM `funnel-customers` WHERE `key`=:Key";
    $PreparedStatement = $this->Prepare( $Query );
    $PreparedStatement->bindValue( ':Key', $key, \PDO::PARAM_STR );
    $PreparedStatement = $this->Execute( $PreparedStatement );
    if($PreparedStatement->rowCount() == 0){
      return false;
    }
    return $PreparedStatement->fetch( \PDO::FETCH_ASSOC );
  }


  public function CustomerByEmail($EmailAddress)
  {
    $Query = "SELECT * FROM `funnel-customers` WHERE `email-address`=:EmailAddress";
    $PreparedStatement = $this->Prepare($Query);
    $PreparedStatement->bindValue(':EmailAddress', $EmailAddress, \PDO::PARAM_STR);
    $PreparedStatement = $this->Execute($PreparedStatement);
    if($PreparedStatement->rowCount() == 0){
      return false;
    }
    return $PreparedStatement->fetch(\PDO::FETCH_ASSOC);
  }


  public function CreateAndReturn( $EmailAddress, $ValueBeforeCost, $ValueAfterCost, $OriginSite, $OriginFunnel )
  {
    $Query = "INSERT INTO `funnel-customers` ( `email-address`, `value-before-cost`, `value-after-cost`, `origin-site`, `origin-funnel` ) VALUES
              ( :EmailAddress, :ValueBeforeCost, :ValueAfterCost, :OriginSite, :OriginFunnel )";
    $PreparedStatement = $this->Prepare($Query);
    $PreparedStatement->bindValue(':EmailAddress', $EmailAddress, \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':ValueBeforeCost', $ValueBeforeCost, \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':ValueAfterCost', $ValueAfterCost, \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':OriginSite', $OriginSite, \PDO::PARAM_INT);
    $PreparedStatement->bindValue(':OriginFunnel', $OriginFunnel, \PDO::PARAM_INT);
    $this->Execute( $PreparedStatement );
    return $this->CustomerByKey( $this->LastInsertID() );
  }

  public function CustomerByKey($Key)
  {
    $Query = "SELECT * FROM `funnel-customers` WHERE `key`=:Key";
    $PreparedStatement = $this->Prepare( $Query );
    $PreparedStatement->bindValue( ':Key', $Key, \PDO::PARAM_STR );
    $PreparedStatement = $this->Execute( $PreparedStatement );
    if($PreparedStatement->rowCount() == 0){
      return false;
    }
    return $PreparedStatement->fetch( \PDO::FETCH_ASSOC );
  }

  public function getCustomerEmail( $key )
  {
    $Query = "SELECT `funnel-customers` FROM `customer` WHERE `key`=:Key";
    $PreparedStatement = $this->Prepare($Query);
    $PreparedStatement->bindValue( ':Key', $key, \PDO::PARAM_STR );
    $PreparedStatement = $this->Execute( $PreparedStatement );
    if ($PreparedStatement->rowCount() == 0) {
      return false;
    }
    $resource = $PreparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource['email-address'];
  }
}
