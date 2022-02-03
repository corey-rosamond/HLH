<?php
/**
 * ArrayCollection
 *
 * @package Framework\Support\Object
 * @version 1.0.0
 */
namespace Framework\Support\Object;

/**
 * ArrayCollection
 *
 * ArrayCollection interface
 */
\Framework\_IncludeCorrect( FRAMEWORK_INTERFACE."Collection.php");
class ArrayCollection implements \Framework\Support\Interfaces\Collection
{
  /**
   * An array containing the entries of this collection.
   * @var     array
   * @access  private
   */
  private $_elements;

  /**
   * Initializes a new ArrayCollection.
   * @method  __construct
   * @param   array $elements
   * @return  void
   * @access  public
   */
  public function __construct(array $elements = array()){ $this->_elements = $elements; }

  /**
   * Gets the PHP array representation of this collection.
   * @method  toArray
   * @return  array The PHP array representation of this collection.
   * @access  public
   */
  public function toArray()
  { return $this->_elements; }

  /**
   * Sets the internal iterator to the first element in the collection and returns this element.
   * @method  first
   * @return  mixed
   * @access  public
   */
  public function first()
  { return reset($this->_elements); }

   /**
    * Sets the internal iterator to the last element in the collection and returns this element.
    * @method  last
    * @return  mixed
    * @access  public
    */
   public function last()
   { return end($this->_elements); }

   /**
    * Gets the current key/index at the current internal iterator position.
    * @method   key
    * @return   mixed
    * @access   public
    */
   public function key()
   { return key($this->_elements); }

   /**
    * Moves the internal iterator position to the next element.
    * @method   current
    * @return   mixed
    * @access   public
    */
   public function next()
   { return next($this->_elements); }

   /**
    * Gets the element of the collection at the current internal iterator position.
    * @method   current
    * @return   mixed
    * @access   public
    */
   public function current()
   { return current($this->_elements); }

   /**
    * Removes an element with a specific key/index from the collection.
    * @method  public
    * @param   mixed   $key
    * @return  mixed         The removed element or NULL, if no element exists for the given key.
    * @access  public
    */
   public function remove($key)
   {
     if(isset($this->_elements[$key])){
       $removed = $this->_elements[$key];
       unset($this->_elements[$key]);
       return $removed;
    }
    return null;
  }

  /**
   * Removes the specified element from the collection, if it is found.
   * @method  removeElement
   * @param   mixed         $element  The element to remove.
   * @return  boolean                 TRUE if this collection contained the specified element, FALSE otherwise.
   * @access  public
   */
  public function removeElement($element)
  {
    $key = array_search($element, $this->_elements, true);
    if($key !== false){
      unset($this->_elements[$key]);
      return true;
    }
    return false;
  }

  /**
   * ArrayAccess implementation of offsetExists()
   * @method  offsetExists
   * @param   mixed         $offset
   * @return  boolean
   * @access  public
   * @uses    containsKey()
   */
  public function offsetExists($offset)
  {return $this->containsKey($offset);}

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
    if(!isset($offset)){ return $this->add($value);}
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
   * Checks whether the collection contains a specific key/index.
   * @method  containsKey
   * @param   mixed   $key  The key to check for.
   * @return  boolean       TRUE if the given key/index exists, FALSE otherwise.
   * @access  public
   */
  public function containsKey($key)
  { return isset($this->_elements[$key]); }

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
  public function contains($element)
  { return in_array($element, $this->_elements, true); }

