<?php
/**
 * Navigator
 *
 * @package App\Abstracts
 * @version 1.2.0
 */
namespace Framework\Modulus\Modal;

/**
 * FunnelErrorMessages
 *
 * This is the base for the whole system. It converts exceptions from being bad things to being
 * amazingly useful
 */
class FunnelErrorMessages extends \Framework\Modulus\Query
{

  public function write($class, $message, $sessionKey)
  {
    $query = "INSERT INTO `funnel-error-messages`
      (`class`,`message`,`session-key`) VALUES (:class, :message, :sessionKey)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':class', $class, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':message', $message, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':sessionKey', $sessionKey, \PDO::PARAM_INT);
    return $this->Execute($preparedStatement);
  }
}