<?php

namespace Framework\Modulus\Modal;

class TrackingSessionsData extends \Framework\Modulus\Query
{
  public function addSessionData($sessionKey, $data)
  {
    $query = "INSERT INTO `funnel-sessions-data` (`funnel-session-key`,`data`) VALUES (:sessionKey, :data)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':sessionKey',  $sessionKey,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':data',        $data,        \PDO::PARAM_STR);
    $this->Execute($preparedStatement);
  }
}
