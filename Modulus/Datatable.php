<?php
/**
 * Datatable
 *
 * @package Framework\Modulus
 * @version 1.0.0
 */
namespace Framework\Modulus;

/**
 * Datatable
 *
 * The main query object. Every model must extend this
 */
class Datatable
{
  /** @const integer searchFilter */
  const searchFilter = 0;
  /****************************************************/
  /*                DATABASE DATA MEMBERS             */
  /****************************************************/
  /** @var string $_databaseName */
  private $_databaseName;
  /** @var \Framework\Modulus\PDOdatabases $_pdoDatabses */
  private $_pdoDatabses;
  /** @var mixed handle $_database */
  private $_database;
  /****************************************************/
  /*                 DATATABLE SETTINGS               */
  /****************************************************/
  /** @var string $_dsTools */
  protected $_dsTools = 'print, pdf, excel, csv';
  /** @var int $_dsResultLimit The limit for queries */
  protected $_dsResultLimit = 50;
  /** @var boolean $_dsSortable */
  protected $_dsSortable = true;
  /** @var boolean $_dsFilterable */
  protected $_dsFilterable = true;
  /** @var boolean $_dsPagination */
  protected $_dsPagination = true;
  /** @var boolean $_dsShowInfo */
  protected $_dsShowInfo = true;
  /** @var string $_dsPaginationType */
  protected $_dsPaginationType = "full_numbers";
  /** @var string $_dsMenuLength Menu length for the datatable */
  protected $_dsMenuLength = [[25,50,75,100],[25,50,75,100]];
  /** @var string $_dsAriaSortAscending */
  protected $_dsAriaSortAscending = "activate to sort column ascending";
  /** @var string $_dsAriaSortDescending */
  protected $_dsAriaSortDescending = "activate to sort column descending";
  /** @var string $_dsEmptyTableMessage */
  protected $_dsEmptyTableMessage = "No data available in table";
  /** @var string $_dsInfoMessage */
  protected $_dsInfoMessage = "Showing _START_ to _END_ of _TOTAL_ entries";
  /** @var string $_dsInfoEmptyMessage */
  protected $_dsInfoEmptyMessage = "No entries found";
  /** @var string $_dsInfoFilteredMessage */
  protected $_dsInfoFilteredMessage ="(filtered from _MAX_ total entries)";
  /** @var string $_dsLengthMenuMessage */
  protected $_dsLengthMenuMessage ="_MENU_ &nbsp;&nbsp;entries";
  /** @var string $_dsSearchMessage */
  protected $_dsSearchMessage = "Search:&nbsp;&nbsp;";
  /** @var string $_dsZeroRecordsMessage */
  protected $_dsZeroRecordsMessage = "No matching records found";
  /****************************************************/
  /*                 GENERATOR SETTINGS               */
  /****************************************************/
  /** @var string $_gsTableID */
  protected $_gsTableID;
  /** @var array $_gsDefaultFilterValues The default filter values */
  protected $_gsDefaultFilterValues = [];
  /** @var array $_gsColumnFilters This is an array of defined filters for this datatable */
  protected $_gsColumnFilters = [];
  /** @var array $_gsColumnDefinitions */
  protected $_gsColumnDefinitions = [];
  /** @var string $_gsTableStart The start of the html datatable */
  protected $_gsTableClass = "table table-striped table-bordered table-hover dt-responsive framework-datatable";
  /** @var string $_gsTableEnd The end of the html datatable */
  protected $_gsTableEnd = "</table>";
  /****************************************************/
  /*             EVENT REQUEST DATA MEMBERS           */
  /****************************************************/
  /** @var array $_erData */
  protected $_erData;
  /** @var array $_erColumns */
  protected $_erColumns;
  /** @var integer $_erRecordStart */
  protected $_erRecordStart;
  /** @var integer $_erRecordEnd */
  protected $_erRecordEnd;
  /** @var integer $_erOrderColumn */
  protected $_erOrderColumnIndex;
  /** @var string $_erOrderDirection */
  protected $_erOrderDirection;
  /** @var integer $_erDrawCount */
  protected $_erDrawCount;
  /** @var  integer $_erLength */
  protected $_erLength;
  /** @var string|boolean $_erSearchRegex */
  protected $_erSearchRegex;
  /** @var string $_erSearchValue */
  protected $_erSearchValue;

  /**
   * Datatable constructor.
   * @method __construct
   * @param                             $dbname
   * @param \Framework\Modulus\DBConfig $config
   */
  public function __construct($dbname, DBConfig $config)
  {
    /** @var \Framework\Modulus\PDOdatabases $_pdoDatabses */
    $this->_pdoDatabses = PDOdatabases::create_instance();
    /** @var string $_databaseName */
    $this->_databaseName = $dbname;
    /** Connect to the database */
    $this->_connect($dbname, $config);
    /** @var string $_tableID */
    $this->_gsTableID = substr(get_called_class(),(strrpos(get_called_class(),"\\")+1));
  }
  /****************************************************/
  /*                  HTML GENERATORS                 */
  /****************************************************/

