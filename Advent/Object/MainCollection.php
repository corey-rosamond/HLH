<?php
/**
 * MainCollection
 *
 * @package Framework\Advent\Object
 * @version 1.0.0
 */
namespace Framework\Advent\Object;

/**
 * MainCollection
 *
 * PHP Object to generage style sheets
 */

class MainCollection implements \IteratorAggregate, \ArrayAccess, \Countable
{
  /////////////////////////////////////////////////////
  /**               Collection Variables             */
  /////////////////////////////////////////////////////

  /**
   * The cursor so what object we are looking at currently
   * We default it to -1 to show that currently the Collection
   * Does not exist
   * @var       integer      $_cursor
   * @access    protected
   */
  protected $_cursor;

  /**
   * This is where the Collection itself is stored all
   * Colleciton data lives here
   * @var     array     $_elements
   * @access  protected
   */
  protected $_elements;

  /**
   * This is the current count of elements in the collection
   * @var     integer   $_count
   * @access  protected
   */
  protected $_count = 0;

  /**
   * Callback function for useability
   * @var     mixed     $_callback
   * @access  protected
   */
  protected $_callback;

  /**
   * These are the variable definations that were passed to the constructor
   * We are using them to name the values they set in the collection in order
   * @var     mixed       $_variables
   * @access  protected
   */
  protected $_variables = [];

  /**
   * This is here so that we can pass variables into functions
   * @var     mixed       $options
   * @access  protected
   */
  protected $_options = [];

  /////////////////////////////////////////////////////
  /**  PHP _MAGIC Functions & __construc /__destruct */
  /////////////////////////////////////////////////////

  /**
   * Construct the MainCollection
   * @method  __construct
   * @param   array       $elements
   * @param   mixed       $callback
   * @access  public
   */
  public function __construct( $elements = null, $callback = null )
  {
    $this->_elements  = [];
    $this->_callback  = $callback;
    $this->_variables = func_get_args();
  }

  /**
   * Make sure we take the MainCollecion object down correct
   * it i s very large and frequently used
   * @method  __destruct
   * @access  public
   */
  public function __destruct()
  {
    unset( $this->_elements );
    unset( $this->_callback );
    unset( $this->_variables );
    unset( $this->_count );
    unset( $this->_cursor );
    unset( $this->_options );
  }

  public function __get( $token ){
    return $this->get( $token );
  }

  /////////////////////////////////////////////////////
  /** Closures Methods For Array Sort & Manipulation */
  /////////////////////////////////////////////////////

  /**
   * Gets the iterator iteslf so we can loop
   * @method  getIterator
   * @return  mixed
   * @access  public
   */
  public function getIterator( )
  { 
    return new ArrayIterator($this); 
  }

  /**
   * Process the predicate for each iterator value
   * @method  each
   * @param   Closure $predicate
   * @return  void
   * @access  public
   */
  public function each(\Closure $predicate)
  { 
    array_map( 
      $predicate, 
      array_keys( $this->_elements ), 
      $this->_elements 
    ); 
  }

  /**
   * Runs your predicate against all objects in the collection
   * Then returns a copy with the results
   * @method  map
   * @param   Closure     $function
   * @return  Collection
   * @access  public
   */
  public function map( \Closure $function )
  { 
    return new static(
      array_map( 
        $function, 
        $this->_elements 
      )
    );
  }

  /**
   * Runs the specified Predicate for all elements
   * @method   forAll
   * @param    Closure   $predicate  The predicate.
   * @return   boolean               TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
   * @access   public
   */
  public function forAll( \Closure $predicate )
  {
    foreach ( $this->_elements as $key => $element ){
      if( !$predicate( $key, $element ) ){ 
        return false; 
      }
    }
    return true;
  }

  /**
   * Returns all elements in the collection that match the filter
   * @method   filter
   * @param    Closure     $predicate  The predicate used for filtering.
   * @return   Collection              A collection with the results of the filter operation.
   * @access   public
   */
  public function filter( \Closure $predicate )
  { 
    return new
    static( 
      array_filter(
        $this->_elements,
        $predicate
      )
    );
  }

  /////////////////////////////////////////////////////
  /**             Array Transformations              */
  /////////////////////////////////////////////////////

