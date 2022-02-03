<?php

namespace Framework\Modulus\Modal;


class FunnelException extends \Framework\Modulus\Query
{

  /**
   * Write a funnel exception log entry
   * @method write
   * @param $exceptionname
   * @param $message
   * @param $code
   * @param $severity
   * @param $file
   * @param $line
   * @param $exception
   * @param $previousexception
   * @param $sessionKey
   * @return mixed
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function write($exceptionname, $message, $code, $severity, $file, $line, $exception, $previousexception, $sessionKey, $clientip, $host)
  {
    $query = "INSERT INTO `funnel-exception`
      (`exception-name`, `message`, `code`, `severity`, `file`, `line`, `exception`, `previous-exception`, `session-key`, `client-ip`, `host`)
      VALUES (:exceptionname, :message, :code, :severity, :file, :line, :exception, :previousexception, :sessionKey,:clientIp,:host)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':exceptionname', $exceptionname, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':message', $message, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':code', $code, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':severity', $severity, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':file', $file, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':line', $line, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':exception', $exception, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':previousexception', $previousexception, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':sessionKey', $sessionKey, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':clientIp', $clientip, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':host', $host, \PDO::PARAM_STR);
    return $this->Execute($preparedStatement);
  }
}
