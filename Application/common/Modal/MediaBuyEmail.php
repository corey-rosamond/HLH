<?php
/**
 * MediaBuyEmail
 *
 * @package common\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * MediaBuyEmail
 *
 * This model is holds all querys related to the Email part of media buys
 */
class MediaBuyEmail extends \Framework\Modulus\Query
{

  /**
   * getPromoters
   *
   * get a list of promoters for the user currently logged in
   * @param  $key key for the user currently logged in
   * @return false|\PDOResource
   * @access public
   */
  public function getPromoters( $userKey )
  {
      $query = "SELECT * FROM `mediabuy-promoters` WHERE `owner-key`=:key ORDER BY `name` DESC";
      $preparedStatement = $this->Prepare($query);
      $preparedStatement->bindValue(':key', $userKey, \PDO::PARAM_INT);
      $preparedStatement = $this->Execute($preparedStatement);
      if ($preparedStatement->rowCount() == 0) {return false;}
      return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

    /**
     * getLinks
     *
     * get all the tracking links for a promoter and user
     * @param int $promoterKey the key for the promoter we are selecting on
     * @param int $userKey the user key we are selecting on
     * @return false|PDOResource
     * @access public
     */
    public function getLinks( $promoterKey, $ownerKey ){
        $query = "SELECT
                `key`,
                `name`,
                DATE_FORMAT(`start-date`,'%W, %M %e') as `start-date`,
                DATE_FORMAT(`end-date`,'%W, %M %e') as `end-date`,
                CONCAT('$',FORMAT(`cost`,2)),
                CONCAT('$',FORMAT(`expected-return`,2))
            FROM `mediabuy-email-links`
            WHERE `promoter-key` = :promoterKey
            AND `owner-key` = :ownerKey
            ORDER BY `name` DESC";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':promoterKey', $promoterKey, \PDO::PARAM_INT);
        $preparedStatement->bindValue(':ownerKey', $ownerKey, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() == 0) {return false;}
        return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * getLinkData
     *
     * Get all needed data to alter or update this link
     * @param int $key the key for this link
     * @return false|PDOResult
     * @access public
     */
    public function getLinkData( $key ){
        $query = "SELECT
            `owner-key`, `promoter-key`, `funnel-key`, `name`, `key`,
            FORMAT(`cost`,2) as `cost`,
            FORMAT(`expected-return`,2) as `expected-return`,
            DATE_FORMAT(`start-date`, '%m-%d-%Y') as `start-date`,
            DATE_FORMAT(`end-date`, '%m-%d-%Y') as `end-date`
            FROM `mediabuy-email-links` WHERE `key`=:key";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() == 0) {return false;}
        return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * getFunnels
     *
     * Get a list of the current site funnels
     * @return PDOResult
     * @access public
     */
    public function getFunnels( ){
        $query = "SELECT * FROM `funnels` ORDER BY `name` ASC";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() == 0) {return false;}
        return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * getAllPromoters
     *
     * get a full list of all of the promoters
     * @return false|PDOException object
     * @access public
     */
    public function getAllPromoters(){
        $query = "SELECT * FROM `mediabuy-promoters` ORDER BY `name` DESC";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':key', $userKey, \PDO::PARAM_INT);
        $preparedStatement = $this->Execute($preparedStatement);
        if ($preparedStatement->rowCount() == 0) {return false;}
        return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * upadateEmailLink
     *
     * Update all the information for the email link in the database
     * @param string $name name of link
     * @param int $promoterKey key of the promoter
     * @param int $funnelkey key of the funnelkey
     * @param double $cost cost for this campaign
     * @param double $expectedreturn expected return value
     * @param timestamp $startdate expected date this link will be SoapClient
     * @param timestamp $enddate  expected date traffic ends from this link
     * @return boolean did it work?
     * @access public
     */
    public function updateEmailLink( $name, $promoterkey, $funnelkey, $cost, $expectedreturn, $startdate, $enddate, $key ){
        $query = 'UPDATE `mediabuy-email-links` SET
            `name`=:name, `promoter-key`=:promoterkey, `funnel-key`=:funnelkey, `cost`=:cost, `expected-return`=:expectedreturn,
            `start-date`=:startdate, `end-date`=:enddate WHERE `key`=:key';
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':name', $name, \PDO::PARAM_STR);
        $preparedStatement->bindValue(':promoterkey', $promoterkey, \PDO::PARAM_INT);
        $preparedStatement->bindValue(':funnelkey', $funnelkey, \PDO::PARAM_INT);
        $preparedStatement->bindValue(':cost', $cost, \PDO::PARAM_STR);
        $preparedStatement->bindValue(':expectedreturn', $expectedreturn, \PDO::PARAM_STR);
        $preparedStatement->bindValue(':startdate', $startdate, \PDO::PARAM_STR);
        $preparedStatement->bindValue(':enddate', $enddate, \PDO::PARAM_STR);
        $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
        return $this->Execute($preparedStatement);
    }

}
