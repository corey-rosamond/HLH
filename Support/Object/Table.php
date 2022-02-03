<?php
/**
 * Table
 *
 * @package Framework\Support\Object
 * @version 1.0.0
 */
namespace Framework\Support\Object;

/**
 * Table
 *
 * This helps generate a very clean table
 */
class Table{

  /***************************
  *      Object Variables    *
  /**************************/

  /**
   * Table Object
   * @var     \Framework\Dominator\Objects\Table  $this->table
   * @access  private
   */
  private $table;

  /**
   * Thead Object
   * @var     \Framework\Dominator\Objects\Thead  $this->thead
   * @access  private
   */
  private $thead;

  /**
   * Tbody Object
   * @var     \Framework\Dominator\Objects\Tbody  $this->tbody
   * @access  private
   */
  private $tbody;

  /**
   * Tfoot Object
   * @var     \Framework\Dominator\Objects\Tfoot  $this->tfoot
   * @access  private
   */
  private $tfoot;

  /*********************************
  *    DOM CONFIGURATION OBJECTS   *
  /********************************/

  /**
   * Table configuration object
   * @var     object  $this->tableConfiguration
   * @access  private
   */
  private $tableConfiguration = [ "id" => false, "name" => false, "attributes" => [ "cellspacing" => "0", "cellpadding" => "0" ], "classes" => [ "framework-table" ], "styles"  => false, "text" => false ];

  /**
   * Thead configuration object
   * @var     object  $this->theadConfiguration
   * @access  private
   */
  private $theadConfiguration = [ "id" => false, "name" => false, "attributes" => false, "classes" => [ "framework-table-head" ], "styles"  => false, "text" => false ];

  /**
   * Tbody configuration object
   * @var     object  $this->tbodyConfiguration
   * @access  private
   */
  private $tbodyConfiguration = [ "id" => false, "name" => false, "attributes" => false, "classes" => [ "framework-table-body" ], "styles"  => false, "text" => false ];

  /**
   * Tfoot configuration object
   * @var     false|object  $this->tfootConfiguration
   * @access  private
   */
  private $tfootConfiguration = [ "id" => false, "name" => false, "attributes" => false, "classes" => [ "framework-table-head" ], "styles"  => false, "text" => false ];

  /**
   * Tr configuration object
   * @var     false|object  $this->trConfiguration
   * @access  private
   */
  private $trConfiguration = [ "id" => false, "name" => false, "attributes" => false, "classes" => false, "styles"  => false, "text" => false ];

  /**
   * Thead th configuration for the current column
   * @var     array         $this->hcolconfiguration
   * @access  private
   */
  private $headColumnConfiguration = [];

  /**
   * Tbody td configuration for the current column
   * @var     array         $this->bodyColumnConfiguration
   * @access  private
   */
  private $bodyColumnConfiguration = [];

  /**
   * Tfoot td configuration for the current column
   * @var     array         $this->footColumnConfiguration
   * @access  private
   */
  private $footColumnConfiguration = [];

  /***************************
  *     Setting Variables    *
  /**************************/

  /**
   * The class to use for even rows
   * @var     string  $this->evenClass
   * @access  public
   */
  private $evenClass = 'even-row';

  /**
   * The class to use for odd rows
   * @var     string  $this->oddClass
   * @access  public
   */
  private $oddClass = 'odd-row';

  /***************************
  *       DATA Variables     *
  /**************************/

  /**
   * data for the header
   * @var     array   $this->headData
   * @access  private
   */
  private $headData = [ ['one', 'two', 'three', 'four', 'five' ] ];

  /**
   * Data for the body
   * @var     array   $this->bodyData
   * @access  private
   */
  private $bodyData = [
    [ '1', '2', '3', '4', '5' ],
    [ '1', '2', '3', '4', '5' ],
    [ '1', '2', '3', '4', '5' ],
    [ '1', '2', '3', '4', '5' ],
    [ '1', '2', '3', '4', '5' ],
    [ '1', '2', '3', '4', '5' ]
  ];

  /**
   * Data for the footer
   * @var     array   $this->footData
   * @access  private
   */
  private $footData = [];


  /*********************************
  *   DEFAULT DOM CONFIGURATIONS   *
  /********************************/

  /**
   * Th default configuration
   * @var     object  $this->thDefaultConfiguration
   * @access  private
   */
  private $thDefaultConfiguration =  [ "id" => false, "name" => false, "attributes" => false, "classes" => false, "styles"  => false, "text" => false ];

  /**
   * Td default configuration
   * @var     object  $this->tdDefaultConfiguration
   * @access  private
   */
  private $tdDefaultConfiguration =  [ "id" => false, "name" => false, "attributes" => false, "classes" => false, "styles"  => false, "text" => false ];

