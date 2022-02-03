<?php
/**
 * Funnel
 *
 * @package Framework\Modulus\Modal
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * Funnel
 *
 * This is your main interface to the user table
 */
class Funnel extends \Framework\Modulus\Query
{

  /**
   * Get all funnel information for the specified funnel
   * @method get
   * @param $key
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function get( $key )
  {
    $query = "SELECT * FROM `funnels` WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * Find a funnel key using its host name
   * @method findFunnelKey
   * @param $host
   * @return bool
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  public function findFunnelKey( $host )
  {
    $query = "SELECT `key` FROM `funnels` WHERE `address`=:host";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':host', $host, \PDO::PARAM_STR );
    $preparedStatement = $this->Execute($preparedStatement);
    if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
    $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
    return $resource['key'];
  }

  /**
   * Get all funnels key and name order by name desc
   * @method getFunnelKeyAndName
   */
  public function getFunnelKeyAndName()
  {
    $query = "SELECT `name`, `key` FROM `funnels` order by `name` desc";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement = $this->Execute($preparedStatement);
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }
}
