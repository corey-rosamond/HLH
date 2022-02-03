<?php
/**
 * Proteus
 *
 * @package Framework\Core
 * @version 1.0.0
 */
namespace Framework\Core;

/**
 * Proteus is the template engine
 * @todo Create an object for frame work that catches the following issues
 * @todo Undefined method
 * @todo Undefined static method
 * @todo Undefined index
 * @todo Undefined static variable
 * @todo Undefined variable
 */
class Proteus extends \Framework\Core\Core
{

  public static function construct()
  {
    $instance = self::getInstance();

    return $instance;
  }
}
