<?php
/**
 * MediaBuyEmailLink
 *
 * @package Framework\Modulus\Datatable
 * @version 1.0.0
 */
namespace Framework\Modulus\Datatable;

/**
 * MediaBuyEmailLink
 *
 * This is the prototype datatable master object for the tracking system
 * It will help me learn the cause of my tracking anomaly
 */
class MediaBuyEmailLink extends \Framework\Modulus\Datatable
{
  /** @var array $_columnDefinitions */
  protected $_columnDefinitions = [
    ["label"=>"Link #",         "style"=>"text-align:center;",          "order"=>true],
    ["label"=>"Name",           "style"=>"text-align:center;",          "order"=>true],
    ["label"=>"Promoter",       "style"=>"",                            "order"=>true],
    ["label"=>"Funnel",         "style"=>"",                            "order"=>true],
    ["label"=>"Start Date",     "style"=>"text-align:right;",           "order"=>true],
    ["label"=>"End Date",       "style"=>"text-align:right;",           "order"=>true],
    ["label"=>"Cost",           "style"=>"text-align:right;",           "order"=>true],
    ["label"=>"Expected Yield", "style"=>"width:20%;text-align:right;", "order"=>true]
  ];
  /** @var array $_columnFilters This is an array of defined filters for this datatable */
  protected $_columnFilters = [
    false,
    [
      'type'=>self::searchFilter,
      'id'=>'',
      'name'=>'',
      'placeholder'=>'',
      'filter-name'=>'',
      'filter-type'=>''
    ],
    false,
    false,
    false,
    false,
    false,
    false,
  ];
  /** @var array $_defaultFilterValues The default filter values */
  protected $_defaultFilterValues = [];
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
      select
       `mel`.`key`,
       `mel`.`name` as `link-name`,
       `mp`.`name` as `promoter-name`,
       `f`.`name` as `funnel-name`,
       `mel`.`start-date`,
       `mel`.`end-date`,
       `mel`.`cost`,
       `mel`.`expected-return`
      FROM `mediabuy-email-links` as `mel`
      LEFT JOIN `mediabuy-promoters` as `mp`
      ON `mel`.`promoter-key` = `mp`.`key`
      LEFT JOIN `funnels` as `f`
      ON `mel`.`funnel-key` = `f`.`key`
      ORDER BY `mel`.`name` DESC
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
    return $row;
  }
}