  /**
   * Convert the Collection to an arry
   * @method  toArray
   * @return  array   A representation of the collection as an array
   * @access  public
   */
  public function toArray()
  { 
    return $this->_elements; 
  }



  /**
   * Adds/sets an element in the collection at the index / with the specified key.
   * When the collection is a Map this is like put(key,value)/add(key,value).
   * When the collection is a List this is like add(position,value).
   * @method  set
   * @param   mixed   $key
   * @param   mixed   $value
   * @access  public
   */
  public function set( $key, $value )
  {   
    $this->_elements[ $key ] = $value;  
  }

  /**
   * Add a row to the collection
   * @method  add
   * @param   mixed   $key
   * @param   mixed   $value
   * @uses    _argument()
   * @access  public
   */
  public function add( $key, $value = false )
  { 
    if( !$value ){
      return $this->_elements[] = $key;
    }
    return $this->_elements[ $key ] = $value; 
  }

  /**
   * Add a batch of records to the collection
   * @method  addBatch
   * @param   mixed     $batch
   * @uses    add()
   * @access  public
   */
  public function addBatch( $batch )
  {
    array_map(
      function( $key, $value ){
        $this->add($key, $value);
    }, array_keys( $batch ), $batch );
  }

  /**
   * Clear the object collection out
   * @method  clear
   * @return  mixed
   * @access  public
   */
  public function clear()
  { 
    $this->_elements = []; 
  }

  /**
   * Tries to find and remove the referenced object
   * @method  removeElement
   * @param   mixed         $element  The element to remove.
   * @return  boolean                 TRUE if this collection contained the specified element, FALSE otherwise.
   * @access  public
   */
  public function removeElement( $element )
  {
    $key = array_search(
      $element,
      $this->_elements,
      true
    );
    if( $key !== false ){
      unset( $this->_elements[ $key ] );
      return true;
    }
    return false;
  }

  /**
   * Removes an element with a specific key/index from the collection.
   * @method  public
   * @param   mixed   $key
   * @return  mixed         The removed element or NULL, if no element exists for the given key.
   * @access  public
   */
  public function remove( $key )
  { 
    if( isset( $this->_elements[ $key ] ) ){
      $removed = $this->_elements[ $key ];
      unset( $this->_elements[ $key ] );
      return $removed;
    }
    return null;
  }

  /**
   * Slice part of the collection out and return it
   * @method  slice
   * @param   integar $offset Where the slice will start
   * @param   integar $length How many records from the first
   * @return  mixed           The requiesed parts
   * @access  public
   */
  public function slice( $offset, $length = null )
  { 
    return array_slice( 
      $this->_elements, 
      $offset, 
      $length, 
      true 
    ); 
  }

  /////////////////////////////////////////////////////
  /**       Array Test: No collection changes        */
  /////////////////////////////////////////////////////

  /**
   * Test if the array is empty
   * @method  isEmpty
   * @return  boolean
   * @access  public
   */
  public function isEmpty( )
  { 
    return !$this->_elements; 
  }


  public function valid( )
  { }

  /**
   * Count the number of items in the collection
   * @method  count
   * @return  mixed
   * @access  public
   */
  public function count()
  { 
    return count( $this->_elements ); 
  }

  /**
   * Checks whether the given element is contained in the collection.
   * Only element values are compared, not keys. The comparison of two elements
   * is strict, that means not only the value but also the type must match.
   * For objects this means reference equality.
   * @method  contains
   * @param   mixed   $element
   * @return  boolean           TRUE if the given element is contained in the collection, FALSE otherwise.
   * @access  public
   */
  public function contains( $element )
  { 
    return in_array( 
      $element, 
      $this->_elements, 
      true 
    ); 
  }

  /**
   * Checks whether the collection contains a specific key/index.
   * @method  containsKey
   * @param   mixed   $key  The key to check for.
   * @return  boolean       TRUE if the given key/index exists, FALSE otherwise.
   * @access  public
   */
  public function containsKey( $key )
  { 
    return isset( 
      $this->_elements[ $key ] 
    ); 
  }

  /**
  * Tests for the existance of an element that satisfies the given predicate.
  * @method   exists
  * @param    Closure   $predicate  The predicate.
  * @return   boolean               TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
  * @access   public
  */
  public function exists( \Closure $predicate )
  {
    foreach( $this->_elements as $key => $element ){
      if( 
        predicate( 
          $key, 
          $element 
        )
      ){ 
        return true; 
      }
    }
    return false;
  }

