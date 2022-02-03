<?php
/**
 * StyleObject
 *
 * @package Framework\Dominator\Support
 * @version 1.0.0
 */
namespace Framework\Dominator\Support;

/**
 * StyelObject
 *
 * Every DocumentObject
 */
class StyleObject{

  /*********************************
  *        OBJECT VARIABLES        *
  /********************************/

  /**
   * Collection of framework specific
   * attributes for the object it belongs to
   * @var     array   $this->styles   [style name => style value]
   * @access  private
   */
  private $styles = [];

  /**
   * Count of the attributes registered to this object
   * @var     integer   $this->count
   * @access  private
   */
  private $count = 0;

  /*********************************
  *        STANDARD METHODS        *
  /********************************/

  /**
   * __construct
   * @method  __construct
   * @param   false | array   $styles   optional provide styles to start with
   * @return  StyleObject
   * @access  public
   */
  public function __construct( $styles = false ){
    /** Check if we have styles */
    if( $styles ){
      /** Styles were provided set styles and count */
      $this->styles = $styles;
      $this->count = count( $styles );
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
    unset( $this->styles );
    unset( $this->count );
  }

  /*********************************
  *   DEFAULT DOM CONFIGURATIONS   *
  /********************************/

  /**
   * Add Style
   * @method  add
   * @param   string    $style   Style to add
   * @param   string    $value   Value
   * @return  boolean
   * @access  public
   */
  public function add( $style, $value ){
    /** Check if this is a new style */
    if( !isset($this->styles[ $style ]) ){
      /** Not set add to the count */
      $this->count++;
    }
    /** Set the style */
    $this->styles[ $style ] = $value;
    /** Return true */
    return true;
  }

  /**
   * Remove a style
   * @method  remove
   * @param   string   $style   Style to remove
   * @return  boolean
   * @access  public
   */
  public function remove( $style ){
    /** Check  if the style is set */
    if( isset($this->styles[ $style ]) ){
      /** It exist subract from the count */
      $this->count--;
    }
    /** Unset the attribute */
    unset($this->styles[ $style ]);
    /** return true */
    return true;
  }

  /**
   * Determine if the object has a specific style
   * @method  has
   * @param   string  $style  Style to look for
   * @return  boolean
   * @access  public
   */
  public function has( $style ){
    return isset( $this->styles[ $style ] );
  }

  /**
  * Gets the element with the given key/index.
  * @method   get
  * @param    mixed   $key  The key.
  * @return   mixed         The element or NULL, if no element exists for the given key.
  * @access   public
  */
  public function get($key)
  {
    if(isset($this->_elements[$key])){
      return $this->_elements[$key];
    }
    return null;
  }

  /**
   * Get the value of a style
   * @method  value
   * @param   string          $style      Style to look for
   * @return  false|string                Value or false
   * @access  public
   */
  public function value( $style ){
    /** Check if it has been set */
    if( !$this->has( $style ) ){
      /** Not set return false */
      return false;
    }
    /** Is set return value */
    return $this->styles[ $style ];
  }

  /*********************************
  *   			OUTPUT METHODS         *
  /********************************/

  /**
   * Render the style attribute
   * @method  render
   * @return  string   Attributes as string
   * @access  public
   */
  public function render(){
    /** Check for styles */
    if( $this->count == 0 ){
      /** No styles return empty string */
      return "";
    }
    /** Build the stlye string */
    return implode(' ',
      array_map(
        function( $style, $value ){
          return sprintf( "%s:%s;", $style, $value);
        },
        $this->styles, array_keys( $this->styles )
      )
    );
  }
}
