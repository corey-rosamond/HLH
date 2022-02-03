<?php
/**
 * FunnelPage
 *
 * @package App\Abstracts
 * @version 1.2.0
 */
namespace App\Abstracts;

/**
 * FunnelPage
 *
 * This is the base funnelPage object all funnel pages extend this page. That gives
 * them all of the redundant functionality we would normally have to write into each page object
 * INFORMATION
 *
 * FUNNEL MODE
 * fModeLive            = 0
 * fModeDevelopment     = 1
 * fModeMaintenance     = 2
 * fModeComingSoon      = 3
 * fModeOutOfStock      = 4
 *
 * DATA MEMBERS
 * dataPrefix           = 0
 * dataGoogleAnalytics  = 1
 * dataAssetLocation    = 2
 * dataConfiguration    = 3
 * dataTitle            = 4
 * dataHeader           = 5
 * dataContent          = 6
 * dataFooter           = 7
 * dataSDest            = 8
 * dataFDest            = 9
 * dataTokens           = 10
 * dataProducts         = 11
 * dataFreshDesk        = 12
 *
 * ASSET LOCATIONS
 * aLocationS3          = 0
 * aLocationCF          = 1
 *
 * EXTRA CONTENT SECTIONS
 * eSectionOne          = 0
 * eSectionTwo          = 1
 * eSectionThree        = 2
 * eSectionFour         = 3
 *
 * EXTRA CONTENT TYPES
 * cTypeEmpty           = 0
 * cTypeContentOnly     = 1
 * cTypeTitleAnContent  = 2
 */
\Framework\_IncludeCorrect(APP_ROOT."Abstracts".DSEP."Document.php");
class FunnelPage extends Document
{
  /****************************************************/
  /*                    FUNNEL MODE                   */
  /****************************************************/
  /** @const integer fModeLive */
  const fModeLive = 0;
  /** @const integer fModeDevelopment */
  const fModeDevelopment = 1;
  /** @const integer fModeMaintenance */
  const fModeMaintenance = 2;
  /** @const integer fModeComingSoon */
  const fModeComingSoon = 3;
  /** @const integer fModeOutOfStock */
  const fModeOutOfStock = 4;
  /****************************************************/
  /*                   DATA MEMBERS                   */
  /****************************************************/
  /** @const integer dataPrefix */
  const dataPrefix = 0;
  /** @const integer dataAnalytics */
  const dataGoogleAnalytics = 1;
  /** @const integer dataResourceLocation */
  const dataAssetLocation = 2;
  /** @const integer dataConfiguration */
  const dataConfiguration = 3;
  /** @const integer dataTitle */
  const dataTitle = 4;
  /** @const integer dataHeader */
  const dataHeader = 5;
  /** @const integer dataContent */
  const dataContent = 6;
  /** @const integer dataFooter */
  const dataFooter = 7;
  /** @const integer dataSDest */
  const dataSDest = 8;
  /** @const integer dataFDest */
  const dataFDest = 9;
  /** @const integer dataTokens */
  const dataTokens = 10;
  /** @const integer dataProducts */
  const dataProducts = 11;
  /** @const integer dataFreshDesk */
  const dataFreshDesk = 12;
  /****************************************************/
  /*                   ASSET LOCATION                 */
  /****************************************************/
  /** @const integer aLocationS3 */
  const aLocationS3 = 0;
  /** @const integer aLocationCF */
  const aLocationCF = 1;
  /****************************************************/
  /*              EXTRA CONTENT SECTIONS              */
  /****************************************************/
  /** @const integer eSectionOne */
  const eSectionOne = 0;
  /** @const integer eSectionTwo */
  const eSectionTwo = 1;
  /** @const integer eSectionThree */
  const eSectionThree = 2;
  /** @const integer eSectionFour */
  const eSectionFour = 3;
  /****************************************************/
  /*                EXTRA CONTENT TYPES               */
  /****************************************************/
  /** @const integer cTypeEmpty */
  const cTypeEmpty = 0;
  /** @const integer cTypeContentOnly */
  const cTypeContentOnly = 1;
  /** @const integer cTypeContentAndTitle */
  const cTypeTitleAnContent = 2;
  /****************************************************/
  /*                CONTROLLER OBJECTS                */
  /****************************************************/
  /** @var \App\System $_system Pointer to the system object */
  protected $_system;
  /** @var \Framework\Commander\Funnel $_cFunnel */
  protected $_cFunnel;
  /****************************************************/
  /*                     SETTINGS                     */
  /****************************************************/
  /** @var integer $_sFunnelMode */
  protected $_sFunnelMode = self::fModeDevelopment;
  /** @var boolean $_sHideErrors */
  protected $_sHideErrors = false;
  /** @var boolean $_sPreQualify */
  protected $_sPreQualify = false;
  /****************************************************/
  /*                   DATA STORAGE                   */
  /****************************************************/
  /** @var array $_data */
  protected $_data = [
    self::dataPrefix => null,
    self::dataGoogleAnalytics => null,
    self::dataAssetLocation => null,
    self::dataConfiguration => null,
    self::dataTitle => null,
    self::dataHeader => null,
    self::dataContent => null,
    self::dataFooter => null,
    self::dataSDest => null,
    self::dataFDest => null,
    self::dataTokens => null,
    self::dataProducts => null,
    self::dataFreshDesk => null
  ];
  /** @var array $_aLocation */
  protected $_aLocation = [
    self::aLocationS3 => "//s3-us-west-1.amazonaws.com/hhhmedia/",
    self::aLocationCF => null
  ];
  /** @var array $_errors */
  protected $_errors = [];

