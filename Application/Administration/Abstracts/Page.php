<?php
/**
 * The Base page for administration
 *
 * @package App\Event
 * @version 1.0.0
 */
namespace App\Abstracts;

/**
 * Page
 *
 * This is the base for all admin pages
 * INFORMATION
 *
 * Object Types
 * oTypeController    = 0
 * oTypeCronjob       = 1
 * oTypeModal         = 3
 * oTypeDatatable     = 4
 *
 * @todo Remove framework v1 vars
 */
class Page extends \Framework\Advent\Page
{
  /****************************************************/
  /*                RECEPTIONIST TYPES                */
  /****************************************************/
  /** @const integer oTypeController */
  const oTypeController = 0;
  /** @const integer oTypeCronjob */
  const oTypeCronjob = 1;
  /** @const integer oTypeModal */
  const oTypeModal = 2;
  /** @const integer oTypeDatatable */
  const oTypeDatatable = 3;
  /****************************************************/
  /*                FRAMEWORK V1 VARS                 */
  /****************************************************/
  protected $modal = [];
  /** @var array $template Templates you want the framework to preload */
  protected $template = [];
  /** @var array $controller Controllers you want the framework to preload */
  protected $controller = [];
  /****************************************************/
  /*                  COMMAND OBJECTS                 */
  /****************************************************/
  /** @var \Framework\Commander\Document $_cDocument */
  protected $_cDocument;
  /****************************************************/
  /*       PAGE PERMISSION REQUIREMENT DEFAULTS       */
  /****************************************************/
  /** @var boolean $requiresLogin */
  public static $requiresLogin = true;
  /** @var boolean|string $permissionGroup */
  public static $permissionGroup = false;
  /** @var boolean|string $permission */
  public static $permission = false;
  /****************************************************/
  /*                  PAGE SETTINGS                   */
  /****************************************************/
  /** @var array $scripts Array of script files we need framework to include */
  protected $_pageScripts = [];
  /** @var string $pageSubHeader A small version of the header appears to the right of it */
  protected $pageSubHeader = null;
  /****************************************************/
  /*                  DATA MEMBERS                    */
  /****************************************************/
  /** @var string $_dBodyClass */
  protected $_dBodyClass = "page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white";
  /** @var boolean $buildui Are we building the UI */
  protected $_dBuildUserInterface = true;
  /** @var array $_portletConfiguration Default portlet values */
  protected $_dPortletConfiguration = [
    "type" => 'box',
    "color" => "green",
    "icon" => "fa fa-server",
    "title" => "Title",
    "sub-title" => "",
    "content" => "",
    "tools" => [],
    "actions" => [],
    "collapse" => false
  ];
  /** @var array $_dAdministrationTokens */
  private $_dAdministrationTokens = [
    'company-website'       => 'shophlh.com',
    'company-website-full'  => 'http://www.shophlh.com/',
    'company-name'          => 'HolylandHealth',
    'copyright-year'        => '2015'
  ];
  /** @var array $_dAdministrationStyles */
  private $_dAdministrationStyles = [
    ["//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css",self::absolute],
    ["//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,600,400italic,600italic,700,700italic,800,800italic",self::absolute],
    ["simple-line-icons.css",                     self::application],
    ["jquery.notific8.min.css",                   self::application],
    ["datatables.css",                            self::application],
    ["select2-3.4.4.css",                         self::application],
    ["typeahead.js-bootstrap.css",                self::application],
    ["bootstrap3.css",                            self::application],
    ["bootstrap-datetimepicker.css",              self::application],
    ["bootstrap-editable.css",                    self::application],
    ["bootstrap-select2.css",                     self::application],
    ["bootstrap-switch.css",                      self::application],
    ["bootstrap-modal-bs3patch.css",              self::application],
    ["address.css",                               self::application],
    ["components.css",                            self::application],
    ["plugins.css",                               self::application],
    ["layout.css",                                self::application],
    ["darkblue.css",                              self::application]
  ];
  /** @var array $_dAdministrationScripts */
  private $_dAdministrationScripts = [
    ["//code.jquery.com/jquery-2.2.0.min.js",     self::absolute],
    ["bootstrap3.js",                             self::application],
    ["datatables.js",                             self::application],
    ["datatable.min.js",                          self::application],
    ["select2-3.4.4.js",                          self::application],
    ["jquery.uniform.min.js",                     self::application],
    ["jquery.notific8.js",                        self::application],
    ["bootstrap-datatables.js",                   self::application],
    ["jquery.validate.min.js",                    self::application],
    ["jquery.validate.additional-methods.min.js", self::application],
    ["bootstrap-modalmanager.js",                 self::application],
    ["bootstrap-hover-dropdown.min.js",           self::application],
    ["bootstrap-switch.min.js",                   self::application],
    ["bootstrap-popover.js",                      self::application],
    ["bootstrap-tooltip.js",                      self::application],
    ["bootstrap-confirmation.min.js",             self::application],
    ["bootstrap-datetimepicker.min.js",           self::application],
    ["app.js",                                    self::application],
    ["layout.js",                                 self::application],
    ["DatatableInterface.js",                     self::framework],
    ["Framework.js",                              self::framework],
    ["UserController.js",                         self::application],
    ["SearchController.js",                       self::application],
    ["QuickSidebarController.js",                 self::application]
  ];

