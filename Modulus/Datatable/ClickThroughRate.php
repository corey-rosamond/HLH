<?php
/**
 * ClickThroughRate
 *
 * @package Framework\Modulus\Datatable
 * @version 1.0.0
 */
namespace Framework\Modulus\Datatable;

/**
 * ClickThroughRate
 *
 * This is the datatable that generates the report for click through rate
 */
class ClickThroughRate extends \Framework\Modulus\Datatable
{
  /****************************************************/
  /*              FUNNEL PAGE CONSTANTS               */
  /****************************************************/
  /** @const vsl */
  const vsl = 1;
  /** @const checkout */
  const checkout = 2;
  /** @const checkout */
  const upsell1 = 3;
  /** @const upsell1 */
  const upsell2 = 4;
  /** @const upsell2 */
  const upsell3 = 5;
  /** @const upsell3 */
  const upsell4 = 6;
  /** @const upsell4 */
  const upsell5 = 7;
  /** @const upsell5 */
  const antiSpamPolicy = 8;
  /** @const antiSpamPolicy */
  const contactUs = 9;
  /** @const contactUs */
  const disclaimer = 10;
  /** @const privacyPolicy */
  const privacyPolicy = 11;
  /** @const refunds */
  const refunds = 12;
  /** @const termsAndConditions */
  const termsAndConditions = 13;
  /** @const thankYou */
  const thankYou = 14;
  /** @const download */
  const download = 15;
  /** @const downsell1 */
  const downsell1 = 16;
  /** @const downsell2 */
  const downsell2 = 17;
  /** @const downsell3 */
  const downsell3 = 18;
  /** @const downsell4 */
  const downsell4 = 19;
  /** @const downsell5 */
  const downsell5 = 20;
  /** @const advertorial */
  const advertorial = 21;
  /** @const error */
  const error = 22;
  /** @const notFound */
  const notFound = 23;
  /** @const tsl */
  const tsl = 24;
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
    ["label"=>"Key",          "style"=>"width:10%",     "order"=>true],
    ["label"=>"Name",         "style"=>"width:40%",     "order"=>true],
    ["label"=>"Visitors",     "style"=>"width:10%",     "order"=>true],
    ["label"=>"CTR Overall",  "style"=>"width:20%",     "order"=>true],
    ["label"=>"CTR Previous", "style"=>"width:20%",     "order"=>true]
  ];
  /** @var array $_columnFilters This is an array of defined filters for this datatable */
  protected $_gsColumnFilters = ['funnel-key'=>[],'link-key'=>[]];
  /** @var array $_defaultFilterValues The default filter values */
  protected $_gsDefaultFilterValues = [];
  /****************************************************/
  /*                  DATA MEMBERS                    */
  /****************************************************/
  /** @var integer $_aFunnelKey The active funnel key */
  private $_aFunnelKey = 9;
  /** @var integer $_aEmailLinkKey The active Email Link Key */
  private $_aEmailLinkKey = 188;
  /** @var array $_dFunnelMap */
  private $_dFunnelMap = [
    0=>self::vsl,
    1=>self::checkout,
    2=>self::upsell1,
    3=>self::downsell1,
    4=>self::upsell2,
    5=>self::downsell2,
    6=>self::upsell3,
    7=>self::downsell3,
    8=>self::upsell4,
    9=>self::downsell4,
    10=>self::upsell5,
    11=>self::downsell5,
    12=>self::thankYou
  ];
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
    /** @var array $data This is where we will populate the page data */
    $data = [];
    /** @var array $pageData Get the pages associated with this funnel */
    $pageData = $this->_funnelPages();
    /** @var array $funnelPages We will store the page data here */
    $funnelPages = [];
    /** @var array $page iterate over the pages for this funnel */
    foreach($pageData as $page){
      /** Push the page data into funnel pages */
      $funnelPages[$page['key']] = $page;
      /** And the visitors count for this page */
      $funnelPages[$page['key']]['visitors'] = $this->_funnelPageVisitors($page['key']);
    }
    /** @var integer $pageIndex The index for the funnel page in the data array */
    $pageIndex = 0;
    /** @var integer $pVisitors The previous pages visitors */
    $pVisitors = 0;
    /** @var integer $tVisitors total visitors */
    $tVisitors = null;
    /** @var array $_dFunnelMap */
    foreach($this->_dFunnelMap as $pageTypeKey){
      if(isset($funnelPages[$pageTypeKey])){
        if(is_null($tVisitors)){
          $tVisitors = $funnelPages[$pageTypeKey]['visitors'];
        }
        $data[$pageIndex] = [];
        $data[$pageIndex]['key'] = $funnelPages[$pageTypeKey]['key'];
        $data[$pageIndex]['name'] = $funnelPages[$pageTypeKey]['name'];
        $data[$pageIndex]['visitors'] = $funnelPages[$pageTypeKey]['visitors'];
        $data[$pageIndex]['ctr-overall'] = '100.00';
        if($tVisitors!=0){
          $data[$pageIndex]['ctr-overall'] = ($funnelPages[$pageTypeKey]['visitors']/$tVisitors)*100;
        }
        $data[$pageIndex]['ctr-previous']  = '100';
        if($pVisitors!=0){
          $data[$pageIndex]['ctr-previous'] = ($funnelPages[$pageTypeKey]['visitors']/$pVisitors)*100;
        }
        $pVisitors = $funnelPages[$pageTypeKey]['visitors'];
        $pageIndex++;
      }
    }
    /** Return the data */
    return $data;
  }

  /**
   * Get a list of the active pages for a funnel
   * @method _funnelPages
   * @return array
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  private function _funnelPages()
  {
    $query = "SELECT `page-type-key` as `key`, `name` FROM `funnel-pages` WHERE `funnel-key`=:funnelKey AND `page-type-key` IN(1,2,3,4,5,6,7,16,17,18,19,20,14,15)";
    $statement = $this->_prepare($query);
    $statement->bindValue(":funnelKey", $this->_erFunnelKey);
    $statement = $this->_execute($statement);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Get the visitor count for a given funnel page campaign and page type
   * @method _funnelPageVisitors
   * @param $pageTypeKey
   * @return mixed
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  private function _funnelPageVisitors($pageTypeKey)
  {
    $query = "
      SELECT count(DISTINCT(`fs`.`key`)) as `visitors`
      FROM `funnel-sessions` AS `fs`
      LEFT JOIN `funnel-session-views` as `fsv`
      ON `fs`.`key` = `fsv`.`funnel-session-key`
      WHERE `fs`.`funnel-key`=:funnelKey
      AND `fs`.`campaign-key`=:campaignKey
      AND `fsv`.`funnel-page-key`=:pageTypeKey";
    $statement = $this->_prepare($query);
    $statement->bindValue(":funnelKey", $this->_erFunnelKey);
    $statement->bindValue(":campaignKey", $this->_erLinkKey);
    $statement->bindValue(":pageTypeKey", $pageTypeKey);
    $statement = $this->_execute($statement);
    $resource = $statement->fetch(\PDO::FETCH_ASSOC);
    return $resource['visitors'];
  }

  /**
   * Format row data
   * @method _rowFormatter
   * @param $row
   * @return mixed
   */
  protected function _rowFormatter($row)
  {
    $row['visitors'] = number_format($row['visitors']);
    $row['ctr-overall'] = number_format($row['ctr-overall'],2)."%";
    $row['ctr-previous'] = number_format($row['ctr-previous'],2)."%";
    return $row;
  }

  protected function _eRequestParse()
  {
    parent::_eRequestParse();
    $this->_erFunnelKey = $this->_erData['funnel-key'];
    $this->_erLinkKey = $this->_erData['link-key'];
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