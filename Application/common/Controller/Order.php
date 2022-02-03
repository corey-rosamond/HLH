<?php
/**
 * Order
 *
 * @package Framework\Commander
 * @version 1.0.0
 */
namespace Framework\Commander;

/**
 * 	Order
 *
 *	This is the order object it is the main interface for Manipulating orders Information on on his objects variables
 *	And methods detailed bellow
 *
 * 	$_  Variables and methods are private none useable objects and datamembers for the controller only.
 * 	$_p Variables are pointers to a data set in use at that moment
 * 	$_m Variables are containers for modals or will be
 * 	$_c Variables are containers for controller objects or will be
 *  $_d Variables are data stoarge objects for some type of data this controller uses
 *
 * 	@todo Implement verbose mode
 */
class Order extends \Framework\Support\Abstracts\Singleton
{
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                         CONTROLLERS                                                 */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * The thracking controller this takes the load of handing tracking methods off
   * the order controller and lets the tracking controller do it
   * @var     \Framework\Commander\Tracking   $_cTracking
   * @access  private
   */
  private $_cTracking;

  /**
   * The funnel controller is the order controllers interface to any method or
   * data that is sepcific to a funnel
   * @var     \Framework\Commander\Tracking   $_cFunnel
   * @access  private
   */
  private $_cFunnel;

  /**
   * The fulfillment controller right now only points to the complemar Object
   * in the future when we have more than one fulfillment object this will
   * point to which every fullfillment object is going to be used for this order
   * @var     \Framework\Commander\Complemar   $_cFulfillment
   * @access  private
   */
  private $_cFulfillment;

  /**
   * The payment Processor controller is in charge of all actions related to payments ie refunds
   * captureing validating and any other action similar to that
   * @var     \Framework\Commander\Complemar   $_cPaymentProcessor
   * @access  private
   */
  private $_cPaymentProcessor;

  /**
   * The email controller takes care of emailing from whatever email service this site
   * or funnel has been configured to email from
   * @var     \Framework\Commander\Email       $_cEmail
   * @access  private
   */
  private $_cEmail;

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                            MODALS                                                   */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * The order modal
   * @var     \Framework\Modulus\Query  $_mOrder
   * @access  private
   */
  private $_mOrder;

  /**
   * The order Customer Modal
   * @var     \Framework\Modulus\Query  $_mCustomer
   * @access  private
   */
  private $_mCustomer;

  /**
   * The order address modal
   * @var     \Framework\Modulus\Query  $_mAddress
   * @access  private
   */
  private $_mAddress;

  /**
   * The order items Modal
   * @var     \Framework\Modulus\Query  $_mOrderItems
   * @access  private
   */
  private $_mOrderItems;

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                          DATA OBJECTS                                               */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Container for currently selected order data
   * @var     array     $_dOrder
   * @access  private
   */
  private $_dOrder;

  /**
   * Container for currently selected order customer data
   * @var     array     $_dCustomer;
   * @access  private
   */
  private $_dCustomer;

  /**
   * Container for currently selected order shipping address
   * @var     array     $_dShippingAddress
   * @access  private
   */
  private $_dShippingAddress;

  /**
   * Container for currently selected order billing address
   * @var     array     $_dBillingAddress
   * @access  private
   */
  private $_dBillingAddress;

  /**
   * Container for currently selected order items data
   * @var     array     $_dOrderItems
   * @access  private
   */
  private $_dOrderItems;

  /**
   * Container for the currently selected order tracking data
   * @var     array     $_dTracking
   * @access  private
   */
  private $_dTrackingVisit;

  /**
   * Container for the currently selected order tracking page views
   * @var     array     $dTrackingViews
   * @access  private
   */
  private $_dTrackingView;

  /**
   * This is a pointer to the currently selected address in the active order
   * @var     pointer   $_pAddress
   * @access  private
   */
  private $_pAddress;

