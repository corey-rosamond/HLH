<?php
namespace Handler;

use Support\Abstraction;

/**
 * This is the handler tokens
 * @package Handler
 * @version 1.0.0
 * @todo Implement some kind of multi collection
 * @todo Come up with a faster method for replacing these tokens
 * @todo Add protection for overwriting a token that already exists
 * @todo Consider alternate token syntax
 **/
class Token extends \Support\Abstraction\Singleton
{
    /**
     * This is the current collection of tokens
     * @var array $collection
     * @access private
     */
    private $collection = array();

    /**
     * Set the token to null then return the key
     * @param $token
     * @access public
     */
    public static function register($token)
    {
        $instance                           = self::getInstance();
        $instance->collection["{*$token*}"] = null;
        echo "{*$token*}";
    }
}
