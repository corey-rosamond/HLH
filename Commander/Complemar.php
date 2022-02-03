<?php
/**
 * Complemar
 *
 * @package Framework\Commander
 * @version 1.0.0
 */
namespace Framework\Commander;

/**
 * 	Complemar
 *
 *	The Complemar object is in charge of handling all communications
 *	Between the framework admin the funnel system and complemar. To that effect
 *	The only methods you will find here are aimed at that goal
 * @todo rewrite this to include logging and use the service system to store its information
 */
\Framework\_IncludeCorrect(FRAMEWORK_ROOT.'vendor'.DSEP.'nusoap'.DSEP.'nusoap.php');
\Framework\_IncludeCorrect(CONFIGURATION.'Services'.DSEP.'Complemar.config.php');
class Complemar extends \Framework\Commander\Commander
{
  /** @var string $username */
  private $username = COMPLEMAR_USERNAME;
  /** @var string $password */
  private $password = COMPLEMAR_PASSWORD;
  /** @var string $wsdl Location of wsdl */
  private $wsdl = COMPLEMAR_WSDL;
  /** @var int $jobNumber Provided by nmi */
  private $jobNumber = COMPLEMAR_JOB_NUMBER;

  /**
   * Setup the Complemar object
   * @method __construct
   * @access public
   */
   public static function construct()
   {
     $instance = self::getInstance();
     $instance->SOAPInit();
     return $instance;
   }

  /**
   * Get the satus of an order we already submitted
   * @method getOrderStatus
   * @return array         The XML response
   * @access public
   */
  public function getOrderStatus( $orderNumber, $sequenceNumber )
  {
    $GetOrderStatus = $this->SOAPMakeEnvelope( "GetOrderStatus" );
      $GetOrderStatus->addChild( 'orderNumber', $orderNumber );
      $GetOrderStatus->addChild( 'sequenceNumber', "0" );
      $GetOrderStatus->addChild( 'userName', $this->username );
			$GetOrderStatus->addChild( 'password', $this->password );
      $Envelope = $this->SOAPFinalizeEnvelope( $GetOrderStatus );
      $result = $this->SOAPRequest( 'GetOrderStatus', $Envelope );
      if( $result ){
        print_r( $result );
      }
  }

  /**
   * Take one of our orders and submit it to complemar for shipping
   * @method  createOrderInComplemar
   * @param   array                 $order    The order record from the database
   * @param   array                 $items    The order item records from the database
   * @param   array                 $billing  The billing address
   * @param   array                 $shipping The shipping address
   * @return  boolean
   * @access  public
   */
  public function createOrderInComplemar( $orderData ){
    $order    = $orderData['Order'];
    $shipping = $orderData['ShippingAddress'];
    $billing  = $orderData['BillingAddress'];
    $items    = $orderData['OrderItems'];
    /** Do it ,,,,,,, */
    $CreateOrder = $this->SOAPMakeEnvelope( 'CreateOrder' );
      $request = $CreateOrder->addChild( 'request' );
        $Items = $request->addChild( 'Items' );
          foreach( $items as $item ){
            if($item['is-digital']==0) {
              $OrderRequestItem = $Items->AddChild ('OrderRequestItem');
              $OrderRequestItem->addChild ('Quantity', 1);
              $OrderRequestItem->addChild ('StockNumber', htmlspecialchars ($item['sku']));
              $OrderRequestItem->addChild ('Description1', htmlspecialchars ($item['name']));
            }
          }
        $request->addChild('JobNumber', $this->jobNumber );
        $OrderedBy = $request->addChild('OrderedBy');
          $OrderedBy->addChild('FirstName', htmlspecialchars( $order['first-name'] ));
			    $OrderedBy->addChild('LastName', htmlspecialchars( $order['last-name'] ));
            $Addresses = $OrderedBy->addChild('Addresses');
              $Addresses->addChild('Name', 'Billing');
              $Addresses->addChild('StreetAddress1', htmlspecialchars( $billing['address-line-one'] ));
              $Addresses->addChild('StreetAddress2', htmlspecialchars( $billing['address-line-two'] ));
              $Addresses->addChild('City', htmlspecialchars( $billing['city'] ));
              $Addresses->addChild('State', htmlspecialchars( $billing['state'] ));
              $Addresses->addChild('ZipCode', htmlspecialchars( $billing['zip'] ));
            $Phones = $OrderedBy->addChild('Phones');
				      $PhonesRequest = $Phones->addChild('PhoneNumberRequest');
			          $PhonesRequest->addChild('Name', 'Main');
					      $PhonesRequest->addChild('Number', htmlspecialchars( $order['last-name'] ));
        $ShipTo = $request->addChild('ShipTo');
          $ShipTo->addChild('FirstName', htmlspecialchars( $order['first-name'] ));
          $ShipTo->addChild('LastName', htmlspecialchars( $order['last-name'] ));
  				$AddressesTwo = $ShipTo->addChild('Addresses');
  					$AddressesTwo->addChild('Name', 'Shipping');
  					$AddressesTwo->addChild('StreetAddress1', htmlspecialchars( $shipping['address-line-one'] ));
  					$AddressesTwo->addChild('StreetAddress2', htmlspecialchars( $shipping['address-line-two'] ));
  					$AddressesTwo->addChild('City', htmlspecialchars( $shipping['city'] ));
  					$AddressesTwo->addChild('State', htmlspecialchars( $shipping['state'] ));
  					$AddressesTwo->addChild('ZipCode',  htmlspecialchars( $shipping['zip'] ));
			    $PhoneTwo = $ShipTo->addChild('Phones');
  				  $PhonesRequestTwo = $PhoneTwo->addChild('PhoneNumberRequest');
  					  $PhonesRequestTwo->addChild('Name','Main');
  					  $PhonesRequestTwo->addChild('Number', htmlspecialchars( $order['phone-number']) );
        $request->addChild('TType', 'StandardOrder');
        $request->addChild('OrderOrigination', 'Web');
        $request->addChild('OrderApprovalStatus', 'Approved');
        $request->addChild('OrderName', htmlspecialchars( $order['key'] ));
  		  $request->addChild('CustReference', htmlspecialchars( $order['key'] ));
      $CreateOrder->addChild('userName', $this->username);
      $CreateOrder->addChild('password', $this->password);
    $Envelope = $this->SOAPFinalizeEnvelope( $CreateOrder );
    $result = $this->SOAPRequest( 'CreateOrder', $Envelope );
    if($result){
      $TransactionKey = $result['CreateOrderResult']['Transaction'].PHP_EOL;
      return $TransactionKey;
    }
    return print_r($result,true);
  }

