<?php
/**
 * ManagementMediaBuyEmailLinks
 *
 * @package App\Event\Request
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * ManagementMediaBuyEmailLinks
 *
 * All request functions for the MediaBuy Email Links
 */
class ManagementMediaBuyEmailLinks  extends \Framework\Event\Base
{
  /** @var does this request object require you to be logged in */
  public $requiresLogin   = true;

  /** @var the permission this system requires */
  public $permissionGroup = 'media-buy';

  /** @var the permission this system requires */
  public $permission      = null;

  /** @var did this event succeed in some way */
  private $result         = false;

  /** @var response data */
  private $data           = 'Insufficient data provided!';

  /**
   * __construct the event object
   * @method __construct
   * @return self
   * @access public
   */
  public function __construct()
  {
    /** Get the users model so we can set owning user for promoters */
    $this->userModel        = \Framework\Core\Modulus::get("User",                "Holylandhealth", false);
    /** Get the promoters model so we can manipulate promoters */
    $this->promotersModel   = \Framework\Core\Modulus::get("MediaBuyPromoters",   "Holylandhealth" );
    /** Get the email link model so we can manipulate the links */
    $this->emailLinksModel  = \Framework\Core\Modulus::get("MediaBuyEmailLinks",  "Holylandhealth" );
    /** Get the funnels model so we can select funnels for the links */
    $this->funnelsModel     = \Framework\Core\Modulus::get("Funnels",             "Holylandhealth" );
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

      /** Search */
      case 'search-users':        /** USER */
        $this->data = $this->userModel->userSearch( $_POST['query'] );
      break;
      case 'search-promoters':    /** PROMOTER */
        $this->data = $this->promotersModel->promoterSearch( $_POST['query'] );
      break;
      case 'search-funnels':      /** FUNNEL */
        $this->data = $this->funnelsModel->funnelSearch( $_POST['query'] );
      break;

      /** GET DATA */
      case 'get-link-data':       /** LINK */
        $this->data = $this->emailLinksModel->getLink( $_POST['key'] );
      break;
      case 'get-promoter-data':   /** PROMOTER */
        $this->data = $this->promotersModel->getPromoter( $_POST['key'] );
      break;


      /** GET NAME */
      case 'get-link-name':       /** LINK */
        $this->data = $this->emailLinksModel->getLinkName( $_POST['key'] );
      break;
      case 'get-promoter-name':   /** PROMOTER */
        $this->data = $this->promotersModel->getPromoterName( $_POST['key'] );
      break;
      case 'get-funnel-name':     /** FUNNEL */
        $this->data = $this->funnelsModel->getFunnelName( $_POST['key'] );
      break;


      /** ADD */
      case 'add-promoter':        /** PROMOTER */
        $this->data = $this->promotersModel->makePromoter( $_POST['owner-key'], $_POST['name'] );
      break;
      case 'add-link':            /** LINK */
        $this->data = $this->emailLinksModel->makeEmailLink(
          $_POST['name'], $_POST['promoter'], $_POST['funnel'], $_POST['cost'], $_POST['return'],
          $_POST['start'], $_POST['end']
        );
      break;

      /** UPDATE */
      case 'update-link':         /** LINK */
        $this->data = $this->emailLinksModel->updateEmailLink(
          $_POST['name'], $_POST['promoter'], $_POST['funnel'], $_POST['cost'], $_POST['return'],
          $_POST['start'], $_POST['end'], $_POST['key']
        );
      break;
      case 'update-promoter':     /** PROMOTER */
        $this->data = $this->promotersModel->updatePromoter( );
      break;

      /** FAIL */
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
