<?php
/**
 * Collection
 *
 * @package Framework\Support\Collection
 * @version 1.0.0
 */
namespace Framework\Support\Interfaces;

/**
 * Collection
 *
 * Collection interface
 */
interface Collection extends \Countable, \IteratorAggregate, \ArrayAccess
{

  /**
   * Append element to the end of the collection
   * @method  add
   * @param   mixed     $element
   * @return  boolean               Always true
   */
  function add($element);

  /**
   * Clear collection completely
   * @method  clear
   */
  function clear();

  /**
   * Check for element in collection
   * @method  contains
   * @param   mixed     $element
   * @return  boolean             TRUE if exists
   */
  function contains($element);

  /**
   * Checks if the collection is empty
   * @method  isEmpty
   * @return  boolean   TRUE if is empty
   */
  function isEmpty();

  /**
   * Remove the specified element from the array
   * @method  remove
   * @param   mixed     $key
   * @return  boolean         TRUE if it existed
   */
  function remove($key);

  /**
   * Remove element from the element from the array
   * @method  removeElement
   * @param   mixed         $element
   * @return  boolean                 TRUE if it existed
   */
  function removeElement($element);

  /**
   * Check if key exists
   * @method  containsKey
   * @param   string|integer  $key
   * @return  boolean                 TRUE if it exists
   */
  function containsKey($key);

  /**
   * Get the element for the key
   * @method  get
   * @param   string|integer $key
   * @return  mixed
   */
  function get($key);

  /**
   * Get all keys for this collection
   * @method  getKeys
   * @return  array     The keys for this collection
   */
  function getKeys();

  /**
   * Get all values of the collection
   * @method  getValues
   * @return  array
   */
  function getValues();

  /**
   * Sets an element in the collection
   * @method  set
   * @param   string|integer  $key
   * @param   mixed           $value
   */
  function set($key, $value);

  /**
   * Gets a native PHP array representation of the collection
   * @method toArray
   * @return array
   */
  function toArray();

  /**
   * Set the iterator to the first element in the collection
   * @method  first
   * @return  mixed   The element
   */
  function first();

  /**
   * Set the iterator to the last element in the collection
   * @method  last
   * @return  mixed   The element
   */
  function last();

  /**
   * Get the key at the current iterator position
   * @method  key
   * @return  mixed
   */
  function key();

  /**
   * Get the element at the current iterator position
   * @method  current
   * @return  mixed
   */
  function current();

  /**
   * Moves the iterator to the next element
   * @method  next
   * @return  boolean     TRUE as long as next exists
   */
  function next();

  /**
   * Tests for the existence of an element that satisfies the given predicate.
   * @method  exists
   * @param   Closure     $predicate    The predicate.
   * @return  boolean                   TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
   */
  function exists(\Closure $predicate);

  /**
   * Returns all the elements of this collection that satisfy the predicate. The order of the elements is preserved.
   * @method  filter
   * @param   Closure     $predicate    The predicate used for filtering.
   * @return  Collection                A collection with the results of the filter operation.
   */
  function filter(\Closure $predicate);

  /**
   * Applies the given predicate p to all elements of this collection, returning true, if the predicate yields true for all elements.
   * @method  forAll
   * @param   Closure     $predicate    The predicate.
   * @return  boolean                   TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
   */
  function forAll(\Closure $predicate);

  /**
   * Applies the given function to each element in the collection and returns a new collection with the elements returned by the function.
   * @method
   * @param   Closure     $function
   * @return  Collection
   */
  function map(\Closure $function);

  /**
   * Partitions this collection in two collections according to a predicate.
   * Keys are preserved in the resulting collections.
   * @method  partition
   * @param   Closure     $predicate    The predicate on which to partition.
   * @return  array An array with two elements. The first element contains the collection
   *               of elements where the predicate returned TRUE, the second element
   *               contains the collection of elements where the predicate returned FALSE.
   */
  function partition(\Closure $predicate);

  /**
   * Gets the index/key of a given element. The comparison of two elements is strict,
   * that means not only the value but also the type must match.
   * For objects this means reference equality.
   * @method  indexOf
   * @param   mixed     $element  The element to search for.
   * @return  mixed               The key/index of the element or FALSE if the element was not found.
   */
  function indexOf($element);

  /**
   * Extract a slice of $length elements starting at position $offset from the Collection.
   * If $length is null it returns all elements from $offset to the end of the Collection.
   * Keys have to be preserved by this method. Calling this method will only return the
   * selected slice and NOT change the elements contained in the collection slice is called on.
   * @method  slice
   * @param   int       $offset
   * @param   int       $length
   * @return  array
   */
  public function slice($offset, $length = null);
}