  /**
  * Searches for a given element and, if found, returns the corresponding key/index
  * of that element. The comparison of two elements is strict, that means not
  * only the value but also the type must match. For objects this means reference equality.
  * @method   indexOf
  * @param    mixed     $element  The element to search for.
  * @return   mixed               The key/index of the element or FALSE if the element was not found.
  * @access   public
  */
  public function indexOf( $element )
  { 
    return array_search( 
      $element, 
      $this->_elements, 
      true
    ); 
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
    if( 
      isset( 
        $this->_elements[ $key ] 
      ) 
    ){
      return $this->_elements[ $key ];
    }
    return null;
  }

  /**
   * Gets all keys/indexes of the collection elements.
   * @method  getKeys
   * @return  array
   * @access  public
   */
  public function getKeys()
  { 
    return array_keys( 
      $this->_elements  
    ); 
  }

  /**
   * Gets all elements.
   * @method getValue
   * @return array
   * @access public
   */
  public function getValues()
  { 
    return array_values( 
      $this->_elements 
    ); 
  }

  /////////////////////////////////////////////////////
  /**      Iterator navigation Control Methods       */
  /////////////////////////////////////////////////////

  /**
   * Returns the current iterator record
   * @method  current
   * @return  mixed
   * @access  public
   */
  public function current()
  { 
    return current( 
      $this->_elements 
    ); 
  }

  /**
   * Get the current record key
   * @method  key
   * @return  mixed
   * @access  public
   */
  public function key()
  { 
    return key( 
      $this->_elements 
    ); 
  }

  /**
    * Get the next element
    * @method   next
    * @return   mixed
    * @access   public
    */
  public function next()
  { 
    return next( 
      $this->_elements 
    ); 
  }

  /**
   * Set the iterator to the firt posisition
   * @method  first
   * @return  mixed
   * @access  public
   */
  public function first()
  { 
    return reset( 
      $this->_elements 
    ); 
  }

  /**
   * Set the iterator to the last position
   * @method  last
   * @return  mixed
   * @access  public
   */
  public function last()
  { 
    return end( 
      $this->_elements 
    ); 
  }

  /**
   * Reset the current Iterator
   * @method  rewind
   * @return  mixed
   * @access  public
   */
  function rewind()
  {
    return reset( 
      $this->_elements
    );
  }

  /////////////////////////////////////////////////////
  /**             PHP SPL ITERATOR METHODS           */
  /////////////////////////////////////////////////////

  /** SPL FUNCTIONS */
  protected function setInfo( $information = null )
	{ 
    echo "SPL ITERATOR METHOD"; 
    return $this;
  }

  protected function getInfo()
	{ 
    echo "SPL ITERATOR METHOD"; 
    return $this;
  }

  protected function attach( $object, $information = null )
  { 
    echo "SPL ITERATOR METHOD"; 
    return $this; 
  }

  protected function detach( $object )
  { 
    echo "SPL ITERATOR METHOD"; 
    return $this;
  }

  /////////////////////////////////////////////////////
  /**          ArrayAccess implementations           */
  /////////////////////////////////////////////////////

  /**
   * Set a value with an offset
   * @method  offsetSet
   * @param   mixed
   * @param   mixed
   * @uses    add()
   * @uses    set()
   * @access  public
   */
  public function offsetSet( $key, $val )
  {
    if( !isset( $key ) ){
      return $this->add( 
        $val 
      );
    }
    return $this->set( 
      $key, $val 
    );
  }

  /**
   * Get the record using an offset
   * @method  offsetGet
   * @param   mixed
   * @uses    get()
   * @access  public
   */
  public function offsetGet( $key )
  { 
    return $this->get( 
      $key 
    ); 
  }

  /**
   * Remove this record using an offset
   * @method  offsetUnset
   * @param   mixed
   * @uses    Remove
   * @access  public
   */
  public function offsetUnset( $offset )
  { 
    return $this->remove( 
      $offset 
    ); 
  }

  /**
   * Test if the offset exists
   * @method  offsetExists
   * @param   mixed
   * @uses    containsKey()
   * @access  public
   */
  public function offsetExists($offset)
  { 
    return $this->containsKey( 
      $offset 
    ); 
  }
}
