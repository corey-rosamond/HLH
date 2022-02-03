<?php
/**
 * Funnel
 *
 * @package Framework\Commander
 * @version 1.2.0
 */
namespace Framework\Commander;

/**
 * Funnel
 *
 * This is the main funnel command object.
 */
class Funnel extends Commander
{
  /** THESE ARE THE MODES A FUNNEL CAN BE IN */
  /** @const live Live mode */
  const live = 0;
  /** @const development Development Mode */
  const development = 1;
  /** @const maintance Maintance Mode */
  const maintance = 2;
  /** @const comingsoon Coming Soon Mode */
  const comingsoon = 3;
  /** @const outofstock Out of Stock Mode */
  const outofstock = 4;
  /** THESE CONSTANTS REPRESENT DIFFERENT PAGE TYPES */
  /** @const vsl */
  const vsl = 1;
  /** @const checkout */
  const checkout = 2;
  /** @const checkout */
  const upsell1 = 3;
  /** @const upsell1 */
  const upsell2 = 4;
  /** @const upsell2 */
  const upsell3 = 5;
  /** @const upsell3 */
  const upsell4 = 6;
  /** @const upsell4 */
  const upsell5 = 7;
  /** @const upsell5 */
  const antiSpamPolicy = 8;
  /** @const antiSpamPolicy */
  const contactUs = 9;
  /** @const contactUs */
  const disclaimer = 10;
  /** @const privacyPolicy */
  const privacyPolicy = 11;
  /** @const refunds */
  const refunds = 12;
  /** @const termsAndConditions */
  const termsAndConditions = 13;
  /** @const thankYou */
  const thankYou = 14;
  /** @const download */
  const download = 15;
  /** @const downsell1 */
  const downsell1 = 16;
  /** @const downsell2 */
  const downsell2 = 17;
  /** @const downsell3 */
  const downsell3 = 18;
  /** @const downsell4 */
  const downsell4 = 19;
  /** @const downsell5 */
  const downsell5 = 20;
  /** @const advertorial */
  const advertorial = 21;
  /** @const error */
  const error = 22;
  /** @const notFound */
  const notFound = 23;
  /** @const tsl */
  const tsl = 24;
  /** @var \Framework\Commander\Tracking $_cTracking */
  private $_cTracking;
  /** @var \Framework\Commander\Services $_cServices */
  private $_cServices;
  /** @var \Framework\Commander\Products $_cProducts */
  private $_cProducts;
  /** @var \Framework\Commander\Email $_cEmail*/
  private $_cEmail;
  /** @var \Framework\Commander\EmailLog $_cEmailLog */
  private $_cEmailLog;
  /** @var \Framework\Commander\SmartStreets $_cSmartStreets */
  private $_cSmartStreets;
  /** @var \Framework\Commander\Customer $_cCustomer */
  private $_cCustomer;
  /** @var \Framework\Commander\Orders $_cOrders */
  private $_cOrders;
  /** @var \Framework\Modulus\Modal\Funnel $_mFunnel */
  private $_mFunnel;
  /** @var \Framework\Modulus\Modal\FunnelPage $_mPage */
  private $_mFunnelPage;
  /** @var \Framework\Modulus\Modal\EmailTemplates $_mTemplates */
  private $_mEmailTemplates;
  /** DATA */
  private $_fData;
  private $_fpData;
  private $_fpProducts;
  private $_dOrder;
  /** PAGE DESTINATIONS BASED ON SUCCESS OR FAILURE */
  private $_PageSuccessDestination;
  private $_PageFailureDestination;
  /** SESSION PREFIX FOR SYSTEM AND CONTROLLER */
  protected $_sPrefix;
  private $_cPrefix = 'funnel-data';
  /** EMAIL TEMPLATES */
  private $_templateSupportCustomer;
  private $_templateSupportCompany;
  private $_templateEmailReceipt;
  /** SERVICE TOKENS */
  private $_tSupportEmail;
  private $_tOrderEmail;
  private $_tAddressValidation;
  private $_tPaymentProcessor;
  /** LOG KEYS */
  private $_ContactUsEmailLogKey;
  private $_CustomReceiptEmailLogKey;
  private $_oKey;
  /** @var array $_funnelMap This is a map that represents the path each visitor can follow */
  private $_funnelMap = [
    0=>self::vsl,
    1=>self::checkout,
    2=>self::upsell1,
    3=>self::downsell1,
    4=>self::upsell2,
    5=>self::downsell2,
    6=>self::upsell3,
    7=>self::downsell3,
    8=>self::upsell4,
    9=>self::downsell4,
    10=>self::upsell5,
    11=>self::downsell5,
    12=>self::thankYou
  ];
  /** @var array $_funnelPageNames */
  private $_funnelPageNames = [
    self::vsl => 'Video Sales Letter',
    self::checkout => 'Checkout Page',
    self::upsell1 => 'Upsell 1',
    self::upsell2 => 'Upsell 2',
    self::upsell3 => 'Upsell 3',
    self::upsell4 => 'Upsell 4',
    self::upsell5 => 'Upsell 5',
    self::antiSpamPolicy => 'Anti Spam Policy',
    self::contactUs => 'Contact Us',
    self::disclaimer => 'Disclaimer',
    self::privacyPolicy => 'Privacy Policy',
    self::refunds => 'Refunds',
    self::termsAndConditions => 'Terms and Conditions',
    self::thankYou => 'Thank You page',
    self::download => 'Download',
    self::downsell1 => 'Downsell 1',
    self::downsell2 => 'Downsell 2',
    self::downsell3 => 'Downsell 3',
    self::downsell4 => 'Downsell 4',
    self::downsell5 => 'Downsell 5',
    self::advertorial => 'Advertorial',
    self::error => 'Error Page',
    self::notFound => '404 Page not found',
    self::tsl => 'Text Sales Letter'
  ];

