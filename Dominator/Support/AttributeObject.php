<?php
/**
 * AttributeObject
 *
 * @package Framework\Dominator\Support
 * @version 1.0.0
 */
namespace Framework\Dominator\Support;

/**
 * AttributeObject
 *
 * All dom objects have attributes you can set
 * this object means we do not have to worry about
 * keeping track of them
 */
class AttributeObject{

  /*********************************
  *        OBJECT VARIABLES        *
  /********************************/

  /**
   * Collection of attributes for a DomObject
   * @var     array     $this->attributes [attribute name => attribute value]
   * @access  private
   */
  private $attributes = [];

  /**
   * Count of the attributes registered to this object
   * @var    integer $this->count
   * @access private
   */
  private $count = 0;

  /*********************************
  *   	   STANDARD METHODS        *
  /********************************/

  /**
   * Build our attribute collection object and return it
   * __construct
   * @method  __construct
   * @param   false|array   $attributes
   * @return  $this
   * @access  public
   */
  public function __construct( $attributes = false ){
    /** Check we have attributes **/
    if( $attributes ){
      /** We have attributes set them and count */
      $this->attributes = $attributes;
      $this->count = count( $attributes );
    }
    /** done return this */
    return $this;
  }

  /**
   * This is a very high use object make sure we do not have
   * any memory leaks
   * @method  __destruct
   * @return  void
   * @access  public
   */
  public function __destruct(){
    unset( $this->attributes );
    unset( $this->count );
  }

  /**
   * add an attribute
   * @method  add
   * @param   string    $attribute  Attribute name to add
   * @param   string    $value      Value of the new attribute
   * @return  boolean
   * @access  public
   */
  public function add( $attribute, $value ){
    /** Check if this attribute is already set */
    if( !isset($this->attributes[ $attribute ]) ){
      /** Its new update the count */
      $this->count++;
    }
    /** Set the attribute */
    $this->attributes[ $attribute ] = $value;
    /** Feturn true */
    return true;
  }

  /**
   * Remove an attribute
   * @method  remove
   * @param   string    $attribute  Attribute to remove
   * @return  boolean
   * @access  public
   */
  public function remove( $attribute ){
    /** Check if this attribute is already set */
    if( isset($this->attributes[ $attribute ]) ){
      /** It exists update the count */
      $this->count--;
    }
    /** Unset the attribute */
    unset($this->attributes[ $attribute ]);
    /** return true */
    return true;
  }

  /**
   * Check if this attribute is set
   * @method  has
   * @param   string  $attribute    Attribute to look for
   * @return  boolean
   * @access  public
   */
  public function has( $attribute ){
    return isset( $this->attributes[ $attribute ] );
  }

  /**
   * Return false or the value of the attribute
   * @method  value
   * @param   string    $attribute    Attribute to look for
   * @return  false|string            Value or false
   * @access  public
   */
  public function value( $attribute ){
    /** check if that attribute is set */
    if( !$this->has( $attribute ) ){
      /** not set return false */
      return false;
    }
    /** return the value */
    return $this->attributes[ $attribute ];
  }

  /*********************************
  *   			OUTPUT METHODS         *
  /********************************/

  /**
   * return the string with containing the attributes
   * @method  render
   * @return  string      String containing attributes
   * @access  public
   */
  public function render(){
    /** Make sure we have attributes */
    if( $this->count == 0 ){
      /** No attributes return empty string */
      return "";
    }

    /** Return the attributes string */
    return implode(' ',
      array_map(
        function( $value, $attribute ){
          return sprintf(" %s=\"%s\"", $attribute, $value);
        },
        $this->attributes, array_keys($this->attributes)
      )
    );
  }
}