  /**
   * Pointer to the data object currently in use
   * @var     pointer   $_pData
   * @access  private
   */
  private $_pData;

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                             CONSTRUCTORS                                            */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * The intial public construct is is only an entry point it calls the
   * private constructor and wraps it in the try catch block with the router
   * @method  construct
   * @param   int    $orderKey  Int order key or false
   * @return  Order             An instances of the order controller
   * @access  public
   */
  public static function construct( $key = false )
  {
    try {
      $instance = self::getInstance();
      return $instance->_construct( $key );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      $instance->_eRouter( $exception );
    }
  }

  /**
   * Sub Constructor does all the real constructor work
   * @method  _construct
   * @param   int         orderKey  The order key of the order we want to load the data for
   * @return  Order                 The instance of the order controller
   * @access  private
   */
  private function _construct( $key ){
    try {
      /** MODALS */
      $this->_mOrder        = \Framework\Core\Modulus::get( 'Orders',   "Holylandhealth" );
      $this->_mCustomer     = \Framework\Core\Modulus::get( 'Customer', "Holylandhealth" );
      /** CONTROLLERS */

      $this->_cTracking         = \Framework\Core\Receptionist::controller( "Tracking", false );
      $this->_cFulfillment      = \Framework\Core\Receptionist::controller( 'Complemar', false );
      $this->_cPaymentProcessor = \Framework\Core\Receptionist::controller( 'PaymentProcessor', true );
      $this->_cAddress          = \Framework\Core\Receptionist::controller( 'Address', true );
      $this->_cOrderMessages    = \Framework\Core\Receptionist::controller( 'OrderMessages', true );
      if( !$key ){
        return $this;
      }
      $this->load( $key );
      return $this;
    } catch ( \Framework\Exceptional\BaseException $exception ){
      echo '<pre>';
      print_r($exception);
      echo '</pre>';
      $this->_eThrow( self::_emConstructor, self::_eeFatal, $exception );
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                             LOADERS                                                 */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * This is the base laoder. It will call all the loaders for the sub parts
   * @method  load
   * @param   int     $key
   * @return  void
   * @uses    \Framework\Commander\Order->_loadOrder()
   * @uses    \Framework\Commander\Order->_loadAddress()
   * @access  public
   */
  public function load( $key )
  {
    $this->_loadOrder( $key );
    $this->_loadCustomer();
    //$this->_loadOrderItems();
    //$this->_loadFulfillment();
    //$this->_loadEmails();
    $this->_loadTracking();
  }

  /**
   * This will load the order data from the funnel orders table only
   * @method  _loadOrder
   * @param   int            $key
   * @return  PDOResource
   * @uses    \Framework\Commander\Order->_eThrow()
   * @throws  \Framework\Exceptional\OrderControllerException()  CODE - self::_emLoadOrder
   * @access  private
   */
  private function _loadOrder( $key )
  {
    try {
      if( !isset($this->_mOrder) ){
        $this->_mOrder = \Framework\Core\Modulus::get( 'Orders', "Holylandhealth" );
      }
      return $this->_dOrder = $this->_mOrder->get( $key );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      $this->_eThrow( self::_emLoadOrder, self::_eeFatal, $exception );
    }
  }

  private function _loadCustomer()
  {
    try {
      if( !isset($this->_mCustomer) ){
        $this->_mCustomer = \Framework\Core\Modulus::get( 'Customer', "Holylandhealth" );
      }
      $this->_dCustomer = $this->_mCustomer->get( $this->_dOrder['customer-key'] );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      $this->_eThrow( self::_emLoadCustomer, self::_eeFatal, $exception );
    }
  }








  public function _loadTracking()
  {
    try {
      if( !isset($this->_cTracking) ){
        $this->_cTracking = \Framework\Core\Receptionist::controller( "Tracking", false );
      }
      $this->_cTracking->load( $this->_dOrder['session-key'] );
      $this->_dTrackingVisit = $this->_cTracking->sessionVisit();
      $this->_dTrackingView  = $this->_cTracking->sessionView();
    } catch ( \Framework\Exceptional\BaseException $exception ){
      $this->_eThrow( self::_emLoadTracking, self::_eeFatal, $exception );
    }
  }


  public function count( $target )
  {
    try {
      switch( $target ){
        case 'message':     return 1; break;
        case 'items':       return 2; break;
        case 'processor':   return $this->_countProcessor(); break;
        case 'fulfillment': return 4; break;
        case 'email':       return 5; break;
        /** Get the total pages viewed for this order */
        case 'tracking':    return $this->_cTracking->countSessionViews( $this->_dOrder['session-key'] ); break;
      }
    } catch ( \Framework\Exceptionl\OrderController $exception ){
      print_r( $exception );
    }
  }

  private function _countItems()
  { return 4; }



  private function _countFulfillment()
  { }

  public function billingPortlet()
  { return $this->_cAddress->billingPortlet( $this->_dOrder['billing-address'], $this->_dOrder['first-name'].' '.$this->_dOrder['last-name'] ); }

  public function shippingPortlet()
  { return $this->_cAddress->shippingPortlet( $this->_dOrder['shipping-address'], $this->_dOrder['first-name'].' '.$this->_dOrder['last-name'] ); }

  public function messageCount()
  { return $this->_cOrderMessages->orderMessageCount( $this->_dOrder['key'] ); }

  public function messageLog()
  { return $this->_cOrderMessages->orderMessagesDatatable( $this->_dOrder['key'] ); }

  public function processorCount()
  { return $this->_cPaymentProcessor->orderCount( $this->_dOrder['key'] ); }

  public function processorLog()
  { return $this->_cPaymentProcessor->orderLogDatatable( $this->_dOrder['key'] ); }


  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        FORMATTED RETURN                                             */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Get the order number correclty formated
   * @method  orderNumber
   * @return  int         The order number correctly formatted
   * @uses    \Framework\Commander\Order->_setDataPointer()
   * @uses    \Framework\Commander\Order->_output()
   * @uses    \Framework\Commander\Order->_eThrow()
   * @throws  \Framework\Exceptional\OrderControllerException()  CODE - self::_emUndefinedOrderNumber
   * @access  public
   */
  public function orderNumber()
  {
    try {
      $this->_setDataPointer( 'order' );
      return $this->_output( $this->_pData['key'], 'order' );
    } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
      return $this->_eThrow( self::_emUndefinedOrderNumber, self::_eeFatal, $exception );
    }
  }

  /**
   * Order date formatted
   * @method  orderDate
   * @return  string
   * @uses    \Framework\Commander\Order->_setDataPointer()
   * @uses    \Framework\Commander\Order->_output()
   * @uses    \Framework\Commander\Order->_eThrow()
   * @throws  \Framework\Exceptional\OrderControllerException()  CODE - self::_emUndefinedOrderDate
   * @access  public
   */
  public function orderDate()
  {
    try {
      $this->_setDataPointer( 'order' );
      return $this->_output( $this->_pData['order-date'], 'date' );
    } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
      return $this->_eThrow( self::_emUndefinedOrderDate, self::_eeFatal, $exception );
    }
  }

