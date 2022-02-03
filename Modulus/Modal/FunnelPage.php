<?php
/**
 * FunnelPage
 *
 * @package Framework\Modulus\Modal
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * FunnelPage
 *
 */
class FunnelPage extends \Framework\Modulus\Query
{

  public function getPageByType( $funnelKey, $type )
  {
    $query = "SELECT * FROM `funnel-pages` WHERE `funnel-key`=:funnelKey AND `page-type-key`=:type";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':funnelKey', $funnelKey, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':type', $type, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if($preparedStatement->rowCount() <= 0){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  public function getByName( $name, $funnelKey )
  {
    $query = "SELECT * FROM `funnel-pages` WHERE `funnel-key`=:funnelKey AND `name`=:name";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':name', $name, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':funnelKey', $funnelKey, \PDO::PARAM_STR);
    $preparedStatement = $this->Execute($preparedStatement);
    if($preparedStatement->rowCount() <= 0){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

}
