<?php
/**
 * Singleton
 *
 * @package Framework\Support\Interfaces
 * @version 1.0.0
 */
namespace Framework\Support\Interfaces;

/**
 * Singleton
 *
 * Singleton Interface this is mainly here for clairity
 **/

interface Singleton
{

  /**
   * Use the get_called_class method to check if we
   * have created an instance of this object if we
   * have not create it
   * @method getInstance
   * @return object
   * @access public
   * @static
   **/
  public static function getInstance();
}
