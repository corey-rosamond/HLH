<?php
/**
* OrderMessages
*
* @package Framework\Modulus\Modal
* @version 1.0.0
*/
namespace Framework\Modulus\Modal;


/**
 * OrderMessages
 *
 */
class OrderNotes extends \Framework\Modulus\Query
{
  public function get( $key )
  { 
    $Query = "SELECT * FROM `order-notes` WHERE `key`=:key";
    $PreparedStatement = $this->Prepare( $Query );
    $PreparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $PreparedStatement = $this->Execute($PreparedStatement);
    if($PreparedStatement->rowCount() <= 0){
      return 0;
    }
    return $PreparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  public function countOrderNotes( $orderKey )
  {
    $Query = "SELECT count(`key`) as `total` FROM `order-notes` WHERE `order-key`=:orderKey";
    $PreparedStatement = $this->Prepare( $Query );
    $PreparedStatement->bindValue(':orderKey', $orderKey, \PDO::PARAM_INT);
    $PreparedStatement = $this->Execute($PreparedStatement);
    if($PreparedStatement->rowCount() <= 0){
      return 0;
    }
    $resource = $PreparedStatement->fetch(\PDO::FETCH_ASSOC);
    return $resource['total'];
  }

  public function getOrderNotes( $orderKey )
  {
    $Query = "SELECT `m`.`key`,`m`.`order-key`, CONCAT(`u`.`first-name`,' ',`u`.`last-name`) as `user`, `m`.`message`,`m`.`timestamp` FROM `order-notes` as `m`
      LEFT JOIN `administration-users` as `u` ON `m`.`user-key` = `u`.`key` WHERE `m`.`order-key`=:orderKey ORDER BY `m`.`timestamp` DESC";
    $PreparedStatement = $this->Prepare( $Query );
    $PreparedStatement->bindValue(':orderKey', $orderKey, \PDO::PARAM_INT);
    $PreparedStatement = $this->Execute($PreparedStatement);
    if($PreparedStatement->rowCount() == 0) {
      return [];
    }
    return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function addOrderNote($order, $user, $message)
  {
    $query = "INSERT INTO `order-notes` (`order-key`,`user-key`,`message`) VALUES (:order, :user, :message)";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':order',     $order,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':user',      $user,   \PDO::PARAM_INT);
    $preparedStatement->bindValue(':message',   $message,\PDO::PARAM_STR);
    $this->Execute( $preparedStatement );
    return $this->get($this->LastInsertID());
  }
}
