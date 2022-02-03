<?php
/**
 * Order
 *
 * @package Framework\Commander
 * @version 1.0.0
 */
namespace Framework\Commander;

/**
 * Order
 * 
 * DESCRIPTION
 */
class Order extends \Framework\Support\Abstracts\Singleton
{


  /**
   * Setup the Order Controller
   * @method __construct
   * @access public
   */
   public static function construct()
   {
     $instance = self::getInstance();
     
     return $instance;
   }
}