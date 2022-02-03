<?
/**
 * TokenCollection
 *
 * @package Framework\Advent\Object
 * @version 1.0.0
 */
namespace Framework\Advent\Interfaces;

/**
 * StyleSheet
 *
 * PHP Object to generage style sheets
 */
interface TokenCollection extends \Countable, \IteratorAggregate, \ArrayAccess
{
  /**
   * Initializes a new sheet collection
   * @method  __construct
   * @param   array $elements
   * @return  StyleSheet
   * @access  public
   */
  public function __construct(array $elements = array());

  /**
   * __destruct
   * @method  __destruct
   * @access  public
   */
  public function __destruct();


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
  public function set($sheet, $location);

  /**
   * Adds an element to the collection.
   * @method  add
   * @param   mixed   $value
   * @return  boolean         Always TRUE.
   * @access  public
   */
  public function add($sheet, $location);

  /**
   * Checks for the existance of a style sheet returns the key or false
   * @method  exists
   * @param   string $script  The sheet we are looking for
   * @return  mixed           FALSE or the key if it exists
   * @access  public
   */
  public function exists($sheet);

  /**
  * Gets the element with the given key/index.
  * @method   get
  * @param    mixed   $key  The key.
  * @return   mixed         The element or NULL, if no element exists for the given key.
  * @access   public
  */
  public function get($sheet);

  /**
   * ArrayAccess implementation of offsetExists()
   * @method  offsetExists
   * @param   mixed         $offset
   * @return  boolean
   * @access  public
   * @uses    containsKey()
   */
  public function offsetExists($offset);

  /**
   * Gets all keys/indexes of the collection elements.
   * @method  getKeys
   * @return  array
   * @access  public
   */
  public function getKeys();

  /**
   * ArrayAccess implementation of offsetGet()
   * @method  offsetGet
   * @param   mixed         $offset
   * @access  public
   * @uses    get()
   */
  public function offsetGet($offset);

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
  public function offsetSet($offset, $value);

  /**
   * ArrayAccess implementation of offsetUnset()
   * @method  offsetUnset
   * @param   mixed   $offset
   * @return  boolean
   * @access  public
   * @uses    remove()
   */
  public function offsetUnset($offset);

}
