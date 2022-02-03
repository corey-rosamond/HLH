<?php

namespace Framework\Objectify;

\Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."Service".DSEP."ServiceToken.php");
class EmailToken extends ServiceToken
{
  protected $_address;
  protected $_port;
  protected $_username;
  protected $_password;
  protected $_paramaters = ['address'=>'Server Address','port'=>'Server Port','username'=>'Service Username','password'=>'Service Password'];
  public function __construct( $key, $name, $typeKey, $typeName, array $arguments = array() )
  {
    if( isset($arguments['address']) ){
      $this->_address = $arguments['address'];
    }
    if( isset($arguments['port']) ){
      $this->_port = $arguments['port'];
    }
    if( isset($arguments['username']) ){
      $this->_username = $arguments['username'];
    }
    if( isset($arguments['password']) ){
      $this->_password = $arguments['password'];
    }
    return parent::__construct( $key, $name, $typeKey, $typeName, $arguments );
  }

  public function address( $address = null )
  {
    if( !is_null( $address ) ){
      $this->_address = $address;
    }
    return $this->_address;
  }

  public function port( $port = null )
  {
    if( !is_null( $port ) ){
      $this->_port = $port;
    }
    return $this->_port;
  }

  public function username( $username = null )
  {
    if( !is_null( $username ) ){
      $this->_username = $username;
    }
    return $this->_username;
  }

  public function password( $password = null )
  {
    if( !is_null( $password ) ){
      $this->_password = $password;
    }
    return $this->_password;
  }

  public function __destruct()
  {
    unset( $this->_address );
    unset( $this->_port );
    unset( $this->_username );
    unset( $this->_password );
    return parent::__destruct();
  }
}