  /**
   * Render the datatable
   * @method render
   * @return string
   */
  public function render()
  {
    /** Return the datatable */
    return "{$this->_dtTableStart()}{$this->_dtHead()}{$this->_dtBody()}{$this->_gsTableEnd}";
  }

  /**
   * Generate the table start with data attributes for the datatable
   * @method _dtTableStart
   * @return string
   */
  protected function _dtTableStart()
  {
    $menuLength = json_encode($this->_dsMenuLength);
    return "<table
      class=\"{$this->_gsTableClass}\"
      id=\"{$this->_gsTableID}\"
      data-datatable-tools=\"{$this->_dsTools}\"
      data-datatable-filterable=\"{$this->_dsFilterable}\"
      data-datatable-sortable=\"{$this->_dsSortable}\"
      data-datatable-pagination=\"{$this->_dsPagination}\"
      data-datatable-show-info=\"{$this->_dsShowInfo}\"
      data-datatable-page-length=\"{$this->_dsResultLimit}\"
      data-datatable-aria-sort-ascending-message=\"{$this->_dsAriaSortAscending}\"
      data-datatable-aria-sort-decending-message=\"{$this->_dsAriaSortDescending}\"
      data-datatable-empty-table-message=\"{$this->_dsEmptyTableMessage}\"
      data-datatable-info-message=\"{$this->_dsInfoMessage}\"
      data-datatable-info-empty-message=\"{$this->_dsInfoEmptyMessage}\"
      data-datatable-info-filtered-message=\"{$this->_dsInfoFilteredMessage}\"
      data-datatable-length-menu-message=\"{$this->_dsLengthMenuMessage}\"
      data-datatable-search-message=\"{$this->_dsSearchMessage}\"
      data-datatable-zero-records-message=\"{$this->_dsZeroRecordsMessage}\"
      data-datatable-menu-length=\"{$menuLength}\"
      data-datatable-custom-request-variables=\"{$this->_dtCustomRequestVariables()}\">";
  }

  /**
   * Construct the datatable head element
   * @method _dtHead
   * @return string
   */
  protected function _dtHead()
  {
    /** @var string $content build the head element into the content variable */
    $content = "<thead>";
      /** Start th table for the labels */
      $content .= "<tr role=\"row\" class=\"heading\">";
      /** @var array $column Loop over the column definitions */
      foreach($this->_gsColumnDefinitions as $column){
        /** @var string $style Determine if there are styles to add */
        $style = ((isset($column['style']))?" style=\"{$column['style']}\"":"");
        /** @var string $orderAble Determine the orderAble value */
        $orderAble = ((isset($column['order']))?'true':'false');
        /** Append the th element to the head content */
        $content .= "<th{$style} data-datatable-order=\"{$orderAble}\">{$column['label']}</th>";
      }
      /** End the table row */
      $content .= "</tr>";
      /** Check if we have filters to add */
      if(sizeof($this->_gsColumnFilters)!==0){
        /** Add the return from _dtFilters to the header content */
        $content .= $this->_dtFilters();
      }
    /** Return the thead content */
    return "{$content}</thead>";
  }

  protected function _dtCustomRequestVariables()
  {
    $customRequestVariables = "";
    foreach($this->_gsColumnFilters as $key => $data){
      $customRequestVariables .= "{$key}, ";
    }
    $customRequestVariables = substr($customRequestVariables,0,-2);
    return "{$customRequestVariables}";
  }

  /**
   * Make all of the filters for the active datatable
   * @mthod _dtFilters
   * @return string
   */
  protected function _dtFilters()
  {
    return null;
    if(!$this->_gsColumnFilters){
      return null;
    }
    /** @var string $content This is where we will store the filter row while we make it */
    $content = "<tr role=\"row\" class=\"filter\">";
    /** @var array|boolean $filter Iterate over all of the possible filters */
    foreach($this->_gsColumnFilters as $filter){
      /** Check if this filter is set to false */
      if($filter===false){
        /** Return an empty content area */
        $content .= "<td></td>";
        /** End this iteration */
        continue;
      }
      $content .= $this->_dtMakeFilter($filter);
    }
    /** Return the filter row */
    return "{$content}</tr>";
  }

  /**
   * Figure out what kind of filter needs to be made and call its custom make function
   * @method _dtMakeFilter
   * @param $filter
   * @return string
   */
  protected function _dtMakeFilter($filter)
  {
    switch($filter['type']){
      /** Search String Filter */
      case self::searchFilter: return $this->_dtSearchStringFilter($filter); break;
      /** Handle being passed an invalid filter type */
      default: return "<td><center>Invalid Filter</center></td>"; break;
    }
  }

