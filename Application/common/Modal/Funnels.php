<?php
/**
 * Funnels
 *
 * @package common\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * Funnels
 *
 * These are all the querys for the funnels
 */
class Funnels extends \Framework\Modulus\Query
{
  /** TABLE NAMES */
  private $_pTable  = "funnels";


  /** KEYS */
  private $_ptKey   = "key";

  /**
   * Get the name for a specific funnel
   * @method  name
   * @param   int       $key  Funnel key we are querying against
   * @return  mixed           PDOResource or 0
   * @access  public
   */
  public function name( $key )
  {
    $query = "SELECT `name` as `name` FROM `{$this->_pTable}` WHERE `{$this->_ptKey}`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return 0;
    }
    $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource['name'];
  }

  /**
   * Get the address for a specific funnel
   * @method  address
   * @param   int       $key  Funnel key we are querying against
   * @return  mixed           PDOResource or 0
   * @access  public
   */
  public function address( $key )
  {
    $query = "SELECT `address` as `address` FROM `{$this->_pTable}` WHERE `{$this->_ptKey}`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return 0;
    }
    $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource['address'];
  }

  /**
   * Get all data for a specific funnel
   * @method  data
   * @param   int       $key  Funnel key we are querying against
   * @return  mixed           PDOResource or 0
   * @access  public
   */
  public function data( $key )
  {
    $query = "SELECT * FROM `{$this->_pTable}` WHERE `{$this->_ptKey}`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return 0;
    }
    return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }


  /**
  public function funnelSearch( $term ){
    $query = "SELECT `key` as `id`, `name` as `text` FROM `funnels` WHERE `name` LIKE :term";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':term', "%$term%", \PDO::PARAM_STR );
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() <= 0) { return false; }
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }


  public function getFunnelName( $key ){
    $query = "SELECT `key` as `id`, `name` as `text` FROM `funnels` WHERE `key` = :key ";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {return false;}
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }
  */
}
