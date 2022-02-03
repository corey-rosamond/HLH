<?php
/**
 * MediaBuyEmailLinks
 *
 * @package Framework\Modulus\Modal
 * @version 1.2.0
 */
namespace Framework\Modulus\Modal;

/**
 * MediaBuyEmailLinks
 *
 * ALl of the media buy email link queries
 */
class MediaBuyEmailLinks extends \Framework\Modulus\Query
{
  /**
   * Check if the campaign exists
   * @method campaignExists
   * @param $funnelKey
   * @param $campaignKey
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function campaignExists($funnelKey, $campaignKey)
  {
    $query = "SELECT count(`key`) as `total` FROM `mediabuy-email-links` WHERE `key`=:campaignKey AND `funnel-key`=:funnelKey";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':funnelKey',   $funnelKey,   \PDO::PARAM_INT);
    $preparedStatement->bindValue(':campaignKey', $campaignKey, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
    if(intval($resource['total'])===0){
      return false;
    }
    return true;
  }

  /**
   * Get link key and name for a specified funnel
   * @method getFunnelLinksKeyAndName
   * @param $funnelKey
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function getFunnelLinksKeyAndName($funnelKey)
  {
    $query = "SELECT `key`, `name` FROM `mediabuy-email-links` WHERE `funnel-key`=:funnelKey";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':funnelKey',   $funnelKey,   \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if($preparedStatement->rowCount()<=0){
      return false;
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Search for a link via a search term
   * @method linkSearch
   * @param $term
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function linkSearch($term){
    $query = "
      SELECT
        `l`.`key` as `id`,
        concat( `l`.`name`,': ', `p`.`name`) AS `text`
      FROM `mediabuy-email-links` AS `l`,
      `mediabuy-promoters` AS `p`
      WHERE `l`.`promoter-key` = `p`.`key`
      AND (CONCAT(`p`.`name`, ' ', `l`.`name`) LIKE :term OR `l`.`key` LIKE :termtwo)";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':term', "%$term%", \PDO::PARAM_STR);
    $preparedStatement->bindValue(':termtwo', "%$term%", \PDO::PARAM_STR);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount()<=0){
      return false;
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * getLinkName
   * @method getLinkName
   * @param  int              $key The key for this link
   * @return false|PDOResult
   * @access public
   */
  public function getLinkName($key){
    $query = "SELECT `key` as `id`, `name` as `text` FROM `mediabuy-email-links` WHERE `key` = :key ";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount()<=0){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }
}