  /**
   * Make the search filter cell
   * @method _dtSearchStringFilter
   * @param $filter
   * @return string
   */
  protected function _dtSearchStringFilter($filter)
  {
    /** @var null|string $id default the value to null */
    $id=null;
    /** @var null|string $name default the value to null */
    $name=null;
    /** @var null|string $placeholder default the value to null */
    $placeholder=null;
    /** @var null|string $class default the value to null */
    $class=null;
    /** @var null|string $filterName default the value to null */
    $filterName=null;
    /** @var integer $filterType Set the filter to the searchFilter key */
    $filterType="data-datatable-filter-type=\"{self::searchFilter}\"";
    /** Check if we have an id */
    if(isset($filter['id'])&&is_string($filter['id'])){
      /** @var string $id update the value */
      $id=" id=\"{$filter['id']}\"";
    }
    /** Check if we have an name */
    if(isset($filter['name'])&&is_string($filter['name'])){
      /** @var string $name update the value */
      $name=" name=\"{$filter['name']}\"";
    }
    /** Check if we have an placeholder */
    if(isset($filter['placeholder'])&&is_string($filter['placeholder'])){
      /** @var string $placeholder update the value */
      $placeholder=" placeholder=\"{$filter['placeholder']}\"";
    }
    /** Check if we have an class */
    if(isset($filter['class'])&&is_string($filter['class'])){
      /** @var string $class update the value */
      $class=" class=\"{$filter['class']}\"";
    }
    /** @var string $content Store the content for the filter here while we build it */
    $content = "<td>";
      $content .= "<input type=\"text\"{$id}{$name}{$placeholder}{$class}{$filterName}{$filterType} />";
    return "{$content}</td>";
  }

  /**
   * Make the body of the datatable
   * @method _dtBody
   * @return string
   */
  protected function _dtBody()
  {
    /** @var array $rows */
    $rows = $this->_initialQuery();
    /** @var string $content */
    $content = "<tbody>";
    /** @var array $row Loop through the rows*/
    if($rows) {
      foreach ($rows as $row) {
        /** @var array $row Overwrite the row with the formatted data */
        $row = $this->_rowFormatter ($row);
        /** Use makeRow to get the row content and append it to content */
        $content .= $this->_makeRow ($row);
      }
    }
    /** return the body */
    return "{$content}</tbody>";
  }

  /**
   * Make a single row in the active datatable
   * @method _makeRow
   * @param $row
   * @return string
   */
  protected function _makeRow($row)
  {
    /** @var integer $index current index */
    $index = 0;
    /** @var string $content */
    $content = "<tr>";
    /** @var string $cell loop over the cells */
    foreach($row as $cell){
      /** @var string $style Get the styles from the column definitions */
      $style = ((isset($this->_gsColumnDefinitions[$index]['style']))?" style=\"{$this->_gsColumnDefinitions[$index]['style']}\"":"");
      /** Append the cell to the content */
      $content .= "<td{$style}>{$cell}</td>";

      /** Move forward to the next index */
      $index++;
    }
    return "{$content}</tr>";
  }
  /****************************************************/
  /*                  DATABASE METHODS                */
  /****************************************************/

  /**
   * Make the limit string for the query
   * @method _limit
   * @param null $start
   * @param null $end
   * @return string
   */
  protected function _limit($start = null, $end = null )
  {
    /** Check if null was passed */
    if(is_null($start)){
      /** @var integer $start default it to 0 */
      $start = 0;
    }
    /** Check if null was passed */
    if(is_null($end)){
      /** @var integer $end Default it to the result limit */
      $end = $this->_dsResultLimit;
    }
    /** Return the limit string */
    return " LIMIT {$start}, {$end}";
  }
  /****************************************************/
  /*                  DATABASE METHODS                */
  /****************************************************/

