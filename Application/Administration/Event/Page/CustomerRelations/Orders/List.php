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
\Framework\_IncludeCorrect( FRAMEWORK_APPLICATION . "Administration". DSEP . "Objects" . DSEP . "Navigation.php" );
\Framework\_IncludeCorrect( FRAMEWORK_ROOT . "Support" . DSEP . "Object" . DSEP ."Portlet.php" );
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
  public function Body( array $paramaters = array() ){
    /** Set the Document title for this Page Event */
    $this->setDocumentTitle( 'Orders: list' );
    
  }
}
