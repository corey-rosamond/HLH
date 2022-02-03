<?php
/**
 * Orders
 *
 * @package Framework\Modulus\Modal
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * Orders
 *
 * This is your main interface to the user table
 */
class Orders extends \Framework\Modulus\Query
{

  /**
   * This is the primary table for this modal
   * @var     string    $_table
   * @access  private
   */
  private $_table = 'funnel-orders';

  /**
   * Get the count of orders in each status for an interval amount of time backwards
   * @method  getCountPerStatusInterval
   * @param   integer       $interval         Number of the interval type to move back
   * @param   string        $intervalType     Day, Week, Month
   * @return  mixed                           \PDOResource or false if no records found
   * @access  public
   */
  public function getCountPerStatusInterval( $interval = 1, $intervalType = "DAY" )
  {
    $query = "SELECT count(*) as `count`, `status` as `status` FROM `{$this->_table}`
      WHERE `order-date` > DATE( DATE_SUB( NOW() , INTERVAL $interval $intervalType )) GROUP BY `status`";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() === 0 ){
      return 0;
    }
    return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
  }

  /**
   * Get orders in a current status before a set amount of hours before now
   * @method  getOrdersIn
   * @param   string        $status Status to match
   * @param   string        $before Hours to go back
   * @return  mixed               Assocative arrray of records or false
   * @access  public
   */
  public function getOrdersIn( $status, $before = false )
  {
    $query = "SELECT * FROM `{$this->_table}` WHERE `status`=:status";
    if( $before ){
      $query .= " AND `order-date` < DATE( DATE_SUB( NOW(), INTERVAL :before HOUR ) )";
    }
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':status', $status, \PDO::PARAM_INT );
    if( $before ){
      $preparedStatement->bindValue( ':before', $before, \PDO::PARAM_INT );
    }
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() === 0 ){
      return 0;
    }
    return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
  }

  /**
   * Get the customer record from the key
   * @method  get
   * @param   int           $key
   * @return  mixed         PDOResource or false
   * @access  public
   */
  public function get( $key )
  {
    $query = "SELECT * FROM `{$this->_table}` WHERE `key`=:key";
    $preparedStatement = $this->prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() <= 0) {return false;}
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }




  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                          Bellow here really needs to be removed                                     */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  public function getLinkTotalSales($key){
    $query = 'SELECT count(`key`) as `total` FROM `funnel-orders` WHERE `campaign-key`=:key';
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {return 0;}
    $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource['total'];
  }

  public function getLinkTotalProfit($key){
    $query = 'SELECT sum(`order-total-before`) as `total` FROM `funnel-orders` WHERE `campaign-key`=:key';

    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {return 0;}
    $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
    return $resource['total'];
  }
  public function getOrderTotals($key){
    $query = '
      SELECT
        `foi`.`product-key` as `product-key`,
        count(`foi`.`key`) AS `total-sale`,
        sum(`foi`.`price`) as `total-profit`
      FROM `funnel-orders` AS `fo`
      RIGHT join `funnel-order-items` AS `foi`
      ON `fo`.`key` = `foi`.`order-key`
      WHERE `fo`.`campaign-key`=:key
      GROUP BY `foi`.`product-key`';
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {return 0;}
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }
  public function getCountAndSales($key, $link){
    $query = '
      SELECT
        ifnull(count(`foi`.`key`),0) AS `total-sales`,
        ifnull(sum(`foi`.`price`),0) as `total-profit`
      FROM `funnel-orders` AS `fo`, `funnel-order-items` AS `foi`
      WHERE `fo`.`key` = `foi`.`order-key`
      AND `fo`.`campaign-key`=:link
      AND `foi`.`product-key` = :key';
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':link', $link, \PDO::PARAM_INT);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {
      return array('total-sale'=>0,'total-profit'=>0);
    }
    return $preparedStatement->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * [getOrdersDatatable description]
   * @method  getOrdersDatatable
   * @param   mixed              $oID
   * @param   mixed              $oDateStart
   * @param   mixed              $oDateEnd
   * @param   mixed              $oFirstName
   * @param   mixed              $oLastName
   * @param   mixed              $oAmount
   * @param   mixed              $oStatus
   * @param   mixed              $lStart
   * @param   mixed              $lEnd
   * @param   mixed              $oColumn
   * @param   string             $oDirection
   * @param   boolean            $countOnly
   * @return  mixed                           Assocative Array OR FALSE
   * @access  public
   */
  public function getOrdersDatatable(
    $oID = false, $oDateStart = false, $oDateEnd = false, $oFirstName = false, $oLastName = false, $oAmount = false,
    $oStatus = false, $lStart = false, $lEnd = false, $oColumn = false, $oDirection = 'asc', $countOnly = false
  ){
    $selectors = ['`is-test`=0'];
    /** Start of the query */
    $s = "SELECT `key`, `order-date`, `first-name`, `last-name`, FORMAT(`order-total-before`,2) as `total-before`,
      FORMAT(`order-total-after`,2) as `total-after`, `status` FROM `funnel-orders`";
    if( $countOnly ){ $s = "SELECT count(`key`) as `records` FROM `funnel-orders`"; }
    /** Add the selectors */
    if( $oID !== false ){ array_push( $selectors, "`key` LIKE :key" ); }
    if( $oFirstName !== false ){ array_push( $selectors, "`first-name` LIKE :firstName" ); }
    if( $oLastName !== false ){ array_push( $selectors, "`last-name` LIKE :lastName" ); }
    if( $oAmount !== false ){ array_push( $selectors, "`order-total-before` = :orderTotal" ); }
    if( $oStatus !== false ){ array_push( $selectors, "`status` = :orderStatus" ); }
    if( $oDateStart !== false ){ array_push( $selectors, "`order-date` > :sDate" ); }
    if( $oDateEnd !== false ){ array_push( $selectors, "`order-date` < :eDate" ); }
    /* Query middle */
    $m = "";
    /** Add the selectors to the query */
    for( $i = 0; $i < count( $selectors ); $i++ ){ $m .= (($i===0)?' WHERE ':' AND ')."{$selectors[$i]}"; }
    /** End of the query */
    $e = "";
    $orderables = ['key','order-date','first-name','last-name','order-total-before','status'];
    if( $oColumn !== false ){ $e .= " ORDER BY `{$orderables[$oColumn]}` {$oDirection}"; }
    if( !$countOnly ){ if( $lStart !== false ){ $e .= " LIMIT {$lStart}, {$lEnd}"; } }
    /** Prep the query for some binds */
    $preparedStatement = $this->Prepare( "{$s}{$m}{$e}" );
    /** Bind selectors */
    if( $oID !== false ){ $preparedStatement->bindValue( ':key', "%".ltrim( rtrim( $oID ) )."%", \PDO::PARAM_STR ); }
    if( $oFirstName !== false ){ $preparedStatement->bindValue( ':firstName', ltrim( rtrim( $oFirstName ) ), \PDO::PARAM_STR ); }
    if( $oLastName !== false ){ $preparedStatement->bindValue( ':lastName', ltrim( rtrim( $oLastName ) ), \PDO::PARAM_STR ); }
    if( $oAmount !== false ){ $preparedStatement->bindValue( ':orderTotal', ltrim( rtrim( $oAmount ) ), \PDO::PARAM_STR ); }
    if( $oStatus !== false ){ $preparedStatement->bindValue( ':orderStatus', ltrim( rtrim( $oStatus ) ), \PDO::PARAM_STR ); }
    if( $oDateStart !== false ){ $preparedStatement->bindValue( ':sDate', ltrim( rtrim( $oDateStart ) ), \PDO::PARAM_STR ); }
    if( $oDateEnd !== false ){ $preparedStatement->bindValue( ':eDate', ltrim( rtrim( $oDateEnd ) ), \PDO::PARAM_STR ); }
    /** Run the query */
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
    if( $countOnly ){
      $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
      return $resource['records'];
    }
    return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
  }



  /**                            BELOW HERE NEEDS TO BE REWORKED                **/
  /**
   * orderCountByStatus
   *
   * Gives you the order count for that status
   * @param  $status string
   * @return int|false
   */
  public function orderCountByStatus($status)
  {
      $query = "SELECT count(*) as `value` FROM `funnel-orders` WHERE `status`=:status and `is-test`=0 and `order-total-before`>0";
      $preparedStatement = $this->prepare($query);
      $preparedStatement->bindValue(':status', $status, \PDO::PARAM_STR);
      $preparedStatement = $this->Execute($preparedStatement);
      if ($preparedStatement->rowCount() <= 0) {return false;}
      $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
      return $resource['value'];
  }

  public function ordersPerCampaign()
  {
      $Query = "SELECT `campaign-key`, FORMAT(count(*),0) as `sales`, FORMAT(sum(`order-total-before`),2) as `sold`
          FROM `funnel-orders` where `is-test`=0
          and `order-total-before` >0 group by `campaign-key`";
      $PreparedStatement = $this->Prepare($Query);
      $PreparedStatement = $this->Execute($PreparedStatement);
      if ($PreparedStatement->rowCount() <= 0) {return false;}
      return $PreparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function totalOrders()
  {
    $query = "SELECT count(`key`) as `value` FROM `funnel-orders` WHERE `is-test`=0";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement = $this->Execute($preparedStatement);
    if( $preparedStatement->rowCount() <= 0 ){
      return 0;
    }
    $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource['value'];
  }

  // TEMPORARY
  public function getMatureOrders(){
    $query = "SELECT `key` FROM `funnel-orders` WHERE `is-test`=0
        AND `order-total-before`>0 AND `order-date` <
        DATE_SUB(NOW(),INTERVAL 24 HOUR) AND `status`
        IN('Created','Captured') limit 10";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() <= 0) {return false;}
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function setOrderApproved($key){
    $query = "UPDATE `funnel-orders` set `status`='Approved' WHERE `key`=:key";
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
  }
}