  /**
   * Connect to the database
   * @method _connect
   * @param                             $dbname
   * @param \Framework\Modulus\DBConfig $config
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  protected function _connect($dbname, DBConfig $config)
  { $this->_database = $this->_pdoDatabses->connect($dbname, $config); }

  /**
   * Reset our database connection
   * @method _reConnect
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  protected function _reConnect()
  {
    /** Reset our database connection */
    $this->_pdoDatabses->resetconnection($this->_databaseName);
    /** @var mixed handle _database reconnect */
    $this->_database = $this->_pdoDatabses->connect($this->_databaseName);
  }

  /**
   * Prepare a query
   * @method _prepare
   * @param $query
   * @return mixed
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  protected function _prepare($query)
  {
    /** Wrap up for safty */
    try{
      /** Return the prepared query */
      return $this->_database->prepare($query);
    } catch (\PDOException $exception) {
      /** Rethrow the error correctly */
      throw new \Framework\Exceptional\PDOdatabasesException(
        $exception->getMessage(),
        $exception->getCode(),
        1,
        $exception->getFile(),
        $exception->getLine(),
        $exception
      );
    }
  }

  /**
   * Start a database transaction
   * @method _startTransaction
   */
  protected function _startTransaction()
  { $this->_database->beginTransaction(); }

  /**
   * Roll the transaction back
   * @method _rollbackTransaction
   */
  protected function _rollbackTransaction()
  { $this->_database->rollBack(); }

  /**
   * Commit the active transaction
   * @method _commitTransaction
   */
  protected function _commitTransaction()
  { $this->_database->commit(); }

  /**
   * Execute a prepared statment
   * @method _execute
   * @param \PDOStatement $preparedStatement
   * @return \PDOStatement
   * @throws \Framework\Exceptional\PDOdatabasesException
   */
  protected function _execute(\PDOStatement $preparedStatement)
  {
    try{
      /** Try and just execute the statement */
      $preparedStatement->execute();
    } catch (\PDOException $exception) {
      /** CHeck if this is a bad connection exception */
      if($exception->getCode() != 2006){
        /** This is not a bad connection rethrow it */
        throw new \Framework\Exceptional\PDOdatabasesException(
          $exception->getMessage(),
          $exception->getCode(),
          1,
          $exception->getFile(),
          $exception->getLine(),
          $exception
        );
      }
      /** Bad connection lets reconnect and try again */
      $this->_reConnect();
      /** Rerun the execute */
      $this->_execute($preparedStatement);
    }
    /** return the statment */
    return $preparedStatement;
  }
  /****************************************************/
  /*                  REQUEST METHODS                 */
  /****************************************************/


  protected function _eRequestParse()
  {
    /** @var array $_erData */
    $this->_erData = $_POST;
    /** @var array $_erColumns */
    $this->_erColumns = $this->_erData['columns'];
    /** @var integer $_erRecordStart */
    $this->_erRecordStart = intval($this->_erData['start']);
    /** @var integer $_erRecordEnd */
    $this->_erRecordEnd = intval($this->_erData['length']);
    if($this->_dsSortable) {
      /** @var integer $_erOrderColumnIndex */
      $this->_erOrderColumnIndex = intval ($this->_erData['order'][0]['column']);
      /** @var string $_erOrderDirection */
      $this->_erOrderDirection = $this->_erData['order'][0]['dir'];
    }
    /** @var integer $_erDrawCount */
    $this->_erDrawCount = intval($this->_erData['draw']);
    /** @var integer $_erLength */
    $this->_erLength = intval($this->_erData['length']);
    /** @var boolean $_erSearchRegex */
    $this->_erSearchRegex = boolval($this->_erData['search']['regex']);
    /** @var string $_erSearchValue */
    $this->_erSearchValue = $this->_erData['search']['value'];
  }

  /**
   * Handle the response for all datatalbe requests
   * @method _eResponse
   * @param $records
   * @param $draw
   * @param $recordsTotal
   * @param $recordsFiltered
   */
  protected function _eResponse($records, $draw, $recordsTotal, $recordsFiltered)
  {
    /** @var array $records make our records array */
    $result = [];
    /** Add the data part to the records so we can run them in */
    $result["data"] = [];
    /** Add the draw property to the records array */
    $result['draw'] = $draw;
    /** Add the record count to the result */
    $result['recordsTotal'] = $recordsTotal;
    /** Add the total records filtered to the data array */
    $result['recordsFiltered'] = $recordsFiltered;
    /** Make sure we have records */
    if($records){
      foreach( $records as &$resultRow){
        /** @var array $row predefine our row */
        $row = [];
        /** @var array $resultRow format the row */
        $resultRow = $this->_rowFormatter($resultRow);
        /** @var string $cell iterate over the cells */
        foreach($resultRow as &$cell){
          /** Push each cell into the row */
          array_push($row,$cell);
        }
        /** Push the row into the result data */
        array_push($result['data'],$row);
      }
    }
    /** Return the response to the requester */
    echo json_encode($this->_eResponseCleaner($result));
    /** Exit to avoid anything happening after this point */
    exit();
  }

  /**
   * Clean the response array
   * @method _eResponseCleaner
   * @param $array
   * @return mixed
   */
  protected function _eResponseCleaner($array)
  {
    array_walk_recursive($array, function(&$item, $key){
      if(!mb_detect_encoding($item, 'utf-8', true)){
        $item = utf8_encode($item);
      }
    });
    return $array;
  }
}