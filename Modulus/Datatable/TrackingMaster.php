<?php
/**
 * TrackingMaster
 *
 * @package Framework\Modulus\Datatable
 * @version 1.0.0
 */
namespace Framework\Modulus\Datatable;

/**
 * TrackingMaster
 *
 * This is the prototype datatable master object for the tracking system
 * It will help me learn the cause of my tracking anomaly
 */
class TrackingMaster extends \Framework\Modulus\Datatable
{
  /** @var array $_defaultFilterValues The default filter values */
  protected $_defaultFilterValues = [];
  /** @var array $_columnDefinitions */
  protected $_columnDefinitions = [
    ["label"=>"Session Key", "style"=>"width:10%;text-align:center;", "order"=>true],
    ["label"=>"IP Address", "style"=>"width:10%;text-align:center;", "order"=>true],
    ["label"=>"Funnel Name", "style"=>"width:20%", "order"=>true],
    ["label"=>"Tracking Link", "style"=>"width:20%", "order"=>true],
    ["label"=>"Date &amp; Time", "style"=>"width:20%;text-align:right;", "order"=>true],
  ];
  /** @var int $_resultLimit The limit for queries */
  protected $_resultLimit = 50;


  /**
   * The inital query for the datatable
   * @method _initialQuery
   * @return array
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  protected function _initialQuery()
  {
    $query = "
      SELECT
        `fs`.`key` as `session-key`,
        INET6_NTOA(`fs`.`ip-address`) as `ip-address`,
        `f`.`name` as `funnel-name`,
        IFNULL(`mel`.`name`, 'None') as `campaign-name`,
        `time-stamp`
      FROM `funnel-sessions` as `fs`
      LEFT JOIN `funnels` as `f`
      ON `fs`.`funnel-key`=`f`.`key`
      LEFT JOIN `mediabuy-email-links` as `mel`
      ON `fs`.`campaign-key`=`mel`.`key`
      ORDER BY `fs`.`key` desc
      {$this->_limit()}
    ";
    $statement = $this->_prepare($query);
    $statement = $this->_execute($statement);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Format row data
   * @method _rowFormatter
   * @param $row
   * @return mixed
   */
  protected function _rowFormatter($row)
  {
    $row['session-key'] = sprintf("#%012s", $row['session-key']);
    $row['time-stamp'] = date( "l jS \of F Y h:i:s A", strtotime( $row['time-stamp'] ) );
    return $row;
  }
}