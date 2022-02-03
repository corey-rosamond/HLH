<?php
/**
 * User
 *
 * @package Framework\Modulus\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Model;

/**
 * User
 *
 * This is your main interface to the user table
 */
class OrderItem extends \Framework\Modulus\Query
{

    /**
     * @param $Key
     * @return mixed
     */
    public function GetOrder($Key)
    {
        $Query = "
            SELECT `product`.`sku`, `order-item`.*
            FROM `funnel-order-items` left join `product`
            ON `order-item`.`product-key`=`product`.`key` WHERE `order-item`.`order-key`=:Key;";
        $PreparedStatement = $this->Prepare($Query);
        $PreparedStatement->bindValue(':Key', $Key, \PDO::PARAM_INT);
        $PreparedStatement = $this->Execute($PreparedStatement);
        return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
