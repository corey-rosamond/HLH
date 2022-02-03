<?php

namespace Framework\Dominator;

class Scripts implements \ArrayAccess
{
  /**
   * The constant for app resources
   * @var     int
   * @access  private
   */
  const appResource         = 0;

  /**
   * The constant for Direct Resources
   * @var     int
   * @access  private
   */
  const directResource      = 1;

  /**
   * The constant for framework resources
   * @var     int
   * @access  private
   */
  const frameworkResource   = 2;

  /**
   * The url where this app has its script files
   * @var     string
   * @access  private
   */
  private $appResourceLocation;

  /**
   * The location to get framework script files
   * @var     string
   * @access  private
   */
  private $frameworkResourceLocation = 'Scriptaculious/';

  /**
   * An array of scripts we were asked to include
   * @var     array
   * @access  private
   */
  private $scripts = [];

  /**
   * The buffer this object writes to before any return
   * @var     string
   * @access  private
   */
  private $buffer  = null;

  /**
   * Construct the scripts collection object and set the appResourceLocation
   * @method  __construct
   * @param   string          $appResourceLocation  The location where this site stores script files
   * @return  this                                  Return the scritps object for method chaining
   * @access  public
   */
  public function __construct( $appResourceLocation )
  {
    $this->appResourceLocation = $appResourceLocation;
    return $this;
  }

  /**
   * Write our changes to the document buffer
   * @method  write
   * @return  void
   * @access  public
   */
  public function write()
  {
    /** Check if we have scripts to include **/
    if (empty($this->scripts)) {
      /** No scripts return true to end the method */
      return true;
    }
    /** Start looping through the scripts */
    foreach ( $this->scripts as $script ) {
      $this->buffer .= $this->makeTag( $script );
    }
    /** We are done write to the document buffer and return */
    $this->writeBuffer();
    /** End the method */
    return true;
  }

  /**
   * Make and return the html tag
   * @method makeTag
   * @param  array  $script The script data
   * @return string         The html string for this tag
   * @access private
   */
  private function makeTag( $script ){
    /** The source for this script tag */
    $src = null;
    /** Add the base location to the src attribute */
    switch( $script['type'] ){
      case self::frameworkResource:
        /** Add the frameworkResourceLocation to the src variable */
        $src = $this->frameworkResourceLocation;
      break;
      case self::appResource:
        /** Add the appResourceLocation to the src variable */
        $src = $this->appResourceLocation;
      break;
    }
    /** Make the tag and return it */
    return ( new \Framework\Dominator\Objects\SCRIPT( $src.$script['location'] ) )->html();
  }

  /**
   * Write the buffer to the document and return to end the method
   * @method  writeBuffer
   * @return  void
   * @access  private
   */
  private function writeBuffer(){
    Document::getInstance()->write( $this->buffer );
    $this->buffer = null;
    return true;
  }

  /**
   * Check if the offset Exists
   * @method  offsetExists
   * @param   int          $offset The Collection offset to check
   * @return  boolean              Did it exist
   * @access  public
   */
  public function offsetExists($offset)
  { return isset($this->scripts[$offset]); }

  /**
   * Get part of the collection from the offset index
   * @method  offsetGet
   * @param   int          $offset The Collection offset we want to get
   * @return  array                The row that was requested
   * @access  public
   */
  public function offsetGet($offset)
  { return ((isset($this->scripts[$offset])) ? $this->scripts[$offset] : null); }

  /**
   * Set the value of part of the collection using its offset or false will cause a basic add
   * @method  offsetSet
   * @param   false|int     $offset This can be false or an interger value of the offset you want to set
   * @param   string        $value  The value we want to set this row to
   * @return  boolean               Did we succeed
   * @access  public
   */
  public function offsetSet( $offset = false, $value )
  { return (($offset) ? $this->scripts[$offset] = $value : array_push($this->scripts, $value)); }

  /**
   * Unset part of the collection by its offset index
   * @method offsetUnset
   * @param   int         $offset Offset index of the row we want to remove
   * @return  this                Return this to enable method chaining
   * @access  public
   */
  public function offsetUnset($offset)
  {
    unset($this->scripts[$offset]);
    return $this;
  }
}
