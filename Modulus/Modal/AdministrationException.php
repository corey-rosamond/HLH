<?php

namespace Framework\Modulus\Modal;


class AdministrationException extends \Framework\Modulus\Query
{

  public function write($exceptionname, $message, $code, $severity, $file, $line, $exception, $previousexception, $host)
  {
    $query = "INSERT INTO `administration-exception`
      (`exception-name`, `message`, `code`, `severity`, `file`, `line`, `exception`, `previous-exception`, `host`)
      VALUES (:exceptionname, :message, :code, :severity, :file, :line, :exception, :previousexception, :host)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':exceptionname',     $exceptionname,     \PDO::PARAM_STR);
    $preparedStatement->bindValue(':message',           $message,           \PDO::PARAM_STR);
    $preparedStatement->bindValue(':code',              $code,              \PDO::PARAM_INT);
    $preparedStatement->bindValue(':severity',          $severity,          \PDO::PARAM_INT);
    $preparedStatement->bindValue(':file',              $file,              \PDO::PARAM_STR);
    $preparedStatement->bindValue(':line',              $line,              \PDO::PARAM_INT);
    $preparedStatement->bindValue(':exception',         $exception,         \PDO::PARAM_STR);
    $preparedStatement->bindValue(':previousexception', $previousexception, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':previousexception', $previousexception, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':host',              $host,              \PDO::PARAM_STR);
    return $this->Execute($preparedStatement);
  }
}
