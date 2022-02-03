<?php
/**
 * FormRequest
 *
 * @package App\Event\Request
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * FormRequest
 *
 * This is how yout get all the static form files. This allows them to be accesed without
 * altering our .htaccess. And it allows us to restrict access to them to limit the amount
 * of information people can glean about our system
 */
class FormRequest extends \Framework\Event\Base
{
  /** @var does this request object require you to be logged in */
  public $requiresLogin = true;

  /** @var the permission this system requires */
  public $permission = null;

  /** @var did this event succeed in some way */
  private $result = false;

  /** @var response data */
  private $data = 'Insufficient data provided!';

  /**
   * __construct the event object
   * @method __construct
   * @return self
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
   */
  public function run()
  {
    /** Wrap this in a try catch to capture all the errors and log them */
    try {
      /** if the post data object is empty we are done */
      if(empty($_GET)){            return $this->jsonReturn( false, '$_GET not set!' );             }
      /** We are sure we should be doing what they asked so do it */
      $this->processEvent( $_GET['action'] );
      /** return the result as a json string */
      $this->htmlReturn( $this->result, $this->data );
    /** Catch all framework based exceptions */
    } catch (\Framwork\Core\Exceptional\BaseException $e) {
      /** Something bad happen log it */
      $e->log();
      /** Return an error */
      return $this->jsonReturn( false, "An error occured during your request!");
    }
  }

  private function processEvent( $event ){
    /** set result to true since we are here */
    $this->result = true;
    /** find the requested event and do it */
    switch( $event ){
      /** get the user log for this user */
      case 'get':
        $this->data = file_get_contents(APP_ROOT.'Forms'.DSEP.$_GET['name'].'.html');
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
  private function htmlReturn( $result, $data ){
    /** if there was an error 404 them  and return false to end the function */
    if(!$result){
      header("HTTP/1.0 404 ".$data);
      return false;
    }
    /** no error output the html */
    echo $data;
  }
}
