<?php
/**
 * CampaignKeyFixer
 *
 * @package Framework\Modulus\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Model;

/**
 * CampaignKeyFixer
 *
 * All the querys needed for the campaignKeyFixer
 */
class CampaignKeyFixer extends \Framework\Modulus\Query
{
    /**
     * getBatch
     *
     * get the next batch of records that need to be fixed
     * @return false|PDOResource
     * @access public
     */
    public function getBatch($start)
    {
        $query = "SELECT
                `key`, `session-key`, `order-date`
            FROM `funnel-orders`
            WHERE `campaign-key`=0
            AND `order-date`>:start
            AND `key`>10000
            AND `is-test` = 0
            AND `order-total-before`!=0
            ORDER BY `key`";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':start', $start, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() <= 0) {return false;}
        return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getIpAddress($key)
    {
        $query = "SELECT `ip-address` as 'ip-address' FROM `funnel-sessions` WHERE `key`=:key";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() <= 0) {return false;}
        $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
        return $resource['ip-address'];
    }

    public function findMatch($key, $ipAddress)
    {
        $query = "SELECT `key` FROM `funnel-sessions`
            WHERE `ip-address`=:ipAddress AND `key`!=:key";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':ipAddress', $ipAddress, \PDO::PARAM_LOB);
        $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() <= 0) {return false;}
        $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
        return $resource['key'];
    }

    public function getSessionData($key)
    {
        $query = "SELECT `data` FROM `funnel-sessions-data` WHERE `funnel-session-key`!=:key";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() <= 0) {return false;}
        $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
        return $resource['data'];
    }

    public function updateCampaignKey($key, $campaignKey)
    {
        $query = "UPDATE `funnel-orders` set `campaign-key`=:campaignKey WHERE `key`=:key";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':campaignKey', $campaignKey, \PDO::PARAM_INT);
        $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
    }

}
