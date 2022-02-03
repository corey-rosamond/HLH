<?php
/**
 * Dashboard
 *
 * @package App\Event\Page\Main
 * @version 1.0.0
 */
namespace App\Event\Page\CustomerRelations\Orders;

/**
 * Main Dashboard
 *
 * The main dashboard is the v1 dashboard that everyone will
 * get to see more dashboards will come after that
 */
\Framework\_IncludeCorrect( APP_ROOT . "Abstracts" . DSEP . "FunnelPage.php" );
class OrderList extends \App\Abstracts\Page
{
  /**
   * Does this page require that the user be logged in
   * @var     boolean     $requireLogin
   * @access  protected
   */
  public static $requiresLogin = true;

  /**
   * Permission group required to view this page or false if none
   * @var     mixed     $permissionGroup  FALSE if none
   * @access  protected
   */
  public static $permissionGroup = false;

  /**
   * Permission required in the group defined above required to view this page
   * @var     mixed     $permission   FALSE if none
   * @access  protected
   */
  public static $permission = false;

  /**
   * These are the modals this page will use. Nothing needs to be done
   * Beyond this to get them the framework will just do it for you
   * @var     array       $modal
   * @access  protected
   */
  protected $modal = [ 'Orders.Holylandhealth' => null ];

  /**f
   * Templates are a snipet oh html that we break into parts then
   * we transpose the data on top of the html and give it back
   * @var     array       $modal
   * @access  protected
   */
  protected $template = [];

  /**
   * Controllers are more function intensive they are in charge of controlling
   * groups of methods that logicly belong together
   * @var     array      $controller
   * @access  protected
   */
  protected $controller = [];

  /**
   * Page Header
   * @var     string      $pageHeader
   * @access  protected
   */
  protected $pageHeader = "Orders: ";

  /**
   * Page Sub Header
   * @var     string      $pageSubHeader
   * @access  protected
   */
  protected $pageSubHeader = "list";

  /**
   * This will build the body of the docment out the rest of the sections of the
   * page will be handled by the Application\Page abstract or by the
   * Advent\Page Abstract
   * @method  body
   * @param   array   $paramaters   This is an optional array of paramtars that can be passed
   * @return  string                The html needed to render the body for this page
   * @access  public
   */
  public function Body( array $paramaters = array() )
  {
    /** Set the Document title for this Page Event */
    $this->setDocumentTitle( 'Orders: list' );
    $this->writeBody( $this->makePortlet() );
    $this->addScript( 'CustomerRelations.Orders.js', self::application );
  }

  /**
   * Make a select box for orders
   * @method  orderStatusSelect
   * @return  string  HTML to make the select box
   * @access  private
   */
  private function orderStatusSelect()
  {
    return "
    <select name=\"order_status\" class=\"form-control form-filter input-sm\">
      <option value=\"\">Select...</option>
      <option value=\"Created\">Created</option>
      <option value=\"Captured\">Captured</option>
      <option value=\"Canceled\">Canceled</option>
      <option value=\"Authorized\">Authorized</option>
      <option value=\"Auth Capture\">Auth Capture</option>
      <option value=\"Approved\">Approved</option>
      <option value=\"Fulfillment\">Fulfillment</option>
      <option value=\"Complete\">Complete</option>
    </select>";
  }

  /**
   * Make a butt with drop down to list all of the export types
   * @method  exportFormats
   * @return  string html string to make the button and drop down
   * @access  private
   */
  private function exportFormats()
  {
    return "
    <div class=\"btn-group\">
      <a class=\"btn red btn-outline btn-circle\" href=\"javascript:;\" data-toggle=\"dropdown\">
        <i class=\"fa fa-share\"></i>
        <span class=\"hidden-xs\"> Tools </span>
        <i class=\"fa fa-angle-down\"></i>
      </a>
      <ul class=\"dropdown-menu pull-right\">
        <li><a href=\"javascript:;\"> Export to Excel </a></li>
        <li><a href=\"javascript:;\"> Export to CSV </a></li>
        <li><a href=\"javascript:;\"> Export to XML </a></li>
        <li class=\"divider\"> </li>
        <li><a href=\"javascript:;\"> Print Invoices </a></li>
      </ul>
    </div>";
  }

