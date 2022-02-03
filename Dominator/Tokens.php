<?php
namespace Framework\Dominator;

class Tokens implements \ArrayAccess
{

    /**
     * @var array
     */
    private $tokens = array();

    /**
     * @return mixed
     */
    public function __construct()
    {return $this;}

    /**
     * @param $offset
     */
    public function offsetExists($offset)
    {return isset($this->tokens[$offset]);}

    /**
     * @param $offset
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {return $this->tokens[$offset];}
        return false;
    }

    /**
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {$this->tokens[$offset] = $value;}

    /**
     * @param $offset
     */
    public function offsetUnset($offset)
    {
        echo __METHOD__;
    }
}
