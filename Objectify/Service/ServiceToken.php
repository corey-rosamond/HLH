<?php

namespace Framework\Objectify;


class ServiceToken
{
  protected $_key;
  protected $_typeKey;
  protected $_typeName;
  protected $_name;

  public function __construct( $key, $name, $typeKey, $typeName, array $arguments = array() )
  {
    $this->_key       = $key;
    $this->_name      = $name;
    $this->_typeKey   = $typeKey;
    $this->_typeName  = $typeName;
    return $this;
  }

  public function __toString()
  {
    $string = "<div>";
      $string .= "<h1>Service Token</h1>";
      $string .= "<strong>Name: </strong>{$this->_name}";
      $string .= "<sup>({$this->_key})</sup><br />";
      $string .= "<strong>Type: </strong>{$this->_typeName}";
      $string .= "<sup>({$this->_typeKey})</sup><br />";
      foreach( $this->_paramaters as $name => $label ){
        $value = "_{$name}";
        $string .= "<strong>{$label}: </strong>{$this->$value}<br />";
      }
    return "{$string}</html>";
  }

  public function key( $key = null )
  {
    if( !is_null( $key ) ){
      $this->_key = $key;
    }
    return $this->_key;
  }

  public function name( $name = null )
  {
    if( !is_null( $name ) ){
      $this->_name = $name;
    }
    return $this->_name;
  }

  public function typeKey( $typeKey = null )
  {
    if( !is_null( $typeKey ) ){
      $this->_typeKey = $typeKey;
    }
    return $this->_typeKey;
  }

  public function typeName( $typeName = null )
  {
    if( !is_null( $typeName ) ){
      $this->_typeName = $typeName;
    }
    return $this->_typeName;
  }

  public function __destruct()
  {
    unset( $this->_key );
    unset( $this->_typeKey );
    unset( $this->_typeName );
    unset( $this->_name );
  }
}