  /**
   * Construct the funnel object
   * @method construct
   * @return \Framework\Commander\Funnel
   */
  public static function construct()
  {
    /** @var \Framework\Commander\Funnel $instance */
    $instance = self::getInstance();
    /** @var \Framework\Core\Euri $_coreEuri */
    $instance->_coreEuri = \Framework\Core\Euri::getInstance();
    /** @var \Framework\Modulus\Modal\Funnel $_mFunnel */
    $instance->_mFunnel = \Framework\Core\Receptionist::modal("Funnel", "Holylandhealth", true);
    /** @var \Framework\Modulus\Modal\FunnelPage $_mFunnelPage */
    $instance->_mFunnelPage = \Framework\Core\Receptionist::modal("FunnelPage", "Holylandhealth", true);
    /** @var \Framework\Modulus\Modal\EmailTemplates _mEmailTemplates */
    $instance->_mEmailTemplates = \Framework\Core\Receptionist::modal("EmailTemplates", "Holylandhealth", true);
    /** @var \Framework\Commander\Tracking $_cTracking */
    $instance->_cTracking = \Framework\Core\Receptionist::controller("Tracking", true);
    /** @var \Framework\Commander\Services $_cServices */
    $instance->_cServices = \Framework\Core\Receptionist::controller("Services", true);
    /** @var \Framework\Commander\Products $_cProducts */
    $instance->_cProducts = \Framework\Core\Receptionist::controller("Products", true);
    /** @var \Framework\Commander\Email $_cEmail */
    $instance->_cEmail = \Framework\Core\Receptionist::controller("Email", true);
    /** @var \Framework\Commander\EmailLog $_cEmailLog */
    $instance->_cEmailLog = \Framework\Core\Receptionist::controller("EmailLog", true);
    /** @var \Framework\Commander\SmartStreets $_cSmartStreets */
    $instance->_cSmartStreets = \Framework\Core\Receptionist::controller("SmartStreets", true);
    /** @var \Framework\Commander\Customer $_cCustomer */
    $instance->_cCustomer = \Framework\Core\Receptionist::controller("Customer", true);
    /** @var \Framework\Commander\Order $_cOrders */
    $instance->_cOrders = \Framework\Core\Receptionist::controller("Orders", true);
    /** @var string $_sPrefix */
    $instance->_sPrefix = $instance->_coreEuri->server('host');
    /** Check the session for any data we can load and save on the data base query */
    $instance->_checkSession();
    /** Return the confugred funnel object to the caller */
    return $instance;
  }

