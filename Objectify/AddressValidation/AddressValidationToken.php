<?php

namespace Framework\Objectify;

\Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."Service".DSEP."ServiceToken.php");
class AddressValidationToken extends ServiceToken
{
  protected $_address;
  protected $_authenticationID;
  protected $_authenticationToken;
  protected $_paramaters = [
    'address'             => 'Server Address',
    'authentication-id'   => 'Service Username',
    'authentication-token'=> 'Service Password'
  ];
  public function __construct( $key, $name, $typeKey, $typeName, array $arguments = array() )
  {
    if( isset($arguments['address']) ){
      $this->_address = $arguments['address'];
    }
    if( isset($arguments['authentication-id']) ){
      $this->_authenticationID = $arguments['authentication-id'];
    }
    if( isset($arguments['authentication-token']) ){
      $this->_authenticationToken = $arguments['authentication-token'];
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

  public function authenticationID( $authenticationID = null )
  {
    if( !is_null( $authenticationID ) ){
      $this->_authenticationID = $authenticationID;
    }
    return $this->_authenticationID;
  }

  public function authenticationToken( $authenticationToken = null )
  {
    if( !is_null( $authenticationToken ) ){
      $this->_authenticationToken = $authenticationToken;
    }
    return $this->_authenticationToken;
  }

  public function __destruct()
  {
    unset( $this->_address );
    unset( $this->_authenticationID );
    unset( $this->_authenticationToken );
    return parent::__destruct();
  }
}
