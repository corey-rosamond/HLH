<?php
/**
 * ComplemarOrderSubmit
 *
 * @package Framework\Docket
 * @version 1.0.0
 */
namespace Framework\Docket;

/**
 * AbandonedOrderFixer
 *
 * Submit orders to complemar
 */
class AbandonedOrderFixer extends \Framework\Event\Cronjob
{
  /**
   * Run the CronJob
   * @method  run
   *
   * @param   array $data [description]
   * @param         $cCronTab
   *
   * @return array The status array [ 'status' => boolean, 'messages' => array[ 'status' => boolean, 'message'
   *                       => 'some text']]
   * @access  public
   * @static
   */
  public static function run($data, $cCronTab)
  {
    try{
      $instance = self::getInstance();
      $cOrders = \Framework\Core\Receptionist::controller('Orders', true);
      $cOrderNote = \Framework\Core\Receptionist::controller('OrderNotes', true);
      $orders = $cOrders->getAbandonedOrders();

      if(!$orders){
        /** Log nothing to do */
        $instance->addMessage($cCronTab::typeNotice, "Nothing to do");
        /** Set dockets status back to idle */
        $cCronTab->setStatus(0, \Framework\Commander\CronTab::statusIdle);
        /** End the cronjob and turn our report in to docket */
        return $instance->returnReport($cCronTab, $data);
      }
      $FunnelModal = \Framework\Core\Receptionist::modal("Funnel", "Holylandhealth", true);
      $Services = \Framework\Core\Receptionist::controller("Services", true);
      $EmailTemplates = \Framework\Core\Receptionist::modal("EmailTemplates", "Holylandhealth", true);
      $Email = \Framework\Core\Receptionist::controller("Email", true);
      $EmailLog = \Framework\Core\Receptionist::controller("EmailLog", true);
      $Customer = \Framework\Core\Receptionist::controller("Customer", true);
      foreach($orders as $record) {
        /** Get the order data */
        $orderKey = $record['key'];
        $Order = $cOrders->loadOrder ($orderKey);
        $cOrders->markOrderCompleted ($orderKey);
        $Customer->updateCustomerValue ($Order['Order']['customer-key']);
        /** Put the data members into variables  */
        $customer = $Order['Customer'];
        $order = $Order['Order'];
        $billing = $Order['BillingAddress'];
        $shipping = $Order['ShippingAddress'];
        $items = $Order['OrderItems'];
        /** Get the email service objects */
        $funnelData = $FunnelModal->get ($order['funnel-key']);
        $funnelData['tokens'] = json_decode ($funnelData['tokens'], true);

        $EmailCredentials = $Services->get ($funnelData['order-email']);
        $Template = $EmailTemplates->get ($funnelData['order-receipt-email-template']);
        /** Build the email */
        $orderTable = '<table cellspacing="0" cellpadding="10" style="width:580px;">';
        $orderTotal = 0;
        foreach ($items as $item) {
          if ($item['status'] == \Framework\Commander\OrderItems::reconciled) {
            $orderTable .= '<tr>';
            $orderTable .= '<td style="text-align:center;width:50px;font-size:14px;">1</td>';
            $orderTable .= '<td style="font-size:14px;text-align:left;">' . $item['name'] . '</td>';
            $orderTable .= '<td style="width:100px;font-size:14px;text-align:right;">$' . number_format ($item['price'], 2) . '</td>';
            $orderTable .= '</tr>';
            $orderTotal += $item['price'];
          }
        }
        $orderTable .= '</table>';
        /** Setup the email tokens */
        $tokens = [
          '{site-name}' => $funnelData['tokens']['{site-name}'],
          '{support-email}' => $funnelData['tokens']['{support-email}'],
          '{support-number}' => $funnelData['tokens']['{support-phone}'],
          '{order-number}' => sprintf ('#%08d', $order['key']),
          '{order-date}' => date ("l jS \of F Y h:i:s A", strtotime ($order['order-date'])),
          '{shipping-information}' => "{$shipping['name']}<br />{$shipping['address-line-one']}<br />{$shipping['address-line-two']}<br />{$shipping['city']} {$shipping['state']}, {$shipping['zip']}",
          '{billing-information}' => "{$billing['name']}<br />{$billing['address-line-one']}<br />{$billing['address-line-two']}<br />{$billing['city']} {$billing['state']}, {$billing['zip']}",
          '{order-table}' => $orderTable,
          '{subtotal}' => number_format ($orderTotal, 2),
          '{order-total}' => number_format ($orderTotal, 2),
          '{shipping}' => '0.00',
          '{tax}' => '0.00'
        ];

        $emailReceipt = $Email->emailHandle ($EmailCredentials);
        $emailReceipt->to ($customer['email-address']);
        $emailReceipt->from (array($EmailCredentials->username () => $EmailCredentials->name ()));
        $emailReceipt->subject ($Template['subject'], $tokens);
        $emailReceipt->body ($Template['message'], $tokens);
        $emailReceipt->send ();
        $EmailLog->logSend(
          $emailReceipt->getTo(),
          $emailReceipt->getFrom(),
          $emailReceipt->getSubject(),
          $emailReceipt->getBody(),
          $funnelData['key'],
          $customer['key'],
          $order['key'],
          0,
          0
        );
      }
      /** We are done return our report to docket */
      return $instance->returnReport($cCronTab, $data);
    } catch ( \Framework\Exceptional\BaseException $exception ){
      //print_r($exception);
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