  /**
   * Order status formated with span and correct color
   * @method  orderStatus
   * @return  string
   * @uses    \Framework\Commander\Order->_setDataPointer()
   * @uses    \Framework\Commander\Order->_output()
   * @uses    \Framework\Commander\Order->_eThrow()
   * @throws  \Framework\Exceptional\OrderControllerException()  CODE - self::_emUndefinedOrderStatus
   * @access  public
   */
  public function orderStatus()
  {
    try {
      $this->_setDataPointer( 'order' );
      return $this->_output( $this->_pData['status'], 'status' );
    } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
      return $this->_eThrow( self::_emUndefinedOrderStatus, self::_eeFatal, $exception );
    }
  }

  /**
   * Order total formatted
   * @method  orderTotal
   * @return  string                                              Formatted order total
   * @uses    \Framework\Commander\Order->_setDataPointer()
   * @uses    \Framework\Commander\Order->_output()
   * @uses    \Framework\Commander\Order->_eThrow()
   * @throws  \Framework\Exceptional\OrderControllerException()   CODE - self::_emUndefinedOrderTotalBefore
   * @access  public
   */
  public function orderTotal()
  {
    try {
      $this->_setDataPointer( 'order' );
      return $this->_output( $this->_pData['order-total-before'], 'money' );
    } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
      return $this->_eThrow( self::_emUndefinedOrderTotalBefore, self::_eeFatal, $exception );
    }
  }

  /**
   * This gets a named order paramater
   * @method  param
   * @param   String        $param                                Paramater to return
   * @param   string        $selector                             Data selector we will use when returning this paramater
   * @return  mixed                                               String containing the paramater value or return from the exception caused
   * @uses    \Framework\Commander\Order->_setDataPointer()
   * @uses    \Framework\Commander\Order->_eThrow()
   * @throws  \Framework\Exceptional\OrderControllerException()   CODE - self::_emUndefinedParam
   * @access  public
   */
  public function param( $param, $selector = 'order' )
  {
    try {
      $this->_setDataPointer( $selector );
      return $this->_pData[$param];
    } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
      return $this->_eThrow( self::_emUndefinedParam, self::_eeFatal, $exception );
    }
  }

  /**
   * This is the interface for getting address paramaters
   * @method  address
   * @param   string  $param        The paramater to return
   * @param   string  $selector     System selector to use
   * @return  string                Requested paramater value
   * @uses    \Framework\Commander\Order->_setAddressPointer()
   * @uses    \Framework\Commander\Order->_output()
   * @uses    \Framework\Commander\Order->_upperTrim()
   * @uses    \Framework\Commander\Order->_lowerTrim()
   * @uses    \Framework\Commander\Order->_eThrow()
   * @throws  \Framework\Exceptional\OrderControllerException()  CODE - self::_emUndefinedAddressParamater
   * @access  public
   */
  public function address( $param, $selector )
  {
    try {
      $this->_setAddressPointer( $selector );
      /** If we are getting a state give them the return from the output method */
      if( $this->_upperTrim($param) === 'STATE' ){
        return $this->_output( $this->_pAddress[ $param ], $this->_lowerTrim('state') );
      }
      /** Otherwise just give back the data in the paramater requested */
      return $this->_pAddress[$param];
    } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
      return $this->_eThrow( self::_emUndefinedAddressParamater, self::_eeFatal, $exception );
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       POINTER SELECTOR METHODS                                      */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Moves the data pointer to the requested data object
   * @method  _setDataPointer
   * @param   string    $type   string representation of the data we want to select
   * @return                    pointer to the part of the Order object containing the correct information
   * @uses    \Framework\Commander\Order->_upperTrim()
   * @throws  \Framework\Exceptional\OrderControllerException()  CODE - self::_emBadDataSelector
   * @access  private
   */
  private function _setDataPointer( $selector )
  {
    switch( $this->_upperTrim($selector) ){
      /** Move the pointer the the order data object */
      case 'ORDER':         return $this->_pData =& $this->_dOrder;                                       break;
      /** Move the pointer the the customer data object */
      case 'CUSTOMER';      return $this->_pData =& $this->_dCustomer;                                    break;
      /** Move the pointer to the tracking data */
      case 'TRACKING':      return $this->_pData =& $this->_dTrackingVisit;                               break;
      /** Move the pointer to the tracking data page views */
      case 'TRACKINGVIEWS': return $this->_pData =& $this->_dTrackingView;                                break;
      /** Invalid selector passed throw an exception */
      default:              return $this->_eThrow( self::_emBadDataSelector, self::_eeFatal, $exception );break;
    }
  }

  /**
   * Moves the address pointer to the correct address this way we don't keep making
   * new address objects that the garbage cleanup will forget to remove
   * @method  _addressPointer
   * @param   string    $type   string representation of the address we want to select
   * @return                    pointer to the part of the Order object containing the correct address
   * @uses    \Framework\Commander\Order->_upperTrim()
   * @throws  \Framework\Exceptional\OrderControllerException()  CODE - self::_emBadAddressSelector
   * @access  private
   */
  private function _setAddressPointer( $selector )
  {
    switch( $this->_upperTrim($selector) ){
      /** Move the pointer to the shipping address */
      case 'SHIPPING':  return $this->_pAddress  =& $this->_dShipping;                                    break;
      /** Move the pointer the the billing address */
      case 'BILLING':   return $this->_pAddress  =& $this->_dBilling;                                     break;
      /** Invalid selector passed throw exception */
      default:          return $this->_eThrow( self::_emBadAddressSelector, self::_eeFatal, false );      break;
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                           BLIND RETURN                                              */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  public function pageViews()
  {
    try {
      $this->_setDataPointer( 'trackingviews' );
      $extraTitle = [ '', '<strong>Total Views</strong>', '<strong>Total Duration</strong>' ];
      $extraValue = [ '', 0, 0];
      $return = [];
      for( $i=0; $i<sizeof($this->_pData); $i++){
        $extraValue[1]++;
        $extraValue[2] += doubleval($this->_pData[$i]['time-on-page']);
        array_push($return,$this->_pData[$i]);
      }
      $extraValue[2] = $extraValue[2]." Minutes";
      array_push($return, $extraTitle);
      array_push($return, $extraValue);
      return $return;
    } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
      return $this->_eThrow( self::_emPageViews, self::_eeFatal, $exception );
    }
  }

  public function siteVisit()
  {
    try {
      $this->_setDataPointer('tracking');
      return [['ip-address',$this->_pData['ip-address']],['site-visited',$this->_pData['site-visited']],['link',$this->_pData['link']]];
    } catch (\Framework\Exceptional\UndefinedIndexException $exception){
      return $this->_eThrow(self::_emSiteVisit(), self::_eeFatal, $exception);
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       ORDER WIDE DATA FORMATTERS                                    */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Trim any leading or trailing white space off a string
   * @method  _trim
   * @param   string    $data   The data to run the transformation on
   * @return  string            The data after the transformation
   * @access  private
   */
  private function _trim( $data )
  { return ltrim( rtrim( $data ) ); }

  /**
   * Trim the start and end of a string and convert
   * the characters to uppercase
   * @method  _upperTrim
   * @param   string      $data     The string to run the transformation against
   * @return  string                The string after transformation
   * @access  private
   */
  private function _upperTrim( $data )
  { return $this->_trim( strtoupper( $data )); }

  /**
   * Trim the start and end of a string and convert
   * the characters to lowecase
   * @method  _upperTrim
   * @param   string      $data     The string to run the transformation against
   * @return  string                The string after transformation
   * @access  private
   */
  private function _lowerTrim( $data )
  { return $this->_trim( strtolower( $data )); }

  /**
   * The output function is in charge of formatting all output for an order
   * Such as dates money int's doubl's or states, Anything formatting related should
   * be added here
   * @method  _output
   * @param   string    $data         Data to be formatted
   * @param   string    $selector     The format to convert it to
   * @return  string                  The string after requested formmating if any has been done
   * @access  private
   */
  private function _output( $data, $selector )
  {
    switch( $this->_upperTrim($selector) ){
      /** Format a date associated with an order */
      case 'DATE':      return date( "l jS \of F Y h:i:s A", strtotime( $data ) );  break;
      /** Format money to a human readable string */
      case 'MONEY':     return "$".number_format( $data, 2 );                       break;
      /** Format the order number */
      case 'ORDER':     return sprintf('#%08d', $data );                            break;
      /** Format an integer */
      case 'INT':       return number_format( $data, 0 );                           break;
      /** Format a double */
      case 'DOUBLE':    return number_format( $data, 2 );                           break;
      /** return format with upper trim */
      case 'UPPERTRIM': return $this->_upperTrim( $data );                          break;
      /** return format with lower trim */
      case 'LOWERTRIM': return $this->_lowerTrim( $data );                          break;
      /** Format the order status */
      case 'STATUS':
        return "<span class=\"label label-{$this->_statusColor[$data]}\"> {$data} </span>";
      break;
      /** Format the state for an order */
      case 'STATE':
        try {
          return $this->_states[$this->_upperTrim($data)];
        } catch ( \Framework\Exceptional\UndefinedIndexException $exception ){
          return $this->_eThrow( self::_emUndefinedState, self::_eeFatal, $exception );
        }
      break;
      /** By default just return the data */
      default:      return $this->trim($data);                                                 break;
    }
  }

  /**
   * The input function is for sanatizing / transformations that need to
   * be done before a given piece of data can be pushed into the order
   * structure / database
   * @method  _input
   * @param   string    $data         Data to be formatted
   * @param   string    $selector     The format to convert it to
   * @return  string                  The string after requested formmating if any has been done
   * @access  private
   */
  private function _input( $data, $selector )
  {

  }


  private function _validate()
  {

  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       EXCEPTION HANDLING                                            */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Constant for all exception message this will be used the the order controller is put into verbose model
   * Verbose mode basicly causes the order object to write a VERY detailed log of everything it does not matter
   * How large or small this should never be used in production
   */
  private $_ePrefix        = '[OrderController]: ';

  /**
   * EXCEPTION MESSAGE CONSTANTS
   * This is a list of all exception messages that can be thrown by the
   * controller and the constants used to throw them
   *    100   : Contructor failure
   * 101 - 120: Loader failure specic to the loader that failed
   * 121 - 140: Bad selector used to access some method or data specic to that method or data object
   * 141 - 160: Undefined index of specic to the type that was undefined
   * 161 - 180: Method Failure faiilure message
   * 181 - 201: Validation Errors
   */
  const _emConstructor                = 100;

  /** LOADER */
  const _emLoad                       = 101;
  const _emLoadCustomer               = 102;
  const _emLoadOrder                  = 103;
  const _emLoadAddress                = 104;
  const _emLoadProcessor              = 105;
  const _emLoadTracking               = 106;
  const _emLoadOrderItems             = 107;
  const _emLoadPaymentProcessor       = 108;
  const _emLoadFulfillment            = 109;
  const _emLoadEmails                 = 110;

  /** BAD SELECTOR */
  const _emBadAddressSelector         = 121;
  const _emBadDataSelector            = 122;

  /** UNDEFINED */
  const _emUndefinedOrderNumber       = 140;
  const _emUndefinedState             = 141;
  const _emUndefinedAddressParamater  = 142;
  const _emUndefinedOrderTotalBefore  = 143;
  const _emUndefinedOrderStatus       = 144;

  /** METHOD FAILURE */
  const _emPageViews                  = 161;

  /** VALIDATION */
  const _emInvalidAddress             = 181;

  /**
   * EXCEPTION EVENT CODES
   * These are codes that relate to an event to be
   * ran in the case of a specific exception these are
   * when the controller is trying to fix an error or
   * simply failing
   */
  const _eeFatal                      = 200;

  /**
   * Return the exception message for the code that was provided
   * @method  _eMessage
   * @param   integer     $code     Code we want the message for
   * @return  string                The exception message specific to that error code or the default
   * @access  private
   */
  private function _eMessage( $code = 0 )
  {
    switch( $code ){
      /**                                          CONSTRUCTOR                                               */
      case self::_emConstructor:          return "{$this->_ePrefix} Construtor Exception fatal!";        break;

      /**                                            LOADER                                                  */
      case self::_emLoad:                 return "{$this->_ePrefix} Load Exception fatal!";              break;
      case self::_emLoadCustomer:         return "{$this->_ePrefix} Load Customer Exception fatal!";     break;
      case self::_emLoadOrder:            return "{$this->_ePrefix} Load Order Exception fatal!";        break;
      case self::_emLoadAddress:          return "{$this->_ePrefix} Load Address Exception fatal!";      break;
      case self::_emLoadProcessor:        return "{$this->_ePrefix} Load Processor Exception fatal!";    break;
      case self::_emLoadTracking:         return "{$this->_ePrefix} Load Tracking Exception fatal!";     break;
      case self::_emLoadEmails:           return "{$this->_ePrefix} Load Emails Exception fatal!";       break;

      /**                                           BAD SELECT                                               */
      case self::_emBadAddressSelector:   return "{$this->_ePrefix} Bad Address Selector!";              break;
      case self::_emBadDataSelector:      return "{$this->_ePrefix} Bad DATA Selector!";                 break;

      /**                                           DEFAULT MESSAGE                                          */
      default:                            return "{$this->_ePrefix} Default Exception fatal!";           break;
    }
  }

  /**
   * Route a thrown exception to an event
   * @method  _eRouter
   * @param   \Framework\Exceptional\BaseException  $exception
   * @return  void
   * @access  private
   * @todo    Finish the router for the order Controller
   */
  private function _eRouter( $exception )
  {
    switch( $exception->getCode() ){
      case self::_emInvalidAddress:  $this->_returnError( "Invalid Address!" );  break;



      default:
        \Framework\Core\Architect::fallenException(
          'Common\OrderController',
          'Order Controller Exception',
          $exception->getMessage(),
          $exception->getFile(),
          $exception->getLine()
        );
      break;
    }
  }

  /**
   * Rethrow an exception to an exception more specific to the
   * @method  _eThrow
   * @param   string                                $message   The exception message you want to rethrow with
   * @param   int                                   $code      The controller specific error code you want to throw
   *                                                           this exception with this will most likely related to an
   *                                                           exception event. Allowing the controller to recover from
   *                                                           exceptions without ever needing to ask for help
   * @param   \Framework\Exceptional\BaseException  $exception The core exception object that was orinally thrown
   * @return  void
   * @access  private
   */
  private function _eThrow( $message, $code, $exception )
  {
    /** Check if this is an interger */
    if( is_int( $message ) ){
      /** This is an integer that means it is a message for this sytem get it from the message function */
      $message = $this->_eMessage( $message );
    }
    throw new \Framework\Exceptional\OrderControllerException(
      $message,
      $code,
      (( $exception !== false )? $exception->getCode() : 0),
      (( $exception !== false )? $exception->getFile() : "/Framework/Appliaction/common/Controller/Order.php"),
      (( $exception !== false )? $exception->getLine() : 0),
      (( $exception !== false )? $exception : false )
    );
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                      STATIC DATA OBJECTS                                            */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Order status to color code
   * @var     array     $_statusColor
   * @access  private
   */
  private $_statusColor = [
    "Created"       => 'danger',
    "Captured"      => "success",
    "Approved"      => "warning",
    "Fulfillment"   => "info",
    "Complete"      => "primary",
    "Auth Capture"  => "default",
    "Canceled"      => "danger",
    "Authorized"    => "primary"
  ];


}


/*
  Alabama         State   US-AL   AL  01  AL  AL  Ala.    Ala.
  Alaska          State   US-AK   AK  02  AK  AK  Alaska  Alaska  Alas.
  Arizona         State   US-AZ   AZ  04  AZ  AZ  Ariz.   Ariz.   Az.
  Arkansas        State   US-AR   AR  05  AR  AR  Ark.    Ark.
  California      State   US-CA   CA  06  CA  CF  Calif.  Calif.  Ca., Cal.
  Colorado        State   US-CO   CO  08  CO  CL  Colo.   Colo.   Col.
  Connecticut     State   US-CT   CT  09  CT  CT  Conn.   Conn.   Ct.
  Delaware        State   US-DE   DE  10  DE  DL  Del.    Del.    De.
  Florida         State   US-FL   FL  12  FL  FL  Fla.    Fla.    Fl., Flor.
  Georgia         State   US-GA   GA  13  GA  GA  Ga.     Ga.
  Hawaii          State   US-HI   HI  15  HI  HA  Hawaii  Hawaii  H.I.
  Idaho           State   US-ID   ID  16  ID  ID  Idaho   Idaho   Id., Ida.
  Illinois        State   US-IL   IL  17  IL  IL  Ill.    Ill.    Il., Ills., Ill's
  Indiana         State   US-IN   IN  18  IN  IN  Ind.    Ind.    In.
  Iowa            State   US-IA   IA  19  IA  IA  Iowa    Iowa    Ia., Ioa.[1]
  Kansas          State   US-KS   KS  20  KS  KA  Kans.   Kan.    Ks., Ka.
  Kentucky        State   US-KY   KY  21  KY  KY  Ky.     Ky.     Ken., Kent.
  Louisiana       State   US-LA   LA  22  LA  LA  La.     La.
  Maine           State   US-ME   ME  23  ME  ME  Maine   Maine   Me.
  Maryland        State   US-MD   MD  24  MD  MD  Md.     Md.
  Massachusetts   State   US-MA   MA  25  MA  MS  Mass.   Mass.
  Michigan        State   US-MI   MI  26  MI  MC  Mich.   Mich.
  Minnesota       State   US-MN   MN  27  MN  MN  Minn.   Minn.   Mn.
  Mississippi     State   US-MS   MS  28  MS  MI  Miss.   Miss.
  Missouri        State   US-MO   MO  29  MO  MO  Mo.     Mo.
  Montana         State   US-MT   MT  30  MT  MT  Mont.   Mont.
  Nebraska        State   US-NE   NE  31  NE  NB  Nebr.   Neb.
  Nevada          State   US-NV   NV  32  NV  NV  Nev.    Nev.    Nv.
  New Hampshire   State   US-NH   NH  33  NH  NH  N.H.    N.H.
  New Jersey      State   US-NJ   NJ  34  NJ  NJ  N.J.    N.J.
  New Mexico      State   US-NM   NM  35  NM  NM  N. Mex. N.M.    New M.
  New York        State   US-NY   NY  36  NY  NY  N.Y.    N.Y.    N. York
  North Carolina  State   US-NC   NC  37  NC  NC  N.C.    N.C.    N. Car.
  North Dakota    State   US-ND   ND  38  ND  ND  N. Dak. N.D.    NoDak
  Ohio            State   US-OH   OH  39  OH  OH  Ohio    Ohio    O., Oh.
  Oklahoma        State   US-OK   OK  40  OK  OK  Okla.   Okla.   Ok.
  Oregon          State   US-OR   OR  41  OR  OR  Oreg.   Ore.    Or.
  Pennsylvania    State   US-PA   PA  42  PA  PA  Pa.     Pa.     Penn., Penna.
  Rhode Island    State   US-RI   RI  44  RI  RI  R.I.    R.I.    R.I. & P.P., R. Isl.
  South Carolina  State   US-SC   SC  45  SC  SC  S.C.    S.C.    S. Car.
  South Dakota    State   US-SD   SD  46  SD  SD  S. Dak. S.D.    SoDak
  Tennessee       State   US-TN   TN  47  TN  TN  Tenn.   Tenn.
  Texas           State   US-TX   TX  48  TX  TX  Tex.    Texas   Tx.
  Utah            State   US-UT   UT  49  UT  UT  Utah    Utah    Ut.
  Vermont         State   US-VT   VT  50  VT  VT  Vt.     Vt.
  Virginia        State   US-VA   VA  51  VA  VA  Va.     Va.     Virg.
  Washington      State   US-WA   WA  53  WA  WN  Wash.   Wash.   Wa., Wn.[2]
  West Virginia   State   US-WV   WV  54  WV  WV  W. Va.  W.Va.   W.V., W. Virg.
  Wisconsin       State   US-WI   WI  55  WI  WS  Wis.    Wis.    Wi., Wisc.
  Wyoming         State   US-WY   WY  56  WY  WY  Wyo.    Wyo.    Wy.
*/
