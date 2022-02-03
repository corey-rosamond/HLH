<?php

namespace App\Event;

class AjaxPost
{
  private $_system;
  private $_cSearch;


  public function __construct( $system )
  {
    $this->_system = $system;
    $this->_cFunnel = \Framework\Core\Receptionist::controller("Funnel", true);
    $this->_cSearch = \Framework\Core\Receptionist::controller("Search", true);

    return $this;
  }

  public function run()
  {
    if(!isset($_POST['action'])){
      echo json_encode(["result"=>false,"message"=>"Event action not specified!"]);
      exit();
    }
    switch($_POST['action']){
      case 'ContactUsSend':           return $this->_sendContactUs($_POST);     break;
      case 'ContactUsCustomerSearch': return $this->_findCustomer($_POST);      break;
      case 'ValidateAddress':         return $this->_validateAddress($_POST);   break;
    }
  }


  private function _sendContactUs($data)
  {
    $logKey = $this->_cFunnel->sendSupportEmail(
      $data['email'],
      ["{full-name}"=>$data['name'],"{email-address}"=>$data['email'],"{contact-reason}"=>$data['subject'],"{message}"=>$data['message']],
      ((isset($data['customer']))?$data['customer']:0),
      ((isset($data['order']))?$data['order']:0)
    );
    unset($data['action']);
    $logKey = sprintf('#%08d',$logKey);
    echo json_encode([
      "result"=>true,
      "title"=>"Thank you for contacting us!",
      "content"=>"Your contact reference number is {$logKey} please keep this for your records!"
    ]);
    return true;
  }

  private function _emailLookup($email)
  { return $this->_cSearch->emailLookup($email); }

  private function _customerOrders($customerKey)
  { return $this->_cSearch->customerOrders($customerKey); }

  private function _findCustomer($data)
  {
    $customer = $this->_emailLookup($data['email']);
    if($customer){
      echo json_encode(["result"=>true,"message"=>"Found the customer","customer-key"=>$customer['key']]);
      return true;
      //return $this->_returnOrders($customer);
    }
    echo json_encode(["result"=>true,"message"=>"No customer found","customer-key"=>0]);
    return true;
  }

  private function _returnOrders($customer)
  {
    /** We are finding orders easly but this is taking so long so it will need to be completed once the rest is done */
    /** For now we will just relate the order to the customer */
    $orders = $this->_customerOrders($customer['key']);
  }

  private function _validateAddress($data)
  {
    $result = $this->_cFunnel->validateAddress(
      $data['streetOne'],
      $data['streetTwo'],
      $data['city'],
      $data['state'],
      $data['zip']
    );
    echo json_encode($result);
  }
}