  /**
   * Reference to the soap client
   * @var     reference   $SOAPClient
   * @access  private
   */
  private $SOAPClient = null;

  /**
   * We store the soap errors here till they are cleard
   * @var     null|string   $SOAPErrors
   * @access  private
   */
  private $SOAPErrors = null;

  /**
   * This is just a flag for quickly checking if soap is in a ready state
   * @var     boolean       $SOAPReady
   * @access  private
   */
  private $SOAPReady = null;

  /**
   * This is just a flag for quickly checking if soap is in a ready state
   * @var     boolean       $SOAPReady
   * @access  private
   */
  private $SOAPLastResult = null;

  /**
   * This is just a flag for quickly checking if soap is in a ready state
   * @var     boolean       $SOAPReady
   * @access  private
   */
  private $SOAPLastFault = null;

  /**
   * Destruct the current soap objects and recreate them
   * @method  SOAPReset
   * @access  private
   */
  private function SOAPReset(){
    unset( $this->SOAPClient );
    unset( $this->SOAPErrors );
    unset( $this->SOAPReady );
    unset( $this->SOAPLastFault );
    unset( $this->SOAPLastresult );
    $this->SOAPClient     = null;
    $this->SOAPErrors     = null;
    $this->SOAPReady      = null;
    $this->SOAPLastFault  = null;
    $this->SOAPLastresult = null;
    $this->SOAPInit();
  }

  /**
   * Initalize the SOAPClient
   * @method  SOAPInit
   * @access  private
   */
  private function SOAPInit(){
    $this->SOAPClient     = new \nusoap_client( $this->wsdl, true );
    if($this->SOAPHasErrors()){ return true; }
    echo '<h2>SOAPError: </h2><pre>' . $this->SOAPErrors . '</pre>';
    return false;
  }
  /**
   * Check if the SOAPClient is there
   * @method  SOAPClientExists
   * @return  boolean
   * @access  private
   */
  private function SOAPClientExists(){
    return (($this->SOAPClient)?true:false);
  }

  /**
   * Change the SOAPStatus to ready
   * @method SOAPStatusToReady
   * @access private
   */
  private function SOAPStatusToReady(){
    $this->SOAPReady = true;
  }

  /**
   * Change the SOAPStatus to not ready
   * @method SOAPStatusToNotReady
   * @access private
   */
  private function SOAPStatusToNotReady(){
    $this->SOAPReady = false;
  }

  /**
   * Check if the soap ready status is true
   * @method  SOAPStatusReady
   * @return  boolean
   * @access  private
   */
  private function SOAPStatusReady(){
    return (($this->SOAPReady)?true:false);
  }

  /**
   * Check if SOAP has errors
   * @method SOAPHasErrors
   * @return boolean
   * @access private
   */
  private function SOAPHasErrors(){
    $this->SOAPErrors = $this->SOAPClient->getError();
    if(!$this->SOAPErrors){
      $this->SOAPStatusToReady();
      return true;
    }
    $this->SOAPStatusToNotReady();
    return false;
  }

  /**
   * Make an envenlope that will work for complear
   * @method makeEnvelope
   * @param  string             $action The action we are requesting of complear
   * @return SimpleXMLElement           This is the start of your soap envelope
   * @access private
   */
  private function SOAPMakeEnvelope( $action ){
    return new \SimpleXMLElement("<$action xmlns=\"http://complemar.com/\"></$action>");
  }

  /**
   * Fix the envelope to prevent soap errors
   * @method finalizeEnvelope
   * @param  SimpleXMLElement     $xmlr
   * @return DOMDocument
   * @access private
   */
  private function SOAPFinalizeEnvelope( $xmlr ){
    $dom = new \DOMDocument('1.0', 'utf-8');
		$dom->xmlStandalone = false;
		$dom->formatOutput  = true;
		$dom->loadXML( $xmlr->asXML());
		return $dom->saveXML($dom->documentElement);
  }

  /**
   * Send the soap request to complemar
   * @method  SendSoapRequest
   * @param   string          $method       The requst action
   * @param   string          $requestData  The xml
   * @access  private
   */
  private function SOAPRequest( $method, $requestData ){
		$result = $this->SOAPClient->call( $method, $requestData, 'xsi' );
		if( $this->SOAPClient->fault ){
      $this->SOAPLastFault = print_r( $result, true );
      echo '<h2>Fault</h2><pre>'.$this->SOAPLastFault.'</pre>';
			return false;
		} else {
      $this->SOAPErrors = $this->SOAPClient->getError();
			if( $this->SOAPErrors ){
				echo '<h2>Error</h2><pre>' . $this->SOAPErrors . '</pre>';
				return false;
			}
		}
		return $result;
	}
}
