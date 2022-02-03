<?php
/**
 * ManagementUsersManage
 *
 * @package App\Event\Request
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * ManagementUsersManage
 *
 * This is the request object for the Management/Users/Manage
 * application
 */
class ManagementUsersManage extends \Framework\Event\Base
{
  /** @var does this request object require you to be logged in */
  public $requiresLogin   = true;

  /** @var the permission this system requires */
  public $permissionGroup = 'user-system';

  /** @var the permission this system requires */
  public $permission      = null;

  /** @var did this event succeed in some way */
  private $result         = false;

  /** @var response data */
  private $data           = 'Insufficient data provided!';

  /** @var this is an array of all possible permissions and permission groups */
  private $permissions = [
    'user-system'   => [
      'simple-name' => 'User System',
      'default'     => false,
      'permissions' => [
        'user-management' => [ 'simple-name' => 'User Management',    'default' => false ],
        'view-log'        => [ 'simple-name' => 'View Log',           'default' => false ],
        'user-create'     => [ 'simple-name' => 'Create User',        'default' => false ],
        'edit-information'=> [ 'simple-name' => 'Edit Information',   'default' => false ],
        'edit-permissions'=> [ 'simple-name' => 'Edit Permissions',   'default' => false ],
        'edit-status'     => [ 'simple-name' => 'Edit Status',        'default' => false ]
      ]
    ],
    'media-buy'     => [
      'simple-name' => 'Media Buy',
      'default'     => false,
      'permissions' => [
        'view-email-links'  => [ 'simple-name' => 'View Email Links', 'default' => false ],
        'add-email-link'    => [ 'simple-name' => 'Add Email Link',   'default' => false ],
        'edit-email-link'   => [ 'simple-name' => 'Edit Email Link',  'default' => false ],
        'add-promoter'      => [ 'simple-name' => 'Add Promoter',     'default' => false ],
        'edit-promoter'     => [ 'simple-name' => 'Edit Promoter',    'default' => false ]
      ]
    ],
    'tools'                 => [
      'simple-name'         => 'Tools',
      'default'             => false,
      'permissions'         => [
        'creative-builder'=> [ 'simple-name' => 'Creative Builder',    'default' => false ]
      ]
    ],
    'sys-admin'             => [
      'simple-name'         => 'Sys Admin',
      'default'             => false,
      'permissions'         => [
        'system-error-log'  => [ 'simple-name' => 'System Error Log',    'default' => false ],
        'funnel-error-log'  => [ 'simple-name' => 'Funnel Error Log',    'default' => false ]
      ]
    ],
    'reports'             => [
      'simple-name'       => 'Reports',
      'default'           => false,
      'permissions'       => [
        'link-breakdown'  => [ 'simple-name' => 'Link Breakdown',        'default' => false ]
      ]
    ],
    'customer-relations'  => [
      'simple-name'       => 'Customer Relations',
      'default'           => false,
      'permissions'       => [
        'view-orders'     => [ 'simple-name' => 'View Orders',           'default' => false ]
      ]
    ],
    'management'          => [
      'simple-name'       => 'Management',
      'default'           => false,
      'permissions' => [
        'management-system' => [ 'simple-name' => 'Management System',   'default' => false ]
      ]
    ]
  ];

  /**
   * __construct the event object
   * @method __construct
   * @return self
   * @access public
   */
  public function __construct()
  {
    /** @var this is the user model so we can query against it */
    $this->model = \Framework\Core\Modulus::get("User", "Holylandhealth", false);
    /** return this for method chaining */
    return $this;
  }

  /**
   * This handles the events and returns the requested data
   * @method run
   * @return string json response to the request
   * @access public
   */
  public function run()
  {
    /** Wrap this in a try catch to capture all the errors and log them */
    try {
      /** if the post data object is empty we are done */
      if(empty($_POST)){            return $this->jsonReturn( false, '$_POST not set!' );             }
      /** if they dident give us an action we are done */
      if(!isset($_POST['action'])){ return $this->jsonReturn( false, 'Done no process requested!' );  }
      /** if we do not have a key then we cant do anything in this request handle */
      if(!isset($_POST['key'])){    return $this->jsonReturn( false, 'Done no process key defined!' );}
      /** We are sure we should be doing what they asked so do it */
      $this->processEvent( $_POST['action'] );
      /** return the result as a json string */
      $this->jsonReturn( $this->result, $this->data );
    /** Catch all framework based exceptions */
    } catch (\Framwork\Core\Exceptional\BaseException $e) {
      /** Something bad happen log it */
      $e->log();
      /** Return an error */
      return $this->jsonReturn( false, "An error occured during your request!");
    }
  }

  /**
   * Process the requested event if possible
   * @method processEvent
   * @param  string       $event the event we were asked to run
   * @return void
   * @access private
   */
  private function processEvent( $event ){
    /** set result to true since we are here */
    $this->result = true;
    /** find the requested event and do it */
    switch( $event ){
      /** get the user log for this user */
      case 'show-recent-log':
        $this->data = $this->model->getRecentUserLog( $_POST['key'] );
      break;
      /** Get the user information from the database for this user */
      case 'get-user-information':
        $this->data = $this->model->getUserInformation( $_POST['key'] );
      break;
      /** get the users permissions */
      case 'get-user-permissions':
        $this->data = unserialize( $this->model->getUserPermissions( $_POST['key'] ) );
      break;
      /** get all possible permissions */
      case 'get-possible-permissions':
        $this->data = $this->permissions;
      break;
      /** update the user information */
      case 'update-user-information':
        $this->data = $this->model->updateUserInformation( $_POST['key'], $_POST['first-name'], $_POST['last-name'], $_POST['phone-number'], $_POST['email-address'] );
      break;
      /** update the user permissions */
      case 'update-user-permissions':
        $this->data = unserialize( $this->model->updateUserPermissions( $_POST['key'], serialize( json_decode( $_POST['permissions'], true ) ) ) );
      break;
      /** change the user status active dsiabled */
      case 'change-user-status':
        $result = $this->model->changeUserStatus( $_POST['key'], ( ( $_POST['status'] == 'Activate' ) ? 1:0 ) );
        $this->result = true;
        $this->data   = 'done';
        if(!$result){
          $this->result = false;
          $this->data   = 'A database error occured while trying to update status!';
        }
      break;
      /** if we hit the default it is because a valid event was not specified */
      default:
        $this->result = false;
        $this->data   = "$event Undefined!";
      break;
    }
  }

  /**
   * Return the result of this request
   * @method jsonReturn
   * @param  boolean     $result  did we suceed in what we wanted to do
   * @param  str         $data    the data or message returned form this request
   * @return void
   */
  private function jsonReturn( $result, $data ){
    echo json_encode( array( 'result' => $result, 'data' => $data ) );
  }
}
