<?php

namespace Framework\Commander;

\Framework\_IncludeCorrect(FRAMEWORK_ROOT."vendor".DSEP."NetworkMerchant".DSEP."nmiCustomerVault.class.php");
class PaymentProcessor extends \Framework\Support\Abstracts\Singleton
{

  private $_document;
  private $_modal;

  private $_responseCodeAVS = [
    'Y' =>	'Exact match, 5-character numeric ZIP',
    'X' =>	'Exact match, 9-character numeric ZIP',
    'D' =>	'Exact match, 5-character numeric ZIP',
    'M' =>	'Exact match, 5-character numeric ZIP',
    'A' =>	'Address match only',
    'B' =>	'Address match only',
    'W' =>	'9-character numeric ZIP match only',
    'Z' =>	'5-character ZIP match only',
    'P' =>	'5-character ZIP match only',
    'L' =>	'5-character ZIP match only',
    'N' =>	'No address or ZIP match only',
    'C' =>	'No address or ZIP match only',
    'U' =>	'Address unavailable',
    'G' =>	'Non-U.S. issuer does not participate',
    'I' =>	'Non-U.S. issuer does not participate',
    'R' =>	'Issuer system unavailable',
    'E' =>	'Not a mail/phone order',
    'S' =>	'Service not supported',
    'O' =>	'AVS not available',
    'B' =>	'AVS not available'
  ];

  private $_responseCodeCVV = [
    'M' => 'CVV2/CVC2 match',
    'N' => 'CVV2/CVC2 no match',
    'P' => 'Not processed',
    'S' => 'Merchant has indicated that CVV2/CVC2 is not present on card',
    'U' => 'Issuer is not certified and/or has not provided Visa encryption keys'
  ];

  private $_responseCode = [
    '200' =>	'Transaction was declined by processor.',
    '201' =>	'Do not honor.',
    '202' =>	'Insufficient funds.',
    '203' =>	'Over limit.',
    '204' =>	'Transaction not allowed.',
    '220' =>	'Incorrect payment information.',
    '221' =>	'No such card issuer.',
    '222' =>	'No card number on file with issuer.',
    '223' =>	'Expired card.',
    '224' =>	'Invalid expiration date.',
    '225' =>	'Invalid card security code.',
    '240' =>	'Call issuer for further information.',
    '250' =>	'Pick up card.',
    '251' =>	'Lost card.',
    '252' =>	'Stolen card.',
    '253' =>	'Fraudulent card.',
    '260' =>	'Declined with further instructions available. (See response text)',
    '261' =>	'Declined-Stop all recurring payments.',
    '262' =>	'Declined-Stop this recurring program.',
    '263' =>	'Declined-Update cardholder data available.',
    '264' =>	'Declined-Retry in a few days.',
    '300' =>	'Transaction was rejected by gateway.',
    '400' =>	'Transaction error returned by processor.',
    '410' =>	'Invalid merchant configuration.',
    '411' =>	'Merchant account is inactive.',
    '420' =>	'Communication error.',
    '421' =>	'Communication error with issuer.',
    '430' =>	'Duplicate transaction at processor.',
    '440' =>	'Processor format error.',
    '441' =>	'Invalid transaction information.',
    '460' =>	'Processor feature not available.',
    '461' =>	'Unsupported card type.',
  ];


  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_mPaymentProcessor = \Framework\Core\Receptionist::modal( "PaymentProcessor", true );
    $instance->_document= \Framework\Core\Receptionist::controller( 'Document', true );
    return $instance;
  }

  private function _logReturnMessage( $message, $order, $amount = '0.00' )
  {
    return $this->_mPaymentProcessor->addLogEntry(
      $order, $message['response'], $message['responsetext'], $message['authcode'], $message['transactionid'], $message['avsresponse'],
      $message['cvvresponse'], $message['type'], $message['response_code'], $amount
    );
  }

  private function _parseResponse($message)
  {
    $errors = [];
    $code = intval(ltrim(rtrim($message['response_code'])));
    if($code=='100'||$code==100){
      return $errors;
    }
    if(isset($this->_responseCode[$message['response_code']])){
      array_push($errors,$this->_responseCode[$message['response_code']]);
    }
    //if(isset($this->_responseCodeAVS[$message['avsresponse']])){
      ///array_push($errors,$this->_responseCodeAVS[$message['avsresponse']]);
    ///}
    //if(isset($this->_responseCodeCVV[$message['cvvresponse']])){
     // array_push($errors,$this->_responseCodeCVV[$message['cvvresponse']]);
    //}
    if(sizeof($errors)>=1){
      $rtext = $message['responsetext'];
      if( strpos($rtext,'REFID') !== false ){
        $rtext = substr($rtext,0,(strpos($rtext,'REFID')-1));
      }
      array_push($errors, $rtext);
    }
    return $errors;
  }

  public function updateCustomer( $data, $token )
  {
    $options  = ['nmi_url'=>$token->address(),'nmi_user'=>$token->username(),'nmi_password'=>$token->password()];
    $vault = new \nmiCustomerVault($options);
    $vault->setCustomerVaultId( $data['customer']);
    $vault->setCcNumber(        $data['cc-data']['card-number']);
    $vault->setCcExp(           sprintf('%02d', $data['cc-data']['expiration-month'])."/".$data['cc-data']['expiration-year']);
    $vault->setCvv(             $data['cc-data']['cvv']);
    $vault->setFirstName(       $data['first-name']);
    $vault->setLastName(        $data['last-name']);
    $vault->setAddress1(        $data['address-one']);
    $vault->setAddress2(        $data['address-two']);
    $vault->setCity(            $data['city']);
    $vault->setState(           $data['state']);
    $vault->setZip(             $data['zip']);
    $vault->setCountry('US');
    $vault->setPhone(           $data['phone-number']);
    $vault->setEmail(           $data['email-address']);
    $vault->update();
    $result = $vault->executeChild();
    $this->_logReturnMessage($result, $data['order']);
    $errors = $this->_parseResponse($result);
    if(sizeof($errors)>=1){
      return ['result'=>false,'messages'=>$errors];
    }
    return ['result'=>true,'messages'=>[]];
  }

  public function createCustomer( $data, $token )
  {
    $options  = ['nmi_url'=>$token->address(),'nmi_user'=>$token->username(),'nmi_password'=>$token->password()];
    $vault    = new \nmiCustomerVault($options);
    $vault->setCustomerVaultId( $data['customer']);
    $vault->setCcNumber(        $data['cc-data']['card-number']);
    $vault->setCcExp(           sprintf('%02d', $data['cc-data']['expiration-month'])."/".$data['cc-data']['expiration-year']);
    $vault->setCvv(             $data['cc-data']['cvv']);
    $vault->setFirstName(       $data['first-name']);
    $vault->setLastName(        $data['last-name']);
    $vault->setAddress1(        $data['address-one']);
    $vault->setAddress2(        $data['address-two']);
    $vault->setCity(            $data['city']);
    $vault->setState(           $data['state']);
    $vault->setZip(             $data['zip']);
    $vault->setCountry('US');
    $vault->setPhone(           $data['phone-number']);
    $vault->setEmail(           $data['email-address']);
    $vault->add();
    $result = $vault->executeChild();
    $this->_logReturnMessage($result, $data['order']);
    $errors = $this->_parseResponse($result);
    if(sizeof($errors)>=1){
      return ['result'=>false,'messages'=>$errors];
    }
    return ['result'=>true,'messages'=>[]];
  }


  public function charge($data, $token)
  {
    $options = ['nmi_url'=>$token->address(),'nmi_user'=>$token->username(),'nmi_password'=>$token->password()];
    $vault = new \nmiCustomerVault($options);
    $vault->setCustomerVaultId( $data['customer'] );
    $vault->setOrderId( $data['order'] );
    $vault->setOrderDescription( $data['product'] );
    $vault->charge(number_format( $data['order-total'], 2));
    $result = $vault->executeChild();
    $this->_logReturnMessage($result, $data['order'], number_format( $data['order-total'], 2));
    $errors = $this->_parseResponse($result);
    if(sizeof($errors)>=1){
      return ['result'=>false,'messages'=>$errors];
    }
    return ['result'=>true,'messages'=>[]];
  }
}