  /**
   * @param array $parameters
   */
  public function run(array $parameters = array()){
    /** @var \Framework\Commander\Document $_cDocument */
    $this->_cDocument = $this->receptionist(self::oTypeController,"Document");
    /** Add Page scripts if they exist */
    $this->_addPageScripts();
    /** Register Administration styles */
    $this->addStyles( $this->_dAdministrationStyles );
    /** Register Administration Scripts */
    $this->addScripts( $this->_dAdministrationScripts );
    /** Register Administration Tokens */
    $this->addTokens( $this->_dAdministrationTokens );
    /** Check if we were told to build with user interface */
    if($this->_dBuildUserInterface){
      return $this->_buildWithUserInterface();
    }
    return $this->_buildWithoutUserInterface();
  }

  /****************************************************/
  /*                 BUILD METHODS                    */
  /****************************************************/

  /**
   * Build the page with a user interface
   * @method _buildWithUserInterface
   * @param array $parameters
   */
  private function _buildWithUserInterface(array $parameters = array())
  {
    /****************************************************/
    /*               BUILD LAYOUT OBJECTS               */
    /****************************************************/
    /** @var \App\Objects\Navigation $navigation */
    $navigation = new \App\Objects\Navigation();
    /** @var \App\Objects\Header $header */
    $header = new \App\Objects\Header($navigation, $this->tokens);
    /** @var \App\Objects\QuickSidebar $quickSidebar */
    $quickSidebar = new \App\Objects\QuickSidebar($this->tokens);
    /** @var \App\Objects\ContentArea $contentArea */
    $contentArea = new \App\Objects\ContentArea($this->tokens);
    /** @var \App\Objects\Footer $footer */
    $footer = new \App\Objects\Footer($this->tokens);
    /****************************************************/
    /*               SET PAGE DATA MEMBERS              */
    /****************************************************/
    /** Set the Page Header */
    $this->setPageHeader($this->pageHeader);
    /** Set the page Sub Header */
    $this->setPageSubHeader($this->pageSubHeader);
    /** Set the body class */
    $this->setBodyClass($this->_dBodyClass);
    /****************************************************/
    /*                  BUILD THE PAGE                  */
    /****************************************************/
    /** Write the content to the buffer */
    $this->writeBody("
      {$header->build()}
        <div class='clearfix'></div>
        <div class='page-container'>
          {$navigation->SideNavigation()}
          {$contentArea->build()}
          {$this->Body($parameters)}
          {$quickSidebar->build()}
        </div>
      </div>
      {$footer->build()}
    ");
    /** Render the page */
    $this->render();
  }

  private function _buildWithoutUserInterface(array $parameters = array())
  {
    $this->writeBody($this->body($parameters));
    $this->render();
  }

  /****************************************************/
  /*                  SHORT CUTS                      */
  /****************************************************/

  /**
   * Shortcut for generating portlets
   * @method portlet
   * @return string
   */
  protected function portlet()
  {
    return $this->_cDocument->portlet(
      $this->_dPortletConfiguration['type'],
      $this->_dPortletConfiguration['color'],
      $this->_dPortletConfiguration['icon'],
      $this->_dPortletConfiguration['title'],
      $this->_dPortletConfiguration['sub-title'],
      $this->_dPortletConfiguration['content'],
      $this->_dPortletConfiguration['tools'],
      $this->_dPortletConfiguration['actions'],
      $this->_dPortletConfiguration['collapse']
    );
  }

  /**
   * Shortcut for the receptionist framework getter
   * @method receptionist
   * @param      $objectType
   * @param      $objectName
   * @param bool $isFramework
   * @return bool
   */
  protected function receptionist($objectType, $objectName, $isFramework=true)
  {
    /** Use the object type to determine which of the receptionist methods to use */
    switch($objectType){
      /** Command Object */
      case self::oTypeController: return \Framework\Core\Receptionist::controller($objectName,$isFramework); break;
      /** CronJob Object */
      case self::oTypeCronjob: return \Framework\Core\Receptionist::cronjob($objectName,$isFramework); break;
      /** Modal Object */
      case self::oTypeModal: return \Framework\Core\Receptionist::modal($objectName,$isFramework); break;
      /** Datatable Object */
      case self::oTypeDatatable: return \Framework\Core\Receptionist::datatable($objectName,$isFramework); break;
    }
  }

  /**
   * Add a page script to the script files for this page
   * @method _addPageScripts
   * @return bool
   */
  private function _addPageScripts()
  {
    /** Check if scripts were added */
    if(sizeof($this->_pageScripts)===0){
      /** No scripts to add return to escape the method */
      return true;
    }
    /** @var array $pageScript Iterate over the script files */
    foreach($this->_pageScripts as &$pageScript){
      /** Check if this is a application script */
      if($pageScript[1]===self::application){
        /** Append Page/ To the path */
        $pageScript[0] = "Page/{$pageScript[0]}";
      }
      /** Push this Script reference into the administrationScripts array */
      array_push($this->_dAdministrationScripts, $pageScript);
    }
    /** Return true we are done */
    return true;
  }
}
