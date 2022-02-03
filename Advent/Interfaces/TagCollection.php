<?
/**
 * StyleSheet
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
interface TagCollection extends \Countable, \IteratorAggregate, \ArrayAccess
{

  public function __construct(array $elements = array());

  public function __destruct();

  public function set($sheet, $location);

  public function add($sheet, $location);

  public function exists($sheet);

  public function get($sheet);

  public function offsetExists($offset);

  public function getKeys();

  public function offsetGet($offset);

  public function offsetSet($offset, $value);

  public function offsetUnset($offset);

}
