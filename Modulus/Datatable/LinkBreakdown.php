<?php
/**
 * LinkBreakdown
 *
 * @package Framework\Modulus\Datatable
 * @version 1.0.0
 */
namespace Framework\Modulus\Datatable;

/**
 * LinkBreakdown
 *
 * This is the datatable that generates the report for click through rate
 */
class LinkBreakdown extends \Framework\Modulus\Datatable
{
  /****************************************************/
  /*                 DATATABLE SETTINGS               */
  /****************************************************/
  /** @var array $_tools */
  protected $_dsTools = null;
  /** @var int $_resultLimit The limit for queries */
  protected $_dsResultLimit = 50;
  /** @var boolean $_dsSortable */
  protected $_dsSortable = false;
  /** @var boolean $_dsFilterable */
  protected $_dsFilterable = false;
  /** @var boolean $_dsPagination */
  protected $_dsPagination = false;
  /** @var boolean $_dsShowInfo */
  protected $_dsShowInfo = false;
  /****************************************************/
  /*             DATATABLE CONFIGURATION              */
  /****************************************************/
  /** @var array $_columnDefinitions */
  protected $_gsColumnDefinitions = [
    ["label"=>"Key",          "style"=>"width:50%",     "order"=>true],
    ["label"=>"Value",        "style"=>"width:50%",     "order"=>true],
  ];
  /** @var array $_columnFilters This is an array of defined filters for this datatable */
  protected $_gsColumnFilters = ['funnel-key'=>[],'link-key'=>[]];
  /** @var array $_defaultFilterValues The default filter values */
  protected $_gsDefaultFilterValues = [];
  /****************************************************/
  /*           EVENT REQUEST DATA MEMBERS             */
  /****************************************************/
  /** @var integer $_erFunnelKey */
  protected $_erFunnelKey;
  /** @var integer $_erLinkKey */
  protected $_erLinkKey;

  /**
   * The initial query for the datatable
   * @method _initialQuery
   * @return array
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  protected function _initialQuery()
  {
    if(is_null($this->_erFunnelKey)||is_null($this->_erLinkKey)){
      return false;
    }
    $data = [];
    $data[] = $this->_totalRevenueGenerated();
    return $data;
  }

  /**
   * Format row data
   * @method _rowFormatter
   * @param $row
   * @return mixed
   */
  protected function _rowFormatter($row)
  {
    $row['total'] = "$".number_format($row['total'],2);
    return $row;
  }


  protected function _totalRevenueGenerated()
  {
    $query = "
      SELECT 'Total Revenue Generated' as `label`, sum(`funnel-orders`.`order-total-before`) as `total`
      FROM `funnel-orders`
      WHERE `funnel-orders`.`funnel-key`=:funnelKey
      AND `funnel-orders`.`campaign-key`=:campaignKey";
    $statement = $this->_prepare($query);
    $statement->bindValue(":funnelKey", $this->_erFunnelKey);
    $statement->bindValue(":campaignKey", $this->_erLinkKey);
    $statement = $this->_execute($statement);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  protected function _eRequestParse()
  {
    parent::_eRequestParse();
    $this->_erFunnelKey = (($this->_erData['funnel-key']=="")?null:$this->_erData['funnel-key']);
    $this->_erLinkKey = (($this->_erData['link-key']=="")?null:$this->_erData['link-key']);
  }

  public function run()
  {
    $this->_eRequestParse();
    if(is_null($this->_erFunnelKey)||is_null($this->_erLinkKey)){
      $this->_eResponse([], 1, 0, 0);
    }
    /** @var array $records */
    $records = $this->_initialQuery();
    /** Let _eResponse handle the rest */
    $this->_eResponse($records, ($this->_erDrawCount+2), sizeof($records), 0);
  }
}