<?php
/**
 * TokenCollection
 *
 * @package Framework\Advent\Abstracts
 * @version 1.0.0
 */
namespace Framework\Advent\Abstracts;

/**
 * TokenCollection
 *
 * PHP Object to generage style sheets
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT."Advent".DSEP."Interfaces".DSEP."TokenCollection.php" );
class TokenCollection implements \Framework\Advent\Interfaces\TokenCollection
{

  /**
   * The collection container
   * @var []  $_elements
   * @access  private
   */
  private $_elements;

  /**
   * Initializes a new sheet collection
   * @method  __construct
   * @param   array $elements
   * @return  StyleSheet
   * @access  public
   */
  public function __construct( array $elements = array())
  {
    $this->_elements = $elements;
    return $this;
  }

  /**
   * __destruct
   * @method  __destruct
   * @access  public
   */
  public function __destruct(){
    unset($this->_elements);
  }

  /**
   * Generate all of the Style sheets and add them to the document
   * @method  generate
   * @param   string   $page
   * @return  void
   * @access  public
   */
  public function processTokens($page){
    $buffer = $page->getBuffer();
    array_map(function($token, $value) use (&$buffer){
      $bufer = str_replace("%{$token}%", $value, $buffer);
    }, array_keys($this->_elements), $this->_elements);
    $page->setBuffer( $buffer );
  }

  /**
   * Adds/sets an element in the collection at the index / with the specified key.
   * When the collection is a Map this is like put(key,value)/add(key,value).
   * When the collection is a List this is like add(position,value).
   * @method  set
   * @param   mixed   $key
   * @param   mixed   $value
   * @access  public
   * @uses    add()
   */
  public function set($token, $value)
  { $this->_elements[$key] = $value; }

  /**
   * Adds an element to the collection.
   * @method  add
   * @param   mixed   $value
   * @return  boolean         Always TRUE.
   * @access  public
   */
  public function add($token, $value)
  { $this->_elements[$token] = $value; }

  /**
   * Checks for the existance of a style sheet returns the key or false
   * @method  exists
   * @param   string $script  The sheet we are looking for
   * @return  mixed           FALSE or the key if it exists
   * @access  public
   */
  public function exists($token)
  { return ((!isset($this->_elements[$token]))?false:$token); }

  /**
  * Gets the element with the given key/index.
  * @method   get
  * @param    mixed   $key  The key.
  * @return   mixed         The element or NULL, if no element exists for the given key.
  * @access   public
  */
  public function get($token)
  { return ((!isset($this->_elements[$token]))?false:$this->_elements[$token]); }

  /**
   * ArrayAccess implementation of offsetExists()
   * @method  offsetExists
   * @param   mixed         $offset
   * @return  boolean
   * @access  public
   * @uses    containsKey()
   */
  public function offsetExists($offset)
  { return $this->containsKey($offset); }

  /**
   * Gets all keys/indexes of the collection elements.
   * @method  getKeys
   * @return  array
   * @access  public
   */
  public function getKeys()
  { return array_keys($this->_elements); }

  /**
   * ArrayAccess implementation of offsetGet()
   * @method  offsetGet
   * @param   mixed         $offset
   * @access  public
   * @uses    get()
   */
  public function offsetGet($offset)
  { return $this->get($offset); }

  /**
   * ArrayAccess implementation of offsetGet()
   * @method  offsetSet
   * @param   mixed   $key
   * @param   mixed   $value
   * @return  array
   * @access  public
   * @uses    add()
   * @uses    set()
   */
  public function offsetSet($offset, $value)
  {
    if (!isset($offset)){
      return $this->add($value);
    }
    return $this->set($offset, $value);
  }

  /**
   * ArrayAccess implementation of offsetUnset()
   * @method  offsetUnset
   * @param   mixed   $offset
   * @return  boolean
   * @access  public
   * @uses    remove()
   */
  public function offsetUnset($offset)
  { return $this->remove($offset); }
  
  /**
   * Returns the number of elements in the collection. Implementation of the Countable interface.
   * @method  count
   * @return  integer   The number of elements in the collection.
   * @access  public
   */
  public function count()
  { return count($this->_elements); }

  /**
  * Gets an iterator for iterating over the elements in the collection.
  * @method   getIterator
  * @return   ArrayIterator
  * @access   public
  */
  public function getIterator()
  { return new ArrayIterator($this->_elements); }
}
