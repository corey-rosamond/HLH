<?php
/**
 * ComplemarOrderSubmit
 *
 * @package Framework\Docket
 * @version 1.0.0
 */
namespace Framework\Docket;

/**
 * ComplemarOrderSubmit
 *
 * Submit orders to complemar
 */
class ComplemarOrderSubmit extends \Framework\Event\Cronjob
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
      /** @var \Framework\Docket\ComplemarOrderSubmit $instance */
      $instance = self::getInstance();
      /** @var \Framework\Commander\Orders $cOrders */
      $cOrders = \Framework\Core\Receptionist::controller('Orders', true);
      /** @var \Framework\Commander\OrderNotes $cOrderNote */
      $cOrderNote = \Framework\Core\Receptionist::controller('OrderNotes', true);
      /** @var array|boolean $ordersReadyForFulfillment */
      $ordersReadyForFulfillment = $cOrders->ordersReadyForFulfillment();
      /** Check if we have jobs */
      if(!$ordersReadyForFulfillment){
        /** Log nothing to do */
        $instance->addMessage($cCronTab::typeNotice, "No orders ready for Fulfillment!");
        /** Set dockets status back to idle */
        $cCronTab->setStatus(0,\Framework\Commander\CronTab::statusIdle);
        /** End the cronjob and turn our report in to docket */
        return $instance->returnReport($cCronTab, $data);
      }
      /** @var \Framework\Commander\Commander $Complemar */
      $Complemar = \Framework\Core\Receptionist::controller('Complemar', true);
      /** @var array $order Iterate over the orders */
      foreach($ordersReadyForFulfillment as $order){
        /** @var integer|boolean $transKey */
        $transKey = $Complemar->createOrderInComplemar($order);
        /** Check if it failed */
        if($transKey===false){
          /** Log the error to the cron log */
          $instance->addMessage(\Framework\Commander\CronTab::typeError, "Order: {$order['Order']['key']}: FAILED TO SUBMIT! Message returned {$transKey}");
          /** Log the error in order notes */
          $cOrderNote->addOrderNote($order['Order']['key'], 0, "FAILED TO SUBMIT TO COMPLEMAR! Message returned {$transKey}");
          /** Finish this loop out and move to the next */
          continue;
        }
        /** Set the transaction key returned to us by complemar in the order */
        $cOrders->setComplemarTransactionKey($order['Order']['key'],$transKey);
        /** Set the order as in fulfillment */
        $cOrders->_setOrderStatus($order['Order']['key'], \Framework\Commander\Orders::fulfillment);
        /** Log that we have done something */
        $instance->addMessage($cCronTab::typeNotice, "Order: {$order['Order']['key']} => Transaction Key: {$transKey}");
      }
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