<?php
/**
 * ComplemarOrderStatus
 *
 * @package Framework\Docket
 * @version 1.0.0
 */
namespace Framework\Docket;

/**
 * ComplemarOrderStatus
 *
 * Update order status from Complemar
 */
class ComplemarOrderStatus extends \Framework\Event\Cronjob
{
  /**
   * Run the CronJob
   * @method  run
   * @param   array $data [description]
   * @return  array        The status array [ 'status' => boolean, 'messages' => array[ 'status' => boolean, 'message'
   *                       => 'some text']]
   * @access  public
   * @static
   */
  public static function run($data, $cCronTab)
  {
    try{
      /** @var \Framework\Docket\ComplemarOrderStatus $instance */
      $instance = self::getInstance();
      /** @var \Framework\Commander\Orders $cOrders */
      $cOrders = \Framework\Core\Receptionist::controller('Orders', true);
      /** @var \Framework\Commander\OrderNotes $cOrderNote */
      $cOrderNote = \Framework\Core\Receptionist::controller('OrderNotes', true);
      /** @var array|boolean $ordersReadyForFulfillment */
      $fulfillmentOrders = $cOrders->ordersInFulfillment();




      /** We are done return our report to docket */
      return $instance->returnReport($cCronTab, $data);
    } catch ( \Framework\Exceptional\BaseException $exception ){
      /** @var array $trace Get the stack trace so we can log it */
      $trace = print_r($exception->getTrace(), true);
      /** Add an error message to this cron log */
      $instance->addMessage($cCronTab::typeError, "Fatal Error: {$exception->getMessage()} StackTrack: {$trace}");
      /** @var boolean status set our status to false we failed */
      $instance->status = false;
      /** Return our error report to docket */
      return $instance->returnReport($cCronTab, $data);
    }
  }
}