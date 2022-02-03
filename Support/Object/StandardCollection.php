<?php
/**
 * Singleton
 *
 * @package App\Objects
 * @version 1.0.0
 */
namespace Framework\Support\Object;

/**
 * StandardCollection
 *
 *This is the standard collection object
 * @todo Restricted object type
 * @todo More efficent way to compare strings
 * @todo More getters and setters
 * @todo Magic methods to throw exceptions
 */
class StandardCollection implements \ArrayAccess
{
    /**
     * Container for the collection of objects
     * @access private
     * @var null|array
     */
    private $collection;

    /**
     * Null or a pointer to the current object
     * @access private
     * @var null|resource
     */
    private $iteratorCurrent;

    /**
     * The current position  of the iterator
     * @access private
     * @var null|int
     */
    private $iteratorPosition;

    /**
     * The current count of objects in this collection
     * @access private
     * @var null|int
     */
    private $cbjectCount;

    /**
     * Add item to the standardCollection
     * @method add
     * @param   array|object $object this is the object we want to add to the collection
     * @return  this;
     * @access public
     */
    public function add($object)
    {
      foreach ($object as $key => $value) {
        $this->collection[$key] = $value;
        if (is_array($value)) {
          $this->collection[$key] = (new StandardCollection())->add($value);
        }
        $this->incrementCount();
      }
      return $this;
    }

    /**
     * Get the next item in the collection
     * @method next
     * @return resource $this->iteratorCurrent the current resource
     * @access public
     */
    public function next()
    {
        $this->iteratorPosition = ((!$this->isLast()) ? $this->iteratorPosition++ : 0);
        $this->setIteratorCurrent();
        return $this->iteratorCurrent;
    }

    /**
     * construct the StandardCollection
     * @method __construct
     * @return $this Return this for method chaining
     * @access public
     */
    public function __construct()
    {
        $this->collection       = array();
        $this->objectCount      = 0;
        $this->iteratorPosition = $this->resetIterator();
        return $this;
    }

    /**
     * Destroy all the data contained in the StandardCollection
     * @method __destruct
     * @access public
     */
    public function __destruct()
    {
        foreach ($this->collection as $object) {$object = null;}
        $this->collection       = null;
        $this->objectCount      = null;
        $this->iteratorPosition = null;
    }

    /**
     * Check if that offset index exists in the collection
     * @method offsetExists
     * @param  boolean       $offset Does it exist
     * @access
     */
    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    /**
     * offsetGet
     *
     * Get the item stored in the requested position or throw exception
     *
     * @param int|string
     * @return resource
     * @throws \Framework\Exceptional\UndefinedIndexException
     * @access public
     *
     */
    public function offsetGet($offset)
    {
        /** Check if the array index exists in the collection */
        if (isset($this->collection[$offset])) {
            /* we have something in the offset return it */
            return $this->collection[$offset];
        }
        /**
         * @var array store back trace position 0 to the bt var
         * we use this get get the paramaters to throw an exception
         */
        $bt = debug_backtrace()[0];
        /** Build the exception and throw it we are done */
        throw new \Framework\Exceptional\UndefinedIndexException(
            "Undefined Index: $offset", 0, 1, $bt['file'], $bt['line'], null);
    }

    /**
     * Set the object for a given index then reset the count
     * @param int
     * @param mixed
     * @return void
     * @access public
     * @uses function Collection::setObjectCount()
     */
    public function offsetSet($offset, $value)
    {
        $this->collection[$offset] = $value;
        $this->setObjectCount(count($this->collection));
    }

    /**
     * Unset something in the collection reindex the
     * collection then reset the count to the new amount
     * @param int
     * @return void
     * @access public
     * @uses function Collection::setObjectCount()
     */
    public function offsetUnset($offset)
    {
        unset($this->Collection[$offset]);
        $this->collection = array_values($this->collection);
        $this->setObjectCount(count($this->collection));
    }

    /**
     * Subtract one from the count of objects in the collection
     * @return void
     * @access private
     */
    private function decrementCount()
    {
        $this->objectCount--;
    }

    /**
     * Add one from the count of objects in the collection
     * @return void
     * @access private
     */
    private function incrementCount()
    {
        $this->objectCount++;
    }

    /**
     * This checks if the iterator is at the last element in  the collection
     * @return true|false
     * @access private
     */
    private function isLast()
    {
        return ((($this->objectCount - 1) == $this->iteratorPosition) ? true : false);
    }

    /**
     * Resets the IteratorPosition back to 0
     * @return void
     * @access private
     */
    private function resetIterator()
    {
        $this->iteratorPosition = 0;
    }

    /**
     * SetThe Iterator current as a reference the current selected obect
     * @return void
     * @access private
     */
    private function setIteratorCurrent()
    {
        $this->iteratorCurrent &= $this->collection[$this->iteratorPosition];
    }

    /**
     * Set the current position of the iterator
     * @param int $IteratorPosition The position we want to set
     * @return void
     * @access private
     */
    private function setIteratorPosition(int $iteratorPosition)
    {
        $this->iteratorPosition = $iteratorPosition;
    }

    /**
     * Sets the count of objects in this collection
     * @param int $Count new count we are setting
     * @return void
     * @access private
     */
    private function setObjectCount(int $count)
    {
        $this->objectCount = $count;
    }
}
