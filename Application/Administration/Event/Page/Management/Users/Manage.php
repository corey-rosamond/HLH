<?php
/**
 * Manage
 *
 * @package App\Event\Management\Users
 * @version 1.0.0
 */
namespace App\Event\Page\Management\Users;

/**
 * Manage
 *
 * This is the main interface for manageing users and their permissions
 */
class Manage extends \App\Event\Page{

  /** @var string Title */
  public $title           = "Manage Users";

  /** @var string Header */
  public $header          = "Manage";

  /** @var string sub header */
  public $subheader       = "Users";

  /** @var the permission this system requires */
  public $permissionGroup = 'user-system';

  /** @var the permission this system requires */
  public $permission      = null;

  /** @var script file for this page */
  public $script          = "Live/Page-Scripts/Management.Users.Manage.js";

  /**
   * Construct the event object
   * @method __construct
   * @return self
   * @access public
   */
  public function __construct()
  {
    $this->userModel = \Framework\Core\Modulus::get("User", "Holylandhealth", false);
    return $this;
  }

  /**
   * build the User Manage page
   * @method build
   * @return string html content for the view
   * @access public
   */
  public function build()
  {
    $html = '';
    /** Get a list of the current users */
    $userList = $this->userModel->getManageUserDatatable();
    /** Make our datatable columns */
    $columns = [
      ['label'=>'Key',          'order'=>true, 'style'=>'text-align:center;width:50px;'],
      ['label'=>'Username',     'order'=>true, 'style'=>''],
      ['label'=>'Name',         'order'=>true, 'style'=>''],
      ['label'=>'Email Address','order'=>true, 'style'=>''],
      ['label'=>'Phone Number', 'order'=>true, 'style'=>''],
      ['label'=>'Last Login',   'order'=>true, 'style'=>''],
      ['label'=>'Actions',      'order'=>false,'style'=>'width:450px;text-align:left;']
    ];
    /** Make the data table */
    $datatable = new \Framework\Support\Object\Datatable( 'user-datatable', $columns );
    /** loop through the users adding them to the datatable */
    foreach($userList as $user){
      /** set the actions and the button styles */
      $action = 'Disable';
      $icon   = 'fa fa-thumbs-o-down';
      $button = 'btn green-sharp border-white';
      /** If the status is false then update the button to the none default */
      if(!$user['status']){
        $icon   = 'fa fa-thumbs-o-up';
        $action = 'Activate';
        $button = 'btn red-thunderbird border-white';
      }
      /** create the actions section */
      $user['actions'] = '<div class="btn-group btn-group-solid" data-user-key="'.$user['key'].'">
            <button class="'.$button.' user-information"  data-url="/Request/FormRequest?action=get&name=Management.Users.Manage.Information" type="button">
              <i class="fa fa-user" style="margin-right:2px;"></i> Information
            </button>
            <button class="'.$button.' user-permissions"  data-url="/Request/FormRequest?action=get&name=Management.Users.Manage.Permissions" type="button">
              <i class="fa fa fa-key" style="margin-right:2px;"></i> Permissions
            </button>
            <button class="'.$button.' user-log"          data-url="/Request/FormRequest?action=get&name=Management.Users.Manage.Log" type="button">
              <i class="fa fa-list" style="margin-right:2px;"></i> Log
            </button>
            <button
              class="'.$button.' disable-user"
              data-action="'.$action.'"
              data-placement="left"
              data-animation="true"
              data-original-title="'.$action.' User!"
              data-popout="true"
              data-singleton="true"
              data-btn-ok-class="btn green-sharp white-stripe"
              data-btn-ok-label="Yes!"
              data-btn-ok-icon="fa fa-thumbs-o-up"
              data-btn-cancel-class="btn red-thunderbird grey-salt-stripe"
              data-btn-cancel-label="No!"
              data-btn-cancel-icon="fa fa-thumbs-o-down">
              <i class="'.$icon.'" style="margin-right:3px;"></i> '.$action.'
            </button>
      </div>';
      /** Set the content for the users phone number */
      $user['phone-number']   = $this->formatPhoneNumber( $user['phone-number'] );
      /** set the content for the users email address */
      $user['email-address']  = $this->formatEmailAddress( $user['email-address'] );
      /** set the content for the last login date */
      $user['last-login']     = $this->formatLastActive( $user['last-login'] );
      /** Unset the status section we only needed that for the actions menu */
      unset($user['status']);
      /** add the row to the data table */
      $datatable->addRow($user);
    }
    /** finalize the data table */
    $html = $datatable->finalize();

    /** include our form models */
    $html .= $this->includeForms();

    /** return the page content so we can render the page */
    return ( new \Framework\Support\Object\Portlet('User List', $html ) )
      ->setType('box')
      ->setIcon('fa-1x fa fa-shield')
      ->setFontColor('font-blue-steel')
      ->setColor('blue-chambray')
      ->finalize();
  }

  /**
   * Include the forms needed for this program
   * @method includeForms
   * @return string html form content
   * @access private
   */
  private function includeForms(){
    return '
      <div id="user-log" class="modal fade" tabindex="-1"></div>
      <div id="user-information" class="modal fade" tabindex="-1"></div>
      <div id="user-permissions" class="modal fade" tabindex="-1"></div>
    ';
  }

  /**
   * Format the phone number for readability
   * @method formatPhoneNumber
   * @param  int            $number the phone number
   * @return string                 the formated number
   * @access private
   */
  private function formatPhoneNumber( $number ){
    if($number==''){ return 'Phone Number Not Set'; }
    return "(".substr($number,0,3).") ".substr($number,3,3)."-".substr($number,6);
  }

  /**
   * Return the phone number or string saying its not set
   * @method formatEmailAddress
   * @param  string             $email string containing email address
   * @return string                    string containing email address
   * @access private
   */
  private function formatEmailAddress( $email ){
    if($email==''){ return 'Email Address Not Set'; }
    return $email;
  }

  /**
   * Return the last active date or last logged in
   * @method formatLastActive
   * @param  string           $date the last login date
   * @return string                 the last login date
   * @access private
   */
  private function formatLastActive( $date ){
    if($date==''){ return 'Never Logged In'; }
    return $date;
  }
}