  public function defaultNotFoundPage()
  {
    unset($this->_fpData);
    $entry = $this->_mFunnelPage->getPageByType( $this->_fData['key'], self::vsl );
    if($entry===false){
      $entry = ['footer-content' => "&copy; HolylandHealth LLC &at; 2015"];
    }
    $this->_fpData = [
      'key'           => 0,
      'funnel-key'    => $this->_fData['key'],
      'page-type-key' => self::notFound,
      'name'          => 'Default 404 Page',
      'header-content'=> '404 Page not found',
      'footer-content'=> $entry['footer-content'],
      'title'         => '404 Page not found!',
      'content' => json_encode([
        'content' => [
          'content'       => "Ooops It looks like the requested page (%s) does not exist!",
          'title'         => '404 Page not found!'
        ],
         'extra-one' => ["type"=>0],
        'extra-two' => ["type"=>0],
        'extra-three' => ["type"=>0],
        'extra-four' => ["type"=>0]
      ])
    ];
  }

  public function defaultErrorPage()
  {
    $entry = $this->_mFunnelPage->getPageByType( $this->_fData['key'], self::vsl );
    if($entry===false){
      $entry = ['footer-content' => "&copy; HolylandHealth LLC &at; 2015"];
    }
    unset($this->_fpData);
    $this->_fpData = [
      'key'           => 0,
      'funnel-key'    => $this->_fData['key'],
      'page-type-key' => self::error,
      'name'          => 'Default Error Page',
      'header-content'=> 'Opps Our system has encountered an error!',
      'footer-content'=> $entry['footer-content'],
      'title'         => 'Error!',
      'content' => json_encode([
        'content' => [
          'content'       => "Our System has encountered an error. Our staff has been notified and will work to resolve the problem in a timely fashion!",
          'title'         => 'Error!'
        ],
        'extra-one' => ["type"=>0],
        'extra-two' => ["type"=>0],
        'extra-three' => ["type"=>0],
        'extra-four' => ["type"=>0]
      ])
    ];
  }

  /**
   * Use the Host to get the current funnel key
   * @method
   * @param $host
   * @return boolean
   */
  public function findFunnelKey( $host )
  { return $this->_mFunnel->findFunnelKey("https://{$host}"); }

  public function load($key)
  {
    if(!is_null($this->_fData)&&intval($this->_fData['key'])==intval($key)){
      return $this->_fData;
    }
    $this->_fData = $this->_mFunnel->get($key);
    $this->_fData['tokens'] = json_decode($this->_fData['tokens'],true);
    $_SESSION[$this->_sPrefix][$this->_cPrefix]['fData'] = $this->_fData;
    return $this->_fData;
  }

  private function _checkSession()
  {
    if(isset($_SESSION[$this->_sPrefix][$this->_cPrefix]['fData'])){
      $this->_fData = $_SESSION[$this->_sPrefix][$this->_cPrefix]['fData'];
      if(isset($_SESSION[$this->_sPrefix][$this->_cPrefix]['ContactUsEmailLogKey'])){
        $this->_ContactUsEmailLogKey = $_SESSION[$this->_sPrefix][$this->_cPrefix]['ContactUsEmailLogKey'];
      }
      if(isset($_SESSION[$this->_sPrefix][$this->_cPrefix]['CustomRecieptEmailLogKey'])){
        $this->_CustomReceiptEmailLogKey = $_SESSION[$this->_sPrefix][$this->_cPrefix]['CustomRecieptEmailLogKey'];
      }
      if(isset($_SESSION[$this->_sPrefix][$this->_cPrefix]['oKey'])){
        $this->_oKey = $_SESSION[$this->_sPrefix][$this->_cPrefix]['oKey'];
        $this->_dOrder = $this->_cOrders->loadOrder( $this->_oKey );
      }
    }
    return false;
  }

