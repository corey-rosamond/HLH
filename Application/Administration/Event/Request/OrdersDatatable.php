<?php
/**
 * OrdersDatatable
 *
 * @package App\Event\Main
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * OrdersDatatable
 *
 * This controls the orders Datatable
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT."Advent".DSEP."Request.php" );
class OrdersDatatable  extends \Framework\Advent\Request
{

  /**
   * Does this page require that the user be logged in
   * @var     boolean     $requireLogin
   * @access  protected
   * @static
   */
  public static $requiresLogin = false;

  /**
   * Permission group required to view this page or false if none
   * @var     mixed     $permissionGroup  FALSE if none
   * @access  protected
   * @static
   */
  public static $permissionGroup = false;

  /**
   * Permission required in the group defined above required to view this page
   * @var     mixed     $permission   FALSE if none
   * @access  protected
   * @static
   */
  public static $permission = false;

  /**
   * The events for this Request object
   * @method events
   * @param   string $event the event we were asked to run
   * @return  void
   * @access  protected
   */
  protected function events(){
    $model            = \Framework\Core\Modulus::get( 'Orders', "Holylandhealth" );
    $oID              = (( !isset( $_POST['order_id'] ) || $_POST['order_id'] === '' )? false : $_POST['order_id'] );
    $oDateStart       = (( !isset( $_POST['order_date_from'] ) || $_POST['order_date_from'] === '' )? false : date("Y-m-d",strtotime($_POST['order_date_from'])) );
    $oDateEnd         = (( !isset( $_POST['order_date_to'] ) || $_POST['order_date_to'] === '' )? false : date("Y-m-d",strtotime($_POST['order_date_to'])) );
    $oFirstName       = (( !isset( $_POST['first_name'] ) || $_POST['first_name'] === '' )? false : $_POST['first_name'] );
    $oLastName        = (( !isset( $_POST['last_name'] ) || $_POST['last_name'] === '' )? false : $_POST['last_name'] );
    $oAmount          = (( !isset( $_POST['amount'] ) || $_POST['amount'] === '' )? false : $_POST['amount'] );
    $oStatus          = (( !isset( $_POST['order_status'] ) || $_POST['order_status'] === '' )? false : $_POST['order_status'] );
    $lStart           = $_POST['start'];
    $lEnd             = $_POST['length'];
    $oColumn          = (( !isset( $_POST['order'][0]['column'] ) ) ? 'key' : $_POST['order'][0]['column'] );
    $oDirection       = (( !isset( $_POST['order'][0]['dir'] ) ) ? 'desc' : $_POST['order'][0]['dir'] );
    $data             = $model->getOrdersDatatable( $oID, $oDateStart, $oDateEnd, $oFirstName, $oLastName, $oAmount,$oStatus, $lStart, $lEnd, $oColumn, $oDirection );
    $iTotalRecords    = $model->getOrdersDatatable( $oID, $oDateStart, $oDateEnd, $oFirstName, $oLastName, $oAmount,$oStatus, $lStart, $lEnd, $oColumn, $oDirection, true );
    $iDisplayLength   = intval( $_POST['length'] );
    $iDisplayLength   = (( $iDisplayLength < 0 )? $iTotalRecords : $iDisplayLength );
    $iDisplayStart    = intval( $_POST['start'] );
    $sEcho            = intval( $_POST['draw'] );
    $end              = $iDisplayStart + $iDisplayLength;
    $end              = (( $end > $iTotalRecords )? $iTotalRecords : $end );
    $records          = array();
    $records["data"]  = array();
    $status_list = array(
      "Created" => 'danger',
      "Captured" => "success",
      "Approved" => "warning",
      "Fulfillment" => "info",
      "Complete" => "primary",
      "Auth Capture" => "default",
      "Canceled" => "danger",
      "Authorized" => "primary"
    );
    if($data){
      foreach( $data as $row){
        $records["data"][] = array(
          $row['key'], $row['order-date'], $row['first-name'], $row['last-name'], '$' . $row['total-before'],
          '<span class="label label-sm label-'.($status_list[$row['status']]).'">'.($row['status']).'</span>',
          '<a href="/CustomerRelations/Orders/OrderView?customer='.$row['key'].'" class="btn btn-sm btn-circle btn-default btn-editable">
          <i class="fa fa-search"></i> View</a>'
        );
      }
    }
    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;
    echo json_encode( $records );
    exit();
  }
}
