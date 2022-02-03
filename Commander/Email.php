<?php

namespace Framework\Commander;

\Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."Email".DSEP."EmailMessage.php");
class Email extends \Framework\Support\Abstracts\Singleton
{

  public static function construct()
  {
    $instance = self::getInstance();
    return $instance;
  }

  public function _transport($token)
  {
    $transport = \Swift_SmtpTransport::newInstance( $token->address(), $token->port(), "ssl" );
    $transport->setUsername( $token->username() );
    $transport->setPassword( $token->password() );
    return $transport;
  }

  public function _mailer( $transport )
  { return \Swift_Mailer::newInstance( $transport ); }

  public function _message()
  { return \Swift_Message::newInstance(); }

  public function emailHandle($token)
  {
    $transport = $this->_transport($token);
    return new \Framework\Objectify\EmailMessage( $transport, $this->_mailer( $transport ), $this->_message() );
  }
  
}
