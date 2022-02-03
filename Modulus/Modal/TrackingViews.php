<?php

namespace Framework\Modulus\Modal;

class TrackingViews extends \Framework\Modulus\Query
{
  /**
   * Add a page view to a session
   * @method addView
   * @param $sessionKey
   * @param $pageKey
   * @param $loadTime
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function addView($sessionKey, $pageKey, $loadTime)
  {
    $query = "INSERT INTO `funnel-session-views` (`funnel-session-key`, `funnel-page-key`, `load-time`) VALUES (:sessionKey, :pageKey, :loadTime)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':sessionKey',  $sessionKey,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':pageKey',     $pageKey,     \PDO::PARAM_INT);
    $preparedStatement->bindValue(':loadTime',    $loadTime,    \PDO::PARAM_STR);
    $this->Execute($preparedStatement);
  }

  /**
   * Generate page view counts for a funnel and campaign
   * @method funnelCampaignPageViews
   * @param $startDate
   * @param $endDate
   * @param $funnelKey
   * @param $emailLinkKey
   * @return mixed
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function funnelCampaignPageViews($startDate, $endDate, $funnelKey, $emailLinkKey)
  {
    $query = "
      SELECT
        `fsv`.`funnel-page-key` as `page`,
        count(`fsv`.`key`) as `views`
      FROM `funnel-sessions` AS `fs`
      LEFT JOIN `funnel-session-views` AS `fsv`
      ON `fs`.`key`=`fsv`.`funnel-session-key`
      WHERE `fs`.`funnel-key`=:funnelKey
      AND `fs`.`campaign-key`=:emailLinkKey
      AND `fs`.`time-stamp` > DATE_SUB(NOW(),INTERVAL 5 DAY)
      GROUP BY `fsv`.`funnel-page-key`";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':funnelKey', $funnelKey, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':emailLinkKey', $emailLinkKey, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }
}