  private function _configurePage()
  {
    if(isset($this->_fpData['configuration'])&&!is_array($this->_fpData['configuration'])){
      $this->_fpData['configuration'] = json_decode($this->_fpData['configuration'],true);
    }
    return $this->_fpData;
  }

  private function _prepSession()
  {
    if(!isset($_SESSION[$this->_sPrefix])){
      $_SESSION[$this->_sPrefix] = [];
    }
    if(!isset($_SESSION[$this->_sPrefix][$this->_cPrefix])){
      $_SESSION[$this->_sPrefix][$this->_cPrefix] = [];
    }
  }

  public function getProducts($products)
  {
    $this->_fpProducts = $products;
    foreach( $this->_fpProducts as $key => &$data ){
      $this->_fpProducts[$key] = $this->_cProducts->getProduct( $key );
    }
    return $this->_fpProducts;
  }

  public function getCustomer($email)
  {
    $customer = $this->_cCustomer->emailLookup($email);
    if(!$customer){
      $customer = $this->_cCustomer->createCustomer($email, $this->_fData['key']);
    }
    return $customer;
  }

  public function customer( $customer = false )
  {
    if($customer === false){
      if(!isset($this->_dOrder['customer-key'])){
        $this->_dOrder = $this->order();
      }
      $customer = $this->_dOrder['customer-key'];
    }
    return $this->_cCustomer->get( $customer );
  }

  public function order()
  { return $this->_cOrders->order(); }

  public function orderItems()
  { return $this->_cOrders->orderItems(); }

  public function billingAddress()
  { return $this->_cOrders->billingAddress(); }

  public function shippingAddress()
  { return $this->_cOrders->shippingAddress(); }

  public function loadOrder()
  {
    if(is_null($this->_oKey)){
      return false;
    }
    return true;
  }

  public function makeOrder($customerKey, $firstName, $lastName, $phoneNumber, $billingAddress, $shippingAddress, $isTest=false)
  {
    $this->_oKey = $this->_cOrders->makeOrder(
      $customerKey,
      $firstName,
      $lastName,
      $phoneNumber,
      $this->_cTracking->sessionKey(),
      $this->_fData['key'],
      $this->_cTracking->campaignKey(),
      $billingAddress,
      $shippingAddress,
      $isTest
    );
    $_SESSION[$this->_sPrefix][$this->_cPrefix]['oKey'] = $this->_oKey;
    return true;
  }

  public function processCart($products)
  {
    foreach($products as &$product){
      if(!$this->_cOrders->inOrderItems($product)){
        $this->_cOrders->addProductToOrder($product);
      }
    }
    $this->_tPaymentProcessor = $this->_cServices->get( $this->_fData['payment-processor'] );
    $ccData = ['card-number'=>$_POST['card-number'],'cvv'=>$_POST['cvv'],'expiration-month'=>$_POST['expiration-month'],'expiration-year'=>$_POST['expiration-year']];
    return $this->_cOrders->reconcileOrder($this->_tPaymentProcessor, $ccData, $this->_sPrefix);
  }

  public function processCartAdditions($products)
  {
    foreach($products as &$product){
      if(!$this->_cOrders->inOrderItems($product)){ $this->_cOrders->addProductToOrder($product); }
    }
    $this->_tPaymentProcessor = $this->_cServices->get( $this->_fData['payment-processor'] );
    return $this->_cOrders->reconcileOrderAdditions( $this->_tPaymentProcessor, $this->_sPrefix );
  }

