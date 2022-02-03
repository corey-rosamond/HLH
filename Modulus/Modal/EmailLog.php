<?php

namespace Framework\Modulus\Modal;


class EmailLog extends \Framework\Modulus\Query
{
  public function logSend($to, $from, $subject, $message, $funnelKey, $customerKey, $orderKey, $company, $sessionKey)
  {
    $query = "INSERT INTO `email-log` (`to`,`from`,`subject`,`message`,`funnel-key`,`customer-key`,`order-key`,`company`, `session-key`) VALUES
      (:to,:from,:subject,:message,:funnelKey,:customerKey,:orderKey,:company,:sessionKey)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':to',          $to,          \PDO::PARAM_STR);
    $preparedStatement->bindValue(':from',        $from,        \PDO::PARAM_STR);
    $preparedStatement->bindValue(':subject',     $subject,     \PDO::PARAM_STR);
    $preparedStatement->bindValue(':message',     $message,     \PDO::PARAM_STR);
    $preparedStatement->bindValue(':funnelKey',   $funnelKey,   \PDO::PARAM_INT);
    $preparedStatement->bindValue(':customerKey', $customerKey, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':orderKey',    $orderKey,    \PDO::PARAM_INT);
    $preparedStatement->bindValue(':company',     $company,     \PDO::PARAM_INT);
    $preparedStatement->bindValue(':sessionKey',  $sessionKey,  \PDO::PARAM_INT);
    $this->Execute( $preparedStatement );
		return $this->LastInsertID();
  }

}

//`key`,`to`,`from`,`subject`,`message`,`funnel-key`,`customer-key`,`order-key`,`timestamp`,`company`
