<?php
/**
 * System
 *
 * @package App\Event\Management\Users
 * @version 1.0.0
 */
namespace App;

/**
 * System
 *
 * System is the main entry point for all apps built on the framework
 */
class System extends \Framework\Support\Abstracts\Singleton
{
  public  $_coreContour;
  public  $_cFunnel;
  public  $_coreEuri;
  public  $_coreAdmit;
  public  $_cTracking;
  public  $_dFunnelHost;
  private $_dRequestedEvent;
  private $_dRequestProtocol;
  private $_dFunnelKey;
  private $_dLoadtime;
  private $_developerLog = [];

  public static function initialize()
  {
    try {
      $instance = self::getInstance();
      /** Core and Command objects */
      $instance->_coreEuri        = \Framework\Core\Euri::getInstance();
      $instance->_coreContour     = \Framework\Core\Contour::getInstance();
      $instance->_coreAdmit       = \Framework\Core\Admit::getInstance();
      $instance->_cFunnel         = \Framework\Core\Receptionist::controller("Funnel", true);
      $instance->_cTracking       = \Framework\Core\Receptionist::controller("Tracking", true);
      /** Load our data */
      $instance->_dFunnelHost     = $instance->_coreEuri->server('host');
      $instance->_dRequestedEvent = $instance->_coreEuri->param('event');
      $instance->_dRequestProtocol= $instance->_coreEuri->server('method');
      $instance->_dFunnelKey      = $instance->_cFunnel->findFunnelKey( $instance->_dFunnelHost );
      if(!$instance->_dFunnelKey){ return $instance->_unknownFunnel(); }
      $instance->_cFunnel->load($instance->_dFunnelKey);
      $instance->_cTracking->initSession($instance->_dFunnelHost, $instance->_dFunnelKey);
      if(isset($instance->_dRequestedEvent['event']) &&$instance->_dRequestedEvent['event'] == 'Event/ThankYou'){
        return $instance->_chooseEvent();
        exit();
      }
      if(strtoupper($instance->_dRequestProtocol)=='GET'){
        return $instance->_choosePage();
        exit();
      }
      if(strtoupper($instance->_dRequestProtocol)=='POST'){
        return $instance->_chooseEvent();
        exit();
      }
    } catch(\Framework\Exceptional\BaseException $exception){
      $instance->_errorPage($exception);
    }
  }

  private function _choosePage()
  {
    if($this->_dRequestedEvent['event']==""){
      if(!$this->_cFunnel->getEntryPage()){
        return $this->_entryPageMissing();
      }
      return $this->_renderPage();
    }
    if(!$this->_cFunnel->getRequestedPage($this->_dRequestedEvent)){
      return $this->_notFoundPage();
    }
    return $this->_renderPage();
  }