  public function closeoutOrder()
  {
    $order = $this->order();
    $this->_cOrders->_setOrderTotals($order['key']);
    $this->_cOrders->markOrderCompleted($order['key']);
    $this->_cCustomer->updateCustomerValue($order['customer-key']);
    $this->sendEmailReceipt();
    return true;
  }

  public function orderBalance()
  {
    $orderItems = $this->orderItems();
    $balance = 0;
    foreach($orderItems as &$item){
      if( $item['status'] == \Framework\Commander\OrderItems::added ){
        $balance += $item['price'];
      }
    }
    return $balance;
  }

  public function sendEmailReceipt()
  {
    if(is_null($this->_tOrderEmail)){
      $this->_tOrderEmail = $this->_cServices->get( $this->_fData['order-email'] );
    }
    if(is_null($this->_templateEmailReceipt)){
      $this->_templateEmailReceipt = $this->_mEmailTemplates->get( $this->_fData['order-receipt-email-template'] );
    }
    $customer   = $this->customer();
    $order      = $this->order();
    $billing    = $this->_cOrders->billingAddress();
    $shipping   = $this->_cOrders->shippingAddress();
    $items      = $this->orderItems();
    $orderTable = '<table cellspacing="0" cellpadding="10" style="width:580px;">';
    $orderTotal = 0;
    foreach ($items as $item){
      if($item['status']==\Framework\Commander\OrderItems::reconciled){
        $orderTable .= '<tr>';
          $orderTable .= '<td style="text-align:center;width:50px;font-size:14px;">1</td>';
          $orderTable .= '<td style="font-size:14px;text-align:left;">' . $item['name'] . '</td>';
          $orderTable .= '<td style="width:100px;font-size:14px;text-align:right;">$' . number_format ($item['price'], 2) . '</td>';
        $orderTable .= '</tr>';
        $orderTotal += $item['price'];
      }
    }
    $orderTable .= '</table>';
    $tokens = [
      '{site-name}'           => $this->_fData['tokens']['{site-name}'],
      '{support-email}'       => $this->_fData['tokens']['{support-email}'],
      '{support-number}'      => $this->_fData['tokens']['{support-phone}'],
      '{order-number}'        => sprintf('#%08d',$order['key']),
      '{order-date}'          => date("l jS \of F Y h:i:s A",strtotime($this->_dOrder['order-date'])),
      '{shipping-information}'=> "{$shipping['name']}<br />{$shipping['address-line-one']}<br />{$shipping['address-line-two']}<br />{$shipping['city']} {$shipping['state']}, {$shipping['zip']}",
      '{billing-information}' => "{$billing['name']}<br />{$billing['address-line-one']}<br />{$billing['address-line-two']}<br />{$billing['city']} {$billing['state']}, {$billing['zip']}",
      '{order-table}'         => $orderTable,
      '{subtotal}'            => number_format( $orderTotal, 2 ),
      '{order-total}'         => number_format( $orderTotal, 2 ),
      '{shipping}'            => '0.00',
      '{tax}'                 => '0.00'
    ];
    $emailReceipt = $this->_cEmail->emailHandle( $this->_tOrderEmail );
    $emailReceipt->to($customer['email-address']);
    $emailReceipt->from(array($this->_tOrderEmail->username() => $this->_tOrderEmail->name()));
    $emailReceipt->subject($this->_templateEmailReceipt['subject'], $tokens);
    $emailReceipt->body($this->_templateEmailReceipt['message'], $tokens);
    $emailReceipt->send();
    $this->_CustomReceiptEmailLogKey = $this->_cEmailLog->logSend(
      $emailReceipt->getTo(),
      $emailReceipt->getFrom(),
      $emailReceipt->getSubject(),
      $emailReceipt->getBody(),
      $this->_fData['key'],
      $customer['key'],
      $order['key'],
      0,
      $this->_cTracking->sessionKey()
    );
    $this->_CustomReceiptEmailLogKey;
    $_SESSION[$this->_sPrefix][$this->_cPrefix]['CustomRecieptEmailLogKey'] = $this->_CustomReceiptEmailLogKey;
    return $this->_CustomReceiptEmailLogKey;
  }

