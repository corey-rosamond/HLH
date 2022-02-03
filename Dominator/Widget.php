<?php
/**
 * Widget
 *
 * @package Framework\Dominator
 * @version 1.0.0
 */
namespace Framework\Dominator;

/**
 * Widgets are small pieces of html
 */
abstract class Widget
{
    /**
     * @return mixed
     */
    public function __construct()
    {return $this;}
    /**
     * write
     *
     * write the content to the buffer
     * @param string content to write to the buffer
     * @return void
     * @access protected
     */
    final protected static function write($content)
    {Document::getInstance()->write($content);}
}