/*
public function orderCount( $key )
{
  return $this->_modal->getOrderCount( $key );
}

public function orderLogDatatable( $key )
{
  return $this->_document->portlet(
    "box",
    "green-meadow",
    'icon-credit-card',
    'Payment Processor Log',
    '',
    $this->_document->datatable(
      'payment-processor-log',
      [
        ['label'=>'Log #',          'style'=>'text-align:center;',  'order'=>true],
        ['label'=>'Order #',        'style'=>'text-align:center;',  'order'=>true],
        ['label'=>'Code',           'style'=>'text-align:center;',  'order'=>true],
        ['label'=>'Response',       'style'=>'',                    'order'=>true],
        ['label'=>'Auth #',         'style'=>'text-align:center;',  'order'=>true],
        ['label'=>'Transaction #',  'style'=>'',                    'order'=>true],
        ['label'=>'AVS Response',   'style'=>'',                    'order'=>true],
        ['label'=>'CVV Response',   'style'=>'',                    'order'=>true],
        ['label'=>'Type',           'style'=>'',                    'order'=>true],
        ['label'=>'RCode',          'style'=>'text-align:center;',  'order'=>true],
        ['label'=>'Amount',         'style'=>'text-align:center;',  'order'=>true],
        ['label'=>'Timestamp',      'style'=>'',                    'order'=>true]
      ],
      $this->_orderLogFormatted( $key )
    ), array(), array(), true
  );
}

private function _orderLogFormatted($key)
{
  $log = $this->_modal->getOrder( $key );
  foreach( $log as &$entry ){
    $entry['order-key'] = sprintf('#%08d', $entry['order-key'] );
    $entry['authcode'] = "#{$entry['authcode']}";
    $entry['amount'] = "$".number_format( $entry['amount'], 2 );
    $entry['timestamp'] = date( "l jS \of F Y h:i:s A", strtotime( $entry['timestamp'] ) );
  }
  return $log;
}
 */