  public function customerSupportSent()
  { return ((isset($_SESSION[$this->_sPrefix][$this->_cPrefix]['ContactUsEmailLogKey']))?true:false); }

  public function customerSupportLogKey()
  { return $_SESSION[$this->_sPrefix][$this->_cPrefix]['ContactUsEmailLogKey']; }

  public function sendSupportEmail($to, $tokens, $customer = 0, $order = 0)
  {
    if(is_null($this->_templateSupportCustomer)){
      $this->_templateSupportCustomer = $this->_mEmailTemplates->get($this->_fData['support-email-template-customer']);
    }
    if(is_null($this->_templateSupportCompany)){
      $this->_templateSupportCompany  = $this->_mEmailTemplates->get($this->_fData['support-email-template-company']);
    }
    if(is_null($this->_tSupportEmail)){
      $this->_tSupportEmail = $this->_cServices->get($this->_fData['support-email']);
    }
    $tokens['{site-name}'] = $this->_fData['tokens']['{site-name}'];
    $customerEmail  = $this->_cEmail->emailHandle($this->_tSupportEmail);
    $companyEmail   = $this->_cEmail->emailHandle($this->_tSupportEmail);
    $customerEmail->to($to);
    $customerEmail->from(array($this->_tSupportEmail->username() => $this->_tSupportEmail->name()));
    $customerEmail->subject($this->_templateSupportCustomer['subject'], $tokens);
    $customerEmail->body($this->_templateSupportCustomer['message'], $tokens);
    $customerEmail->send();
    $this->_cEmailLog->logSend(
      $customerEmail->getTo(),
      $customerEmail->getFrom(),
      $customerEmail->getSubject(),
      $customerEmail->getBody(),
      $this->_fData['key'],
      $customer,
      $order,
      0,
      $this->_cTracking->sessionKey()
    );
    $companyEmail->to($this->_tSupportEmail->username());
    $companyEmail->from(array($this->_tSupportEmail->username()=>$this->_tSupportEmail->name()));
    $companyEmail->subject($this->_templateSupportCompany['subject'], $tokens);
    $companyEmail->body($this->_templateSupportCompany['message'], $tokens);
    $companyEmail->send();
    $this->_ContactUsEmailLogKey = $this->_cEmailLog->logSend(
      $companyEmail->getTo(),
      $companyEmail->getFrom(),
      $companyEmail->getSubject(),
      $companyEmail->getBody(),
      $this->_fData['key'],
      $customer,
      $order,
      1,
      $this->_cTracking->sessionKey()
    );
    $this->_prepSession();
    $_SESSION[$this->_sPrefix][$this->_cPrefix]['ContactUsEmailLogKey'] = $this->_ContactUsEmailLogKey;
    return $this->_ContactUsEmailLogKey;
  }

  /**
   * Return a map of a the funnel path
   * @method funnelMap
   * @return array
   */
  public function funnelMap()
  { return $this->_funnelMap; }


  /** ADMINISTRATION METHODS FOR FUNNEL SYSTEM */

  /**
   * Build a funnel select form element
   * @method buildFunnelSelect
   * @param      $selected
   * @param null $documentId
   * @return string
   */
  public function buildFunnelSelect($selected, $documentId=null)
  {
    /** @var string $id */
    $id = "funnelKey";
    /** Check if a different id was specified */
    if(!is_null($documentId)){
      /** @var string $id set id to the specified id */
      $id = $documentId;
    }
    /** @var array $funnels */
    $funnels = $this->_mFunnel->getFunnelKeyAndName();
    /** @var string $select Build the select box into the select variable */
    $select = "<select id=\"{$id}\" name=\"{$id}\">";
    /** @var array $funnel */
    foreach($funnels as $funnel){
      $isSelected = (($selected==$funnel['key'])?' selected=selected':'');
      /** add the option to the select */
      $select .= "<option value=\"{$funnel['key']}\"{$isSelected}>{$funnel['name']}</option>";
    }
    /** return the select box */
    return "$select</select>";
  }