  /**
  * Tests for the existance of an element that satisfies the given predicate.
  * @method   exists
  * @param    Closure   $predicate  The predicate.
  * @return   boolean               TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
  * @access   public
  */
  public function exists(\Closure $predicate)
  {
    foreach ($this->_elements as $key => $element) {
      if($predicate($key, $element)){ return true; }
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
  public function indexOf($elfement)
  {return array_search($element, $this->_elements, true);}

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
   * Gets all keys/indexes of the collection elements.
   * @method  getKeys
   * @return  array
   * @access  public
   */
  public function getKeys()
  {return array_keys($this->_elements); }

  /**
   * Gets all elements.
   * @method getValue
   * @return array
   * @access public
   */
  public function getValues()
  {return array_values($this->_elements);}

  /**
   * Returns the number of elements in the collection. Implementation of the Countable interface.
   * @method  count
   * @return  integer   The number of elements in the collection.
   * @access  public
   */
  public function count()
  {return count($this->_elements);}

  /**
   * Adds/sets an element in the collection at the index / with the specified key.
   * When the collection is a Map this is like put(key,value)/add(key,value).
   * When the collection is a List this is like add(position,value).
   * @method  set
   * @param   mixed   $key
   * @param   mixed   $value
   * @access  public
   */
  public function set($key, $value)
  {$this->_elements[$key] = $value;}

  /**
   * Adds an element to the collection.
   * @method  add
   * @param   mixed   $value
   * @return  boolean         Always TRUE.
   * @access  public
   */
  public function add($value)
  {
    $this->_elements[] = $value;
    return true;
  }

  /**
  * Checks whether the collection is empty. Note: This is preferrable over count() == 0.
  * @method isEmpty
  * @return boolean TRUE if the collection is empty, FALSE otherwise.
  * @access public
  */
  public function isEmpty()
  {return ! $this->_elements;}

  /**
  * Gets an iterator for iterating over the elements in the collection.
  * @method   getIterator
  * @return   ArrayIterator
  * @access   public
  */
  public function getIterator()
  {return new ArrayIterator($this->_elements);}

  /**
   * Applies the given function to each element in the collection and returns
   * a new collection with the elements returned by the function.
   * @method  map
   * @param   Closure     $function
   * @return  Collection
   * @access  public
   */
  public function map(\Closure $function)
  {return new static(array_map($function, $this->_elements));}

  /**
   * Returns all the elements of this collection that satisfy the predicate p.
   * The order of the elements is preserved.
   * @method   filter
   * @param    Closure     $predicate  The predicate used for filtering.
   * @return   Collection              A collection with the results of the filter operation.
   * @access   public
   */
  public function filter(\Closure $predicate)
  {return new static(array_filter($this->_elements, $predicate));}

  /**
   * Applies the given predicate p to all elements of this collection,
   * returning true, if the predicate yields true for all elements.
   * @method   forAll
   * @param    Closure   $predicate  The predicate.
   * @return   boolean               TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
   * @access   public
   */
  public function forAll(\Closure $predicate)
  {
    foreach ($this->_elements as $key => $element){
      if (! $predicate($key, $element)){
        return false;
      }
    }
    return true;
  }

  /**
   * Process the predicate for each iterator value
   * @method  each
   * @param   Closure $predicate
   * @return  void
   * @access  public
   */
  public function each(\Closure $predicate)
  {array_map($predicate,array_keys($this->_elements),$this->_elements);}

  /**
   * Partitions this collection in two collections according to a predicate.
   * Keys are preserved in the resulting collections.
   * @method  partition
   * @param   Closure     $predicate  The predicate on which to partition.
   * @return  array                   An array with two elements. The first element contains the collection
   *                                     of elements where the predicate returned TRUE, the second element
   *                                     contains the collection of elements where the predicate returned FALSE.
   * @access  public
   */
  public function partition(\Closure $predicate)
  {
    $coll1 = $coll2 = array();
    foreach ($this->_elements as $key => $element) {
      if($predicate($key, $element)) {
        $coll1[$key] = $element;
      } else {
        $coll2[$key] = $element;
      }
    }
    return array(new static($coll1), new static($coll2));
  }

  /**
   * Returns a string representation of this object.
   * @method   __toString
   * @return   string
   * @access   public
   */
  public function __toString()
  {return __CLASS__ . '@' . spl_object_hash($this);}

  /**
   * clear
   * @method  clear
   * @return  array
   * @access  public
   */
  public function clear()
  { $this->_elements = array(); }

  /**
   * Extract a slice of $length elements starting at position $offset from the Collection.
   * If $length is null it returns all elements from $offset to the end of the Collection.
   * Keys have to be preserved by this method. Calling this method will only return the
   * selected slice and NOT change the elements contained in the collection slice is called on.
   * @method  slice
   * @param   int   $offset
   * @param   int   $length
   * @return  array
   */
  public function slice($offset, $length = null)
  { return array_slice($this->_elements, $offset, $length, true); }
}
