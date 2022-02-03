<?php
/**
 * PaymentProcessor
 *
 * @package Framework\Modulus\Datatable
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

class PaymentProcessor extends \Framework\Modulus\Query
{
  public function addLogEntry($oKey, $response, $rText, $aCode, $transId, $avsResponse, $cvvResponse, $type, $rCode, $amount)
  {
    $query = "INSERT INTO `funnel-order-transactions`
      (`order-key`,`response`,`response-text`,`authcode`,`transaction-id`,`avs-response`,`cvv-response`,`type`,`response-code`,`amount`) VALUES
      (:oKey, :response, :rText, :aCode, :transId, :avsResponse, :cvvResponse, :type, :rCode, :amount)";
    $PreparedStatement = $this->Prepare( $query );
    $PreparedStatement->bindValue(':oKey',        $oKey,        \PDO::PARAM_INT);
    $PreparedStatement->bindValue(':response',    $response,    \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':rText',       $rText,       \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':aCode',       $aCode,       \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':transId',     $transId,     \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':avsResponse', $avsResponse, \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':cvvResponse', $cvvResponse, \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':type',        $type,        \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':rCode',       $rCode,       \PDO::PARAM_STR);
    $PreparedStatement->bindValue(':amount',      $amount,      \PDO::PARAM_STR);
    $this->Execute($PreparedStatement);
    return $this->LastInsertID();
  }

  public function getOrder( $key )
  {
    $Query = "SELECT * FROM `funnel-order-transactions` WHERE `order-key`=:Key ORDER BY `timestamp` DESC";
    $PreparedStatement = $this->Prepare( $Query );
    $PreparedStatement->bindValue(':Key', $key, \PDO::PARAM_INT);
    $PreparedStatement = $this->Execute($PreparedStatement);
    return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getOrderCount( $key )
  {
    $Query = "SELECT count(`key`) as `total` FROM `funnel-order-transactions` WHERE `order-key`=:Key";
    $PreparedStatement = $this->Prepare( $Query );
    $PreparedStatement->bindValue(':Key', $key, \PDO::PARAM_INT);
    $PreparedStatement = $this->Execute($PreparedStatement);
    $resource = $PreparedStatement->fetch(\PDO::FETCH_ASSOC);
    return $resource['total'];
  }
}