  /***************************
  *    	  BASE METhODS       *
  /**************************/

  /**
   * This function builds out all of the options that are in the table object
   * @method  __construct
   * @param   array|false      $headData                  Array of data to populate into the section or false
   * @param   array|false      $bodyData                  Array of data to populate into the section or false
   * @param   array|false      $footData                  Array of data to populate into the section or false
   * @param   false|object     $tableConfiguration        Dom Configuration object or false if not provided
   * @param   false|object     $theadConfiguration        Dom Configuration object or false if not provided
   * @param   false|object     $tbodyConfiguration        Dom Configuration object or false if not provided
   * @param   false|object     $tfootConfiguration        Dom Configuration object or false if not provided
   * @param   false|object     $trConfiguration           Dom Configuration object or false if not provided
   * @param   false|array      $headColumnConfiguration   Dom Configuration object or false if not provided
   * @param   false|array      $bodyColumnConfiguration   Dom Configuration object or false if not provided
   * @param   false|array      $footColumnConfiguration   Dom Configuration object or false if not provided
   * @param   false|string     $evenClass                 String with class to use when row is odd or false
   * @param   false|string     $oddClass                  String with class to use when row is even or false
   * @return  $this
   * @access  public
   */
  public function __construct(
    $headData = false,
    $bodyData = false,
    $footData = false,
    $tableConfiguration = false,
    $theadConfiguration = false,
    $tbodyConfiguration = false,
    $tfootConfiguration = false,
    $trConfiguration = false,
    $headColumnConfiguration = false,
    $bodyColumnConfiguration = false,
    $footColumnConfiguration = false,
    $evenClass = false,
    $oddClass = false
  ){
    /** If we have head data seet it */
    if( $headData ){ $this->headData = $headData; }
    /** If we have body data seet it */
    if( $bodyData ){ $this->bodyData = $headData; }
    /** If we have foot data seet it */
    if( $footData ){ $this->footData = $headData; }
    /** if we have tableConfiguration set it */
    if( $tableConfiguration ){ $this->tableConfiguration = $tableConfiguration; }
    /** if we have theadConfiguration set it */
    if( $theadConfiguration ){ $this->theadConfiguration = $theadConfiguration; }
    /** if we have tbodyConfiguration set it */
    if( $tbodyConfiguration ){ $this->tbodyConfiguration = $tbodyConfiguration; }
    /** if we have theadConfiguration set it */
    if( $tfootConfiguration ){ $this->tfootConfiguration = $tfootConfiguration; }
    /** if we have trConfiguration set it */
    if( $trConfiguration ){ $this->trConfiguration = $trConfiguration; }
    /** if we have headColumnConfiguration set it */
    if( $headColumnConfiguration ){ $this->headColumnConfiguration = $headColumnConfiguration; }
    /** if we have bodyColumnConfiguration set it */
    if( $bodyColumnConfiguration ){ $this->bodyColumnConfiguration = $bodyColumnConfiguration; }
    /** if we have footColumnConfiguration set it */
    if( $footColumnConfiguration ){ $this->footColumnConfiguration = $footColumnConfiguration; }
    /** if we have evenClass set it */
    if( $evenClass ){ $this->evenClass = $evenClass; }
    /** if we have oddClass set it */
    if( $oddClass ){ $this->oddClass = $oddClass; }
    /** Build the table it is the foundation */
    $this->table = new \Framework\Dominator\Objects\Table( $this->tableConfiguration );
    /** Append the the new thead object to the table and save the reference */
    $this->thead = $this->table->append( ( new \Framework\Dominator\Objects\Thead( $this->theadConfiguration ) ) );
    /** Append the the new thead object to the table and save the reference */
    $this->tbody = $this->table->append( ( new \Framework\Dominator\Objects\Tbody( $this->tbodyConfiguration ) ) );
    /** Append the the new thead object to the table and save the reference */
    $this->tfoot = $this->table->append( ( new \Framework\Dominator\Objects\Tfoot( $this->tfootConfiguration ) ) );
    /** Done return this */
    return $this;
  }

  /*********************************
  *   	   CONSTRUCT METHODS       *
  /********************************/

