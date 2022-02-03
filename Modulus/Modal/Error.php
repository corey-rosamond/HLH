<?php
/**
 * Error
 *
 * @package Framework\Modulus\Modal
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * User
 *
 * This is your main interface to the user table
 */
class Error extends \Framework\Modulus\Query
{
  /**
   * @param $key
   * @return mixed
   */
  public function logException($exceptionname, $message, $code, $severity, $file, $line, $exception, $previousexception)
  {
    $query = "INSERT INTO `administration-exception`
        (`exception-name`,`message`,`code`,`severity`,`file`,`line`,`exception`,`previous-exception`)
        VALUES (:exceptionname,:message,:code,:severity,:file,:line,:exception,:previousexception)";

    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':exceptionname', $exceptionname, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':message', $message, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':code', $code, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':severity', $severity, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':file', $file, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':line', $line, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':exception', $exception, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':previousexception', $previousexception, \PDO::PARAM_STR);
    return $this->Execute($preparedStatement);
  }
}
