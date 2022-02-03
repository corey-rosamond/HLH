<?php
/**
 * User
 *
 * @package Framework\Modulus\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * User
 *
 * This is your main interface to the user table
 */
class Address extends \Framework\Modulus\Query
{
    /**
     * @param $AddressLineOne
     * @param $AddressLineTwo
     * @param $City
     * @param $State
     * @param $Zip
     * @return mixed
     */
    public function CreateAndReturn($AddressLineOne, $AddressLineTwo, $City, $State, $Zip)
    {
        $Query = "INSERT INTO `funnel-address` ( `address-line-one`, `address-line-two`, `city`, `state`, `zip` ) VALUES
                  ( :AddressLineOne, :AddressLineTwo, :City, :State, :Zip )";
        $PreparedStatement = $this->Prepare($Query);
        $PreparedStatement->bindValue(':AddressLineOne', $AddressLineOne, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':AddressLineTwo', $AddressLineTwo, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':City', $City, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':State', $State, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':Zip', $Zip, \PDO::PARAM_INT);
        $this->Execute($PreparedStatement);
        return $this->AddressByKey($this->LastInsertID());
    }
    /**
     * @param $Key
     * @param $AddressLineOne
     * @param $AddressLineTwo
     * @param $City
     * @param $State
     * @param $Zip
     * @return mixed
     */
    public function UpdateAndReturn($Key, $AddressLineOne, $AddressLineTwo, $City, $State, $Zip)
    {
        $Query = "Update `funnel-address` SET
                    `address-line-one`=:AddressLineOne,
                    `address-line-two`=:AddressLineTwo,
                    `city`=:City,
                    `state`=:State,
                    `zip`=:Zip
                    WHERE `key`=:Key";
        $PreparedStatement = $this->Prepare($Query);
        $PreparedStatement->bindValue(':AddressLineOne', $AddressLineOne, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':AddressLineTwo', $AddressLineTwo, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':City', $City, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':State', $State, \PDO::PARAM_STR);
        $PreparedStatement->bindValue(':Zip', $Zip, \PDO::PARAM_INT);
        $PreparedStatement->bindValue(':Key', $Key, \PDO::PARAM_INT);
        $this->Execute($PreparedStatement);
        return $this->AddressByKey($Key);
    }

    public function get( $key )
    {
      $Query = "SELECT * FROM `funnel-address` WHERE `key`=:Key";
      $PreparedStatement = $this->Prepare($Query);
      $PreparedStatement->bindValue(':Key', $key, \PDO::PARAM_STR);
      $PreparedStatement = $this->Execute($PreparedStatement);
      if ($PreparedStatement->rowCount() == 0) {return false;}
      return $PreparedStatement->fetch(\PDO::FETCH_ASSOC);
    }
}
