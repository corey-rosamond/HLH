<?php
/**
 * StyleSheet
 *
 * @package Framework\Advent\Abstracts
 * @version 1.0.0
 */
namespace Framework\Advent\Abstracts;

/**
 * StyleSheet
 *
 * PHP Object to generage style sheets
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT."Advent".DSEP."Interfaces".DSEP."TagCollection.php" );

class TagCollection extends implements \Framework\Advent\Interfaces\TagCollection{


    protected $_elements;

    protected $_count = 3;

    public function __construct(array $elements = array())
    {

    }

    public function __destruct()
    {

    }

    public function add( $key, $value ){


    }

    public function remove($key, $reindex=false)
    {
      array_splice( $this->_$elements, $key);
    }


    public function keys($search=null, $strict=null)
    {
      return array_keys($this->_elements, $search, $strict);
    }


    public function count()
    {
      return $this->_count;
    }



    public function set($key, $value)
    {


    }

    public function exists($sheet)
    {


    }


    public function get($sheet)
    {

    }


    public function offsetExists($offset)
    {

    }


    public function getKeys()
    {

    }


    public function offsetGet($offset)
    {

    }

    public function offsetSet($offset, $value)
    {

    }


    public function offsetUnset($offset)
    {

    }


    public function count()
    {

    }


    public function getIterator()
    {

    }
}