  /**
   * Construct the base page object and setup the environment for other pages
   * @method _construct
   * @param $system
   * @return \App\Abstracts\FunnelPage
   */
  public function __construct($system)
  {
    $this->_system  = $system;
    $this->_cFunnel = $system->_cFunnel;
    $this->_sFunnelMode = $this->_cFunnel->getMode();
    if($this->_sPreQualify){
      $this->_preQualify();
    }
    $this->_data[self::dataPrefix] = $system->_dFunnelHost;
    try {
      $this->_data[self::dataConfiguration] = $system->_cFunnel->getConfiguration ();

    } catch ( \Framework\Exceptional\BaseException $exception ){
      $this->_data[self::dataConfiguration] = [];
    }
    $this->_data[self::dataTokens] = $system->_cFunnel->getTokens();
    $this->_data[self::dataTitle] = $system->_cFunnel->getTitle();
    $this->_data[self::dataHeader] = $system->_cFunnel->getHeader();
    $this->_data[self::dataFooter] = $system->_cFunnel->getFooter();
    $this->_data[self::dataContent] = $this->_parseContent($system->_cFunnel->getContent());

    $this->_data[self::dataAssetLocation] = $this->_assetLocation($system->_cFunnel->getAssetDirectory());
    $this->_data[self::dataSDest] = $this->_successNext();
    $this->_data[self::dataFDest] = $this->_failNext();
    if(isset($this->_data[self::dataConfiguration]['Products'])){
      $this->_data[self::dataProducts] = $this->_cFunnel->getProducts($this->_data[self::dataConfiguration]['Products']);
    }
    $this->_assetPath = $this->_data[self::dataAssetLocation];
    $this->_tokens = $this->_data[self::dataTokens];
    return parent::__construct($system);
  }

  /**
   * Render the page
   * @method renderPage
   */
  public function renderPage()
  {
    $this->_cFunnel->registerToken('{destination-success}',$this->_data[self::dataSDest]);
    $this->_cFunnel->registerToken('{destination-failure}',$this->_data[self::dataFDest]);
    $this->_cFunnel->registerToken('{image-location}', $this->_data[self::dataAssetLocation]."images/");
    $this->_tokens = $this->_cFunnel->getTokens();
    /** Render the document */
    $this->_render(
      $this->_documentStart($this->_data[self::dataTitle]).
      "<div id=\"funnel-container\">".
      $this->_header($this->_data[self::dataHeader]).
      $this->_contentAreas($this->_data[self::dataContent]).
      "</div>".
      $this->_footer($this->_data[self::dataFooter]).
      $this->_javaScriptObject().
      $this->_documentEnd()
    );
  }

