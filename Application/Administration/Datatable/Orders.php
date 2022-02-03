<?php
/**
 * Dashboard
 *
 * @package App\Event\Page\Main
 * @version 1.0.0
 */
namespace App\Event\Page\CustomerRelations\Orders;

/**
 * Orders Datatable
 *
 * This is the linch pin its where I win. So lets make sure we do this right the first time
 * 
 */

class Orders{
  
  /**
   * This is the table this datatable uses primaraly  a datatable
   * could use more than one table but in most cases we will only use one
   * @var       string    $databaseTable 
   * @access    private
   */
  private $databaseTable = "funnel-orders";

  /**
   * This is the log prefix to use. This way we can tell which datatable had a problem
   * When we are looking through the logs
   * @var       string    $logPrefix
   * @access    private 
   */
  private $logPrefix;

  

  
  public function generate(){

  }

  private function 

  public function request(){

  }


  private function exceptionHandler( $code ){

  }
}