  /**
   * Make an actions button
   * @method  actions
   * @return  string  html content needed to make the button
   * @access  private
   */
  private function actions()
  {
    return "
    <div class=\"actions\">
      <div class=\"btn-group btn-group-devided\" data-toggle=\"buttons\">
        <label class=\"btn btn-transparent green btn-outline btn-outline btn-circle btn-sm active\">
        <input type=\"radio\" name=\"options\" class=\"toggle\" id=\"option1\">Actions</label>
        <label class=\"btn btn-transparent blue btn-outline btn-circle btn-sm\">
        <input type=\"radio\" name=\"options\" class=\"toggle\" id=\"option2\">Settings</label>
      </div>
      {$this->exportFormats()}
    </div>";
  }

  /**
   * Column Ordering section
   * @method  columnOrdering
   * @return  string of html to create this section
   * @access  private
   */
  private function columnOrdering()
  {
    return "
    <tr role=\"row\" class=\"heading\">
      <th width=\"5%\"> Order&nbsp </th>
      <th width=\"15%\"> Purchased&nbsp;On </th>
      <th width=\"15%\"> First Name </th>
      <th width=\"15%\"> Last Name </th>
      <th width=\"10%\"> Amount </th>
      <th width=\"10%\"> Status </th>
      <th width=\"5%\"> Actions </th>
    </tr>";
  }

  /**
   * Search
   * @method  Search
   * @return  string of html to create this section
   * @access  private
   */
  private function search()
  {
    return "
    <div class=\"margin-bottom-5\">
      <button class=\"btn btn-sm btn-success filter-submit margin-bottom\">
      <i class=\"fa fa-search\"></i> Search</button>
    </div>
    <button class=\"btn btn-sm btn-default filter-cancel\">
    <i class=\"fa fa-times\"></i> Reset</button>";
  }

  /**
   * Filter the results out by date
   * @method  dateFilter
   * @return  string of html to create this section
   * @access  private
   */
  private function dateFilter()
  {
    return "
    <div class=\"input-group date date-picker margin-bottom-5\" data-date-format=\"dd-mm-yyyy\">
      <input type=\"text\" class=\"form-control form-filter input-sm\" readonly name=\"order_date_from\" placeholder=\"From\">
      <span class=\"input-group-btn\"><button class=\"btn btn-sm default\" type=\"button\"><i class=\"fa fa-calendar\"></i></button></span>
    </div>
    <div class=\"input-group date date-picker\" data-date-format=\"dd-mm-yyyy\">
      <input type=\"text\" class=\"form-control form-filter input-sm\" readonly name=\"order_date_to\" placeholder=\"To\">
      <span class=\"input-group-btn\"><button class=\"btn btn-sm default\" type=\"button\"><i class=\"fa fa-calendar\"></i></button></span>
    </div>";
  }

  /**
   * Make the shell for the datatable
   * @method  makeDatatable
   * @return  string of html to create this section
   * @access  private
   */
  private function makeDatatable()
  {
    return "
    <table class=\"table table-striped table-bordered table-hover\" id=\"datatable_orders\">
      <thead>
        {$this->columnOrdering()}
        <tr role=\"row\" class=\"filter\">
          <td><input type=\"text\" class=\"form-control form-filter input-sm\" name=\"order_id\"> </td>
          <td>{$this->dateFilter()}</td>
          <td><input type=\"text\" class=\"form-control form-filter input-sm\" name=\"first_name\"> </td>
          <td><input type=\"text\" class=\"form-control form-filter input-sm\" name=\"last_name\"> </td>
          <td><input type=\"text\" class=\"form-control form-filter input-sm\" name=\"amount\"> </td>
          <td>{$this->orderStatusSelect()}</td>
          <td>{$this->search()}</td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>";
  }

  /**
   * Make the title section of the datatable portlet
   * @method  portletTitle
   * @return  string of html to create this section
   * @access  private
   */
  private function portletTitle()
  {
    return "
    <div class=\"portlet-title\">
      <div class=\"caption\">
        <i class=\"icon-settings font-green\"></i>
        <span class=\"caption-subject font-green sbold uppercase\"> Order Listing </span>
      </div>
    </div>";
  }

  /**
   * Make the outer shell for the datatable portlet
   * @method  portlet
   * @return  string of html to create this section
   * @access  private
   */
  private function makePortlet()
  {
    return "
    <div class=\"portlet light portlet-fit portlet-datatable bordered\">
    {$this->portletTitle()}
    <div class=\"portlet-body\">
      <div class=\"table-container\">
        <div class=\"table-actions-wrapper\"><span></span>
        </div>{$this->makeDatatable()}
      </div>
    </div>";
  }
}
