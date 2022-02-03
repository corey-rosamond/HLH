<?php

namespace Framework\Objectify;


class EmailMessage
{
  private $_transport;
  private $_mailer;
  private $_message;

  public function __construct( $transport, $mailer, $message )
  {
    $this->_transport = $transport;
    $this->_mailer    = $mailer;
    $this->_message   = $message;
    return $this;
  }

  public function subject( $subject = "subject", $tokens = null )
  {
    if(!is_null($tokens)){
      array_map(function($token, $value) use (&$subject){
        $subject = str_replace($token,$value,$subject);
      }, array_keys($tokens), $tokens);
    }
    return $this->_message->setSubject( $subject );
  }

  public function body( $body = "body", $tokens = null )
  {
    if(!is_null($tokens)){
      array_map(function($token, $value) use (&$body){
        $body = str_replace($token,$value,$body);
      }, array_keys($tokens), $tokens);
    }
    return $this->_message->setBody( $body, "text/html" );
  }

  public function to( $to )
  {
    if(!is_array($to)){
      return $this->_message->setTo( array($to) );
    }
    return $this->_message->setTo( $to );
  }

  public function from( $from )
  {
    if(!is_array($from)){
      return $this->_message->setFrom( array($from=>"Undefined") );
    }
    return $this->_message->setFrom( $from );
  }

  public function cc( array $cc = array() )
  { return $this->_message->setCc( $cc ); }

  public function bcc( array $bcc = array() )
  { return $this->_message->setBcc( $from ); }

  public function getTo()
  { return $this->_message->getTo(); }

  public function getFrom()
  { return $this->_message->getFrom(); }

  public function getSubject()
  { return $this->_message->getSubject(); }

  public function getBody()
  { return $this->_message->getBody(); }


  public function send()
  {

    $this->_mailer->send( $this->_message );
  }

  public function __destruct(){
    unset( $this->_transport );
    unset( $this->_transport );
    unset( $this->_transport );
  }
}