  public function registerToken( $token, $value )
  { $this->_fData['tokens'][$token] = $value; }

  /****************************************************/
  /*                   PAGE GETTERS                   */
  /****************************************************/

  /**
   * Get and return the configured page using its name
   * @method getRequestedPage
   * @param string $name
   * @return mixed
   */
  public function getRequestedPage($name)
  {
    $this->_fpData = $this->_mFunnelPage->getByName(strtolower($name['event']), $this->_fData['key']);
    return $this->_configurePage();
  }

  /**
   * Get and return the configured page using its type
   * @method getPageByType
   * @param integer $type
   * @return mixed
   */
  public function getPageByType($type)
  {
    $this->_fpData = $this->_mFunnelPage->getPageByType( $this->_fData['key'], $type );
    return $this->_configurePage();
  }

  /**
   * Get and return the configured entry page
   * @method getEntryPage
   * @return mixed
   */
  public function getEntryPage()
  {
    $this->_fpData = $this->_mFunnelPage->getPageByType($this->_fData['key'], self::vsl);
    return $this->_configurePage();
  }

  /**
   * Get and return the configured not found page
   * @method getNotFoundPage
   * @return mixed
   */
  public function getNotFoundPage()
  {
    $this->_fpData = $this->_mFunnelPage->getPageByType($this->_fData['key'], self::notFound);
    return $this->_configurePage();
  }

  /**
   * Get and return the configured error page
   * @method getErrorPage
   * @return mixed
   */
  public function getErrorPage()
  {
    $this->_fpData = $this->_mFunnelPage->getPageByType($this->_fData['key'], self::error);
    return $this->_configurePage();
  }

  /****************************************************/
  /*               PAGE ATTRIBUTE GETTERS             */
  /****************************************************/

  /**
   * Return the name of the page
   * @method pageName
   * @param $key
   * @return mixed
   */
  public function getPageName($key)
  { return $this->_funnelPageNames[$key]; }

  /**
   * Return the tokens for this page
   * @method getTokens
   * @return mixed
   */
  public function getTokens()
  { return $this->_fData['tokens']; }

  /**
   * Return the configuration
   * @method getConfiguration
   * @return mixed
   */
  public function getConfiguration()
  { return $this->_fpData['configuration']; }

  /**
   * Return content for this page
   * @method getContent
   * @return mixed
   */
  public function getContent()
  { return $this->_fpData['content']; }

  /**
   * Return the header content
   * @method getHeader
   * @return mixed
   */
  public function getHeader()
  { return $this->_fpData['header-content']; }

  /**
   * Return the footer content
   * @method getFooter
   * @return mixed
   */
  public function getFooter()
  { return $this->_fData['footer']; }

  /**
   * Return the page title
   * @method getTitle
   * @return mixed
   */
  public function getTitle()
  {
    try {
      return $this->_fpData['title'];
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return $this->_fpData['content']['title'];
    }

  }

  /**
   * Return asset location
   * @method getAssetLocation
   * @return mixed
   */
  public function getAssetDirectory()
  { return $this->_fData['asset-directory']; }

  /**
   * Return google analytics
   * @method getGoogleAnalytics
   * @return mixed
   */
  public function getGoogleAnalytics()
  { return $this->_fData['google-analytics']; }

  /**
   * Return fresh desk
   * @method getFreshDesk
   * @return mixed
   */
  public function getFreshDesk()
  { return $this->_fData['fresh-desk']; }

  /**
   * Return the mode key this funnel is in
   * @method mode
   * @return mixed
   */
  public function getMode()
  { return $this->_fData['mode']; }

  /**
   * Return the page type key
   * @method pageType
   * @return mixed
   */
  public function getPageType()
  { return $this->_fpData['page-type-key']; }
}
