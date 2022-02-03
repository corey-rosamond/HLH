<?php
/**
 * Order View
 *
 * @package App\Event\Page\Main
 * @version 1.0.0
 */
namespace App\Event\Page\CustomerRelations\Orders;

/**
 * Order View
 *
 * The main dashboard is the v1 dashboard that everyone will
 * get to see more dashboards will come after that
 */
\Framework\_IncludeCorrect( APP_ROOT."Abstracts".DSEP."FunnelPage.php" );
class OrderView extends \App\Abstracts\Page
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
  protected $pageSubHeader = "View";

  protected $_pPortletConfig = [
    'title'           => 'Order: ',
    'icon'            => 'icon-settings font-dark',
    'fontColor'       => 'font-dark',
    'backgroundColor' => "white",
    'type'            => 'portlet light portlet-fit portlet-datatable bordered',
    'portletStyles'   => "",
    'bodyStyles'      => ""
  ];

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
    $document     = \Framework\Core\Receptionist::controller( 'Document', true );
    $oController  = \Framework\Core\Receptionist::controller( "Order", false );
    $oController->load( $_GET['customer'] );
    $this->setDocumentTitle( "Order View: {$oController->orderNumber()}" );
    $pPortlet = $this->portlet( $this->_pPortletConfig );
    $pPortlet->title( 'Order: '. $oController->orderNumber(). " | ". $oController->orderDate() );
    $document->addTab( 'Details',   $this->Details( $oController, $document ), false, false, true );
    $document->addTab( 'Messages',  $this->Messages( $oController ),  'primary',  $oController->messageCount(), false );
    $document->addTab( 'Emails',            'tab content',                                'primary',  $oController->count('email'),       false );
    $document->addTab( 'Order Items',       'tab content',                                'primary',  $oController->count('items'),       false );
    $document->addTab( 'Payment Processor', $this->PaymentProcessor( $oController ),      'primary',  $oController->processorCount(),   false );
    $document->addTab( 'Fulfillment',       'tab content',                                'primary',  $oController->count('fulfillment'), false );
    $document->addTab( 'Site Visit',        $this->SiteVisit( $oController, $document ),  'primary',  $oController->count('tracking'),    false );
    $pPortlet->buffer( $document->renderTabSystem() );
    $this->writeBody( $pPortlet->render() );
    //print_r($oController);
    return true;
  }



  private function Details( $oController, $document )
  {
    return $document->rowDouble(
      $document->portlet("box", "yellow-crusta", 'icon-info', 'Order Details', '',
        $document->datatable( 'order-details', array(), array(
          ["Order #",$oController->orderNumber()],['Order Date & Time',$oController->orderDate()],
          ['Order Status', $oController->orderStatus()],['Grand Total', ''],
          ['Payment Information','Network Merchants (NMI)']
        )),array(), array(), true
      ),
      $document->portlet("box", "blue-hoki", 'icon-user', 'Customer Information', '',
        $document->datatable( 'customer-information', array(), array())
      )
    ).$document->rowDouble(
      $oController->billingPortlet(), $oController->shippingPortlet()
    );
  }


  private function OrderItems( $oController )
  {

  }

  private function Messages( $oController )
  { return $oController->messageLog(); }

  private function PaymentProcessor( $oController )
  { return $oController->processorLog(); }

  private function Fulfillment( $oController )
  {

  }

  /** envelope */
  private function EmailsSent( $oController )
  {

  }

  /** Site Visit Information Datatable Site visit: page views */
  private function SiteVisit( $oController, $document )
  {
    return $document->rowDouble(
      $document->portlet("box", "green-meadow", 'icon-info', 'Visit Information', '',
        $document->datatable( 'visit-information', array(), $oController->siteVisit()),
        array(), array(), true
      ),
      $document->portlet("box", "blue-hoki", 'icon-user', 'Page Views', '',
        $document->datatable( 'page-views',
          [
            ['label'=>'Date &amp; Time of Visit', 'style'=>'text-align:left;'],
            ['label'=>'Page', 'style'=>'text-align:center;'],
            ['label'=>'Duration', 'style'=>'text-align:center;']
          ],
          $oController->pageViews()
        ), array(), array(), true
      )
    );
  }
}
