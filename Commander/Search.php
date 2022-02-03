<?php

namespace Framework\Commander;

class Search extends \Framework\Support\Abstracts\Singleton
{
  private $_cCustomer;


  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_cCustomer = \Framework\Core\Receptionist::controller("Customer", true);


    return $instance;
  }



  public function emailLookup($email)
  { return $this->_cCustomer->emailLookup($email); }

  public function customerOrders($customerKey)
  { return $this->_cCustomer->customerOrders($customerKey);}
}