  private function _chooseEvent()
  {
    try{
      if(!isset($this->_dRequestedEvent[0])||$this->_dRequestedEvent[0]!='Event'){
        echo json_encode(["result"=>false,"message"=>"incorrect location!"]);
      }
      switch($this->_dRequestedEvent['event']){
        /****************************************************/
        /*                      CHECKOUT                    */
        /****************************************************/
        case "Event/Checkout":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Checkout.php");
          $event = new \App\Event\Checkout($this);
        break;
        /****************************************************/
        /*               UP AND DOWN SELL ONE               */
        /****************************************************/
        case "Event/Upsell1":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Upsell1.php");
          $event = new \App\Event\Upsell1($this);
        break;
        case "Event/Downsell1":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Downsell1.php");
          $event = new \App\Event\Downsell1($this);
        break;
        /****************************************************/
        /*               UP AND DOWN SELL TWO               */
        /****************************************************/
        case "Event/Upsell2":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Upsell2.php");
          $event = new \App\Event\Upsell2($this);
          break;
        case "Event/Downsell2":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Downsell2.php");
          $event = new \App\Event\Downsell2($this);
          break;
        /****************************************************/
        /*               UP AND DOWN SELL THREE             */
        /****************************************************/
        case "Event/Upsell3":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Upsell3.php");
          $event = new \App\Event\Upsell3($this);
          break;
        case "Event/Downsell3":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Downsell3.php");
          $event = new \App\Event\Downsell3($this);
          break;
        /****************************************************/
        /*               UP AND DOWN SELL FOUR              */
        /****************************************************/
        case "Event/Upsell4":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Upsell4.php");
          $event = new \App\Event\Upsell4($this);
          break;
        case "Event/Downsell4":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Downsell4.php");
          $event = new \App\Event\Downsell4($this);
          break;
        /****************************************************/
        /*               UP AND DOWN SELL FIVE              */
        /****************************************************/
        case "Event/Upsell5":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Upsell5.php");
          $event = new \App\Event\Upsell5($this);
          break;
        case "Event/Downsell5":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."Downsell5.php");
          $event = new \App\Event\Downsell5($this);
          break;
        /****************************************************/
        /*                  THANK YOU EVENT                 */
        /****************************************************/
        case "Event/ThankYou":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."ThankYou.php");
          $event = new \App\Event\ThankYou($this);
        break;
        case "Event/AjaxPost":
          \Framework\_IncludeCorrect(APP_ROOT."Event".DSEP."AjaxPost.php");
          $event = new \App\Event\AjaxPost($this);
          break;
        default:
          echo json_encode(["result"=>false,"message"=>"incorrect location!"]);
          exit();
        break;
      }
      if(!isset($event)){
        echo json_encode(["result"=>false,"message"=>"incorrect location!"]);
        exit();
      }
      $event->run();
    } catch( \Framework\Exceptional\BaseException $exception ){
      return $this->_errorEvent( $exception );
      exit();
    }
  }


  private function _renderPage()
  {
    switch($this->_cFunnel->getPageType()){
      /****************************************************/
      /*                    ENTRY PAGE                    */
      /****************************************************/
      case \Framework\Commander\Funnel::vsl:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."VslPage.php");
        $page = new \App\Page\VslPage( $this );
        break;
      /****************************************************/
      /*                  CHECKOUT PAGE                   */
      /****************************************************/
      case \Framework\Commander\Funnel::checkout:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."CheckoutPage.php");
        $page = new \App\Page\CheckoutPage( $this );
        break;
      /****************************************************/
      /*               UP AND DOWN SELL ONE               */
      /****************************************************/
      case \Framework\Commander\Funnel::upsell1:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Upsell1.php");
        $page = new \App\Page\Upsell1( $this );
        break;
      case \Framework\Commander\Funnel::downsell1:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Downsell1.php");
        $page = new \App\Page\Downsell1( $this );
        break;
      /****************************************************/
      /*               UP AND DOWN SELL TWO               */
      /****************************************************/
      case \Framework\Commander\Funnel::upsell2:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Upsell2.php");
        $page = new \App\Page\Upsell2( $this );
        break;
      case \Framework\Commander\Funnel::downsell2:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Downsell12.php");
        $page = new \App\Page\Downsell2( $this );
        break;
      /****************************************************/
      /*               UP AND DOWN SELL THREE             */
      /****************************************************/
      case \Framework\Commander\Funnel::upsell3:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Upsell3.php");
        $page = new \App\Page\Upsell3( $this );
        break;
      case \Framework\Commander\Funnel::downsell3:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Downsell3.php");
        $page = new \App\Page\Downsell3( $this );
        break;
      /****************************************************/
      /*               UP AND DOWN SELL FOUR              */
      /****************************************************/
      case \Framework\Commander\Funnel::upsell4:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Upsell4.php");
        $page = new \App\Page\Upsell4( $this );
        break;
      case \Framework\Commander\Funnel::downsell4:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Downsell4.php");
        $page = new \App\Page\Downsell4( $this );
        break;
      /****************************************************/
      /*               UP AND DOWN SELL FIVE              */
      /****************************************************/
      case \Framework\Commander\Funnel::upsell5:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Upsell5.php");
        $page = new \App\Page\Upsell5( $this );
        break;
      case \Framework\Commander\Funnel::downsell5:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."Downsell5.php");
        $page = new \App\Page\Downsell5( $this );
        break;
      /****************************************************/
      /*                    THANK YOU                     */
      /****************************************************/
      case \Framework\Commander\Funnel::thankYou:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."ThankYou.php");
        $page = new \App\Page\ThankYou( $this );
        break;
      /****************************************************/
      /*                 END FUNNEL PAGES                 */
      /****************************************************/
      case \Framework\Commander\Funnel::contactUs:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."ContactUsPage.php");
        $page = new \App\Page\ContactUsPage( $this );
        break;
      case \Framework\Commander\Funnel::download:


        break;
      case \Framework\Commander\Funnel::antiSpamPolicy:
      case \Framework\Commander\Funnel::disclaimer:
      case \Framework\Commander\Funnel::privacyPolicy:
      case \Framework\Commander\Funnel::refunds:
      case \Framework\Commander\Funnel::termsAndConditions:
        \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."ContentPage.php");
        $page = new \App\Page\ContentPage( $this );
        break;
    }
    $page->renderPage();
    $this->_dLoadtime = microtime(true) - $this->_coreEuri->server('time_float');
    $this->_cTracking->addView($page->pageType(),$this->_dLoadtime);
    exit();
  }

  private function _notFoundPage()
  {
    if(!$this->_cFunnel->getNotFoundPage()){
      $this->_cFunnel->defaultNotFoundPage();
    }
    \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."NotFoundPage.php");
    $page = new \App\Page\NotFoundPage($this);
    $page->renderPage();
    exit();
  }

  /**
   * Error event similar to the error page handles errors for none page events
   * @method _errorEvent
   * @param $exception
   */
  private function _errorEvent($exception)
  {
    /** Log the exception message */
    $this->_coreAdmit->log($exception,$this->_dFunnelHost);
    /** Return the error json */
    echo json_encode(["result"=>false,"message"=>"An error occured while trying to process this event!"]);
    /** Exit to stop further execution */
    exit();
  }

  /**
   * Render the errorPage
   * @method _errorPage
   * @param $exception
   */
  private function _errorPage($exception)
  {
    /** Log the exception message */
    $this->_coreAdmit->log($exception,$this->_dFunnelHost);
    /** Make sure the have an error page for this funnel */
    if(!$this->_cFunnel->getErrorPage()){
      /** No error page for this funnel give them the default */
      $this->_cFunnel->defaultErrorPage();
    }
    /** Include the error page object */
    \Framework\_IncludeCorrect(APP_ROOT."Page".DSEP."ErrorPage.php");
    /** @var \App\Page\ErrorPage $page */
    $page = new \App\Page\ErrorPage($this, $exception);
    /** Render the error page */
    $page->renderPage();
    /** Exxit just to make sure nothing funny happens */
    exit();
  }

  private function _entryPageMissing()
  { echo "[{$this->_coreEuri->server('time')}][{$this->_dFunnelHost}]: Funnel missing entry page!"; }

  private function _unknownFunnel()
  { echo "[{$this->_coreEuri->server('time')}][{$this->_dFunnelHost}]: Undefined Funnel!"; }

  private function _addDeveloerEntry( $message )
  { array_push( $this->_developerLog, $message ); }

  /**
   * Render the developer log
   * @method _renderDeveloperLog
   * @return string
   */
  public function _renderDeveloperLog()
  { return ""; }
}
