<?php

namespace Framework\Modulus\Model;

/**
 * User
 *
 * This is your main interface to the user table
 */
class ProcessorTransactions extends \Framework\Modulus\Query
{
    /**
     * @param $Key
     * @return mixed
     */
    public function getForOrder( $Key )
    {
        $Query = "SELECT * FROM `funnel-order-transactions` WHERE `order-key`=:Key ORDER BY `timestamp` DESC";
        $PreparedStatement = $this->Prepare($Query);
        $PreparedStatement->bindValue(':Key', $Key, \PDO::PARAM_INT);
        $PreparedStatement = $this->Execute($PreparedStatement);
        return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
