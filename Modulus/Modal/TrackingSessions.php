<?php

namespace Framework\Modulus\Modal;

class TrackingSessions extends \Framework\Modulus\Query
{
  public function makeSession($funnelKey, $campaignKey, $ipAddress)
  {
    $query = "INSERT INTO `funnel-sessions` (`ip-address`,`funnel-key`,`campaign-key`) VALUES (INET6_ATON(:ipAddress), :funnelKey, :campaignKey)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':funnelKey',   $funnelKey,   \PDO::PARAM_INT);
    $preparedStatement->bindValue(':campaignKey', $campaignKey, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':ipAddress',   $ipAddress,   \PDO::PARAM_LOB);
    $this->Execute($preparedStatement);
    return $this->LastInsertID();
  }
}