  /**
   * Construct all the elements in the data objects
   * @method construct
   * @return boolean
   * @access private
   */
  private function construct(){
    /** Construct the head */
    $this->constructSection( $this->thead, "\Framework\Dominator\Objects\Th", $this->headData, $this->trConfiguration, $this->headColumnConfiguration, $this->thDefaultConfiguration );
    /** Construct the body */
    $this->constructSection( $this->tbody, "\Framework\Dominator\Objects\Td", $this->bodyData, $this->trConfiguration, $this->bodyColumnConfiguration, $this->tdDefaultConfiguration );
    /** Construct the foot */
    $this->constructSection( $this->tfoot, "\Framework\Dominator\Objects\Td", $this->footData, $this->trConfiguration, $this->footColumnConfiguration, $this->tdDefaultConfiguration );
    /** Done Return */
    return true;
  }

  /**
   * Construct the head section of this table
   * @method  constructSection
   * @param   DocumentObject                      $section                    Reference to the section we are constructing
   * @param   string                              $cellName                   String representation of the cell this section builds
   * @param   array                               $data                       MultiDimensional array of table data
   * @param   array                               $trConfiguration            TableRow configuration object
   * @param   array                               $columnConfiguration        Array of configuration objects relating to their column
   * @param   object                              $cellDefaultConfiguration   Default TH Configuration Object
   * @return  boolean                                                         Returns true when complete
   * @uses    \Framework\Dominator\Objects\Tr
   * @uses    \Framework\Dominator\Objects\Td
   * @uses    \Framework\Dominator\Objects\Th
   * @uses    \Framework\Dominator\Objects\Text
   * @access  private
   */
  private function constructSection( $section, $cellName, $data, $trConfiguration, $columnConfiguration, $cellDefaultConfiguration ){
    /** If name was provided set it */
    if( count( $data ) === 0 ){ return false; }
    /** Get the first data object */
    $row = reset( $data );
    /** Ok we have data Get the columnQuantity */
    $columnQuantity = count( reset( $row ) );
    /** Get the columnConfiguration Quantity */
    $columnConfigurationQuantity = count( $columnConfiguration );
    /** Make our initial TR */
    $TR = $this->appendTR( $section, $trConfiguration );
    /** Loop through the cells in the row */
    foreach( $row as $key => $value ){
      /** Check if the columnConfiguration has this index */
      if( !isset($columnConfiguration[$key])){
        /** Configuration missing add it */
        $columnConfiguration[$key] = $cellDefaultConfiguration;
      }
      /** Append new Cell to TR and return */
      $CELL = $this->appendTD( $TR, $columnConfiguration[$key] )->setText( $value );
    }
    /** Loop throught the data members */
    foreach( $data as $row ){
      /** Make and capture a reference to the new TR */
      $TR = $this->appendTR( $section, $trConfiguration );
      foreach( $row as $columen => $cell ){
        /** Loop through the columns */
        $TD = $this->appendTD( $TR, $columnConfiguration[ $columen ] )->setText( $value );
      }
    }
  }

  /*********************************
  *   	   APPEND METHODS          *
  /********************************/

  /**
   * Append the tr object to the target and
   * pass along the configuration object
   * @method  appendTR
   * @param   DomcumentObject     $target         The object we are going to append to
   * @param   array               $configuration  Array of DocumentObject configuration settings
   * @return  DocumentObject                      Returns a reference to the location of the object in the stack
   * @access private
   */
  private function appendTR( $target, $configuration = false )
  { return $target->append( ( new \Framework\Dominator\Objects\Tr( $configuration ) ) ); }

  /**
   * Append the td object to the target and
   * pass along the configuration object
   * @method appendTD
   * @param  ConfigurationObject  $target The location to append this to
   * @return DocumentObject               Reference to the new object in the collection
   * @access private
   */
  private function appendTD( $target, $configuration = false)
  { return $target->append( ( new \Framework\Dominator\Objects\Td( $configuration ) ) ); }

  /**
   * Append the th object to the target and
   * pass along the configuration object
   * @method appendTH
   * @param  ConfigurationObject  $target The location to append this to
   * @return DocumentObject               Reference to the new object in the collection
   * @access private
   */
  private function appendTH( $target, $configuration = false )
  { return $target->append( ( new \Framework\Dominator\Objects\Th( $configuration ) ) ); }

  /**
   * Append the text object to the target and
   * pass along the configuration object
   * @method appendText
   * @param  ConfigurationObject  $target The location to append this to
   * @return DocumentObject               Reference to the new object in the collection
   * @access private
   */
  private function appendText( $target, $configuration = false)
  { return $target->append( ( new \Framework\Dominator\Objects\Text( $configuration ) ) ); }

  /*********************************
  *   	   RENDER METHODS          *
  /********************************/

  /**
   * Render the page
   * @method render
   * @return [type] [description]
   * @access public
   */
  public function render(){
    $this->construct();
    /** Construct done render it */
    return $this->table->render();
  }
}