  /**
   * Generate Content Areas
   * @method _contentAreas
   * @param $data
   * @return string
   */
  protected function _contentAreas($data)
  {
    return $this->_errorContainer().
    $this->_extraContent(self::eSectionOne, $data['extra-one']).
    $this->_extraContent(self::eSectionTwo, $data['extra-two']).
    $this->_content($data['content']).
    $this->_extraContent(self::eSectionThree, $data['extra-three']).
    $this->_extraContent(self::eSectionFour, $data['extra-four']);
  }

  /**
   * Default content area this is only used if the page does not have
   * its own page specific version
   * @method _content
   * @param $data
   * @return string
   */
  protected function _content($data)
  { return $this->_contentBox($data['content'], "funnel-content", $data['title']); }

  /**
   * Generate the page extra content area requested
   * @method _extraContent
   * @param $section
   * @param $data
   * @return null
   */
  protected function _extraContent($section, $data)
  {
    /** Check if this is an empty content section */
    if($data['type']==self::cTypeEmpty){
      /** Return empty */
      return null;
    }
    /** Build a content area using the type */
    switch($data['type']){
      case self::cTypeContentOnly:
        return $this->_contentBox($data['content'], "extra-content-{$section}");
        break;
      case self::cTypeTitleAnContent:
        return $this->_contentBox($data['content'], "extra-content-{$section}", $data['title']);
        break;
    }
  }

  /**
   * Create the content box wrapper
   * @method _contentBox
   * @param $id
   * @param $title
   * @param $content
   * @return string
   */
  protected function _contentBox($content, $id="funnel-content", $title=null)
  {
    if(!is_null($title)){
      $title = "<h1>{$title}</h1>";
    }
    return "<div id=\"{$id}\" class=\"funnel-content-box\">{$title}{$content}</div>";
  }

  /**
   * Generate the error message display
   * @method _ErrorContainer
   * @return mixed
   */
  protected function _errorContainer()
  {
    /** Check if the error data session var is not set */
    if(!isset($_SESSION[$this->_data[self::dataPrefix]]['error-data'])){
      /** The session variable is not set so we have no errors */
      return null;
    }
    /** Check the count of errors in the error data session var is 0 */
    if(sizeof($_SESSION[$this->_data[self::dataPrefix]]['error-data'])==0){
      /** 0 Errors to display */
      return null;
    }
    /** @var string $eMessages This is where we are going to write the error messages */
    $eMessages = "";
    /** @var string $eMessage Loop through the error messages */
    foreach($_SESSION[$this->_data[self::dataPrefix]]['error-data'] as $eMessage){
      /** Append the $eMessage to $eMessages */
      $eMessages .= "<li>{$eMessage}</li>";
    }
    /** Write the error messages to the default error display div */
    return "<div id=\"funnel-default-error-display\"><ul>{$eMessages}</ul></div>";
  }

  /**
   * Write the funnel header to the document body
   * @method _header
   * @param string $content
   * @return string
   */
  protected function _header($content)
  { return "<div id=\"funnel-header\">{$content}</div>"; }

  /**
   * Write the funnel footer to the document body
   * @method _footer
   * @param string $content
   * @return string
   */
  protected function _footer($content)
  {
    return "<div id=\"funnel-footer\">{$content}</div>".
    $this->_googleAnalytics().
    $this->_freshDesk();
  }

  /**
   * Return Google Analytics
   * @method _googleAnalytics
   * @return null
   */
  protected function _googleAnalytics()
  {
    /** Check if we are in development */
    if($this->_sFunnelMode==self::fModeDevelopment){
      /** Return null we dont need google analytics in development */
      return null;
    }
    /** Check if google analytics code was provided */
    if(is_null($this->_data[self::dataGoogleAnalytics])){
      /** Return null */
      return null;
    }
    /** Return the google analytics code */
    return $this->_data[self::dataGoogleAnalytics];
  }

  /**
   * Return Fresh Desk
   * @method _freshDesk
   * @return null
   */
  protected function _freshDesk()
  {
    /** Check if we are in development */
    if($this->_sFunnelMode==self::fModeDevelopment){
      /** Return null we dont need fresh desk in development */
      return null;
    }
    /** Check if fresh desk code was provided */
    if(is_null($this->_data[self::dataFreshDesk])){
      /** Return null */
      return null;
    }
    /** Return the fresh desk code */
    return $this->_data[self::dataFreshDesk];
  }

  /****************************************************/
  /*                  Support Method                  */
  /****************************************************/

