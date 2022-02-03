<?php

namespace Framework\Commander;

class EmailLog extends \Framework\Support\Abstracts\Singleton
{

  private $_mEmailLog;


  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_mEmailLog = \Framework\Core\Receptionist::modal("EmailLog", "Holylandhealth", true);
    return $instance;
  }

  public function logSend($to, $from, $subject, $message, $funnelKey=0, $customerKey=0, $orderKey=0, $company=0, $sessionKey=0)
  {
    if(is_array($to)){
      $to = json_encode($to);
    }
    if(is_array($from)){
      $from = json_encode($from);
    }
    return $this->_mEmailLog->logSend($to, $from, $subject, $message, $funnelKey, $customerKey, $orderKey, $company, $sessionKey);
  }
}