  /**
   * Return the asset location
   * @method _assetLocation
   * @param $location
   * @return string
   */
  protected function _assetLocation($location)
  {
    if(is_null($location)){
      return $this->_aLocation[self::aLocationS3]."FunnelResources/".$this->_data[self::dataPrefix]."/";
    }
    return $this->_aLocation[self::aLocationS3]."FunnelResources/{$location}/";
  }

  /**
   * Parse the content for this page and return flat text or the page
   * content array. If it is not an array put us in text mode
   * @method _parseContent
   * @param string $content
   * @return mixed
   */
  protected function _parseContent($content)
  {
    $result = json_decode($content, true);
    if(is_array($result)){
      return $result;
    }
    return $content;
  }

  /**
   * Generate the products inputs as by creating it this way on every
   * we avoid having to worry about it and the products show in post as
   * as an array
   * @method _products
   * @return string
   */
  protected function _products()
  {
    $product = '';
    /** Iterate over the products keys and their values */
    foreach($this->_data[self::dataProducts] as $key=>$value){
      /** Append this input to the $product var */
      $product .= "<input type=\"hidden\" value=\"{$key}\" name=\"products[]\">";
    }
    /** return the product inputs */
    return $product;
  }

  /**
   * This builds the frameworks javascript instructions container
   * The simplest way of explaining this is. Any thing we want the javascript
   * counterpart for the funnel to do. We pass it instructions here. Then when the page
   * loads the framework grabs this dom object and reads all the attributes out
   * as instructions
   * @method _JavaScriptObject()
   * @return string
   */
  protected function _javaScriptObject()
  {
    /** Check if the javascript config object is set */
    if(isset($this->_data[self::dataConfiguration]['JavaScript'])){
      /** @var string $JavaScriptInstructions */
      $javaScriptInstructions = "";
      /** Iterate over all of the javascript instructions and add data attributes for each */
      foreach($this->_data[self::dataConfiguration]['JavaScript'] as $instruction => $detail){
        /** Add this key value pair the the DIV */
        $javaScriptInstructions .= " {$instruction}=\"{$detail}\"";
      }
      /** Write the DIV to the buffer */
      return "<div{$javaScriptInstructions} id=\"framework-instruction-object\"></div>";
    }
    return null;
  }

  /****************************************************/
  /*                Navigation Methods                */
  /****************************************************/

  /**
   * The next success destination
   * @method _successNext
   * @return string
   */
  protected function _successNext()
  {
    $map = $this->_getMap(self::successNext);
    if(!$map){
      return null;
    }
    foreach($map as $dest) {
      $destination = $this->_destination($dest);
      if ($destination) {
        return $destination;
      }
    }
    return null;
  }

  /**
   * Find the next fail destination
   * @method _failNext
   * @return string
   */
  protected function _failNext()
  {
    $map = $this->_getMap(self::failureNext);
    if(!$map){
      return null;
    }
    foreach($map as $dest) {
      $destination = $this->_destination($dest);
      if ($destination) {
        return $destination;
      }
    }
    return null;
  }

  /**
   * Get the destination variables
   * @method _destination
   * @param $destination
   * @return array
   */
  protected function _destination($destination)
  {
    $dest = [];
    if($destination[0]==self::typeEvent){
      list($dest['type'], $dest['location'], $dest['string']) = $destination;
      return $this->_getDestinationLocation($dest);
    }
    list($dest['type'], $dest['location']) = $destination;
    return $this->_getDestinationLocation($dest);
  }

  /**
   * Get the destination location string
   * @method _getDestinationLocation
   * @param $destination
   * @return bool|string
   */
  protected function _getDestinationLocation($destination)
  {
    $pageData = $this->_getPageByType($destination['location']);
    if(!$pageData){
      return false;
    }
    if($destination['type']==self::typeEvent){
      return "/Event/{$destination['string']}";
    }
    return "/{$pageData['name']}";
  }

  /**
   * Return the map for the event type and selector
   * @method _getMap
   * @param $selector
   * @return bool
   */
  protected function _getMap($selector)
  {
    $map = $this->_map();
    $type = $this->_type();
    if(!isset($map[$type][$selector])){
      return false;
    }
    return $map[$type][$selector];
  }

  public function pageType()
  {
    return $this->_pageType;

  }
}
