<?php

namespace App\Page;

\Framework\_IncludeCorrect(APP_ROOT."Abstracts".DSEP."FunnelPage.php");
class ThankYou extends \App\Abstracts\FunnelPage
{

  protected $_pageType = self::thankYou;
  protected $_cFunnel;
  protected $_dOrder;
  protected $_dBillingAddress;
  protected $_dShippingAddress;
  protected $_dCustomer;
  protected $_dOrderItems;

  protected function _content( $tokens )
  {
    $this->_dOrder  = $this->_cFunnel->order();
    if(!isset($this->_dOrder['customer-key'])){
      $this->_redirectTo(self::typePage, self::checkout);
    }
    $this->_dCustomer = $this->_cFunnel->customer($this->_dOrder['customer-key']);
    $this->_dOrderItems = $this->_cFunnel->orderItems();
    $this->_dBillingAddress = $this->_cFunnel->billingAddress();
    $this->_dShippingAddress= $this->_cFunnel->shippingAddress();
    $this->_cFunnel->registerToken('{order-number}', sprintf('#%08d',$this->_dOrder['key']));
    $this->_cFunnel->registerToken('{order-email}', $this->_dCustomer['email-address']);
    $this->_cFunnel->registerToken('{billing-information}',"
      {$this->_dBillingAddress['name']}<br />
      {$this->_dBillingAddress['address-line-one']}<br />
      {$this->_dBillingAddress['address-line-two']}<br />
      {$this->_dBillingAddress['city']}
      {$this->_dBillingAddress['state']},
      {$this->_dBillingAddress['zip']}
    ");
    $this->_cFunnel->registerToken('{shipping-information}',"
      {$this->_dShippingAddress['name']}<br />
      {$this->_dShippingAddress['address-line-one']}<br />
      {$this->_dShippingAddress['address-line-two']}<br />
      {$this->_dShippingAddress['city']}
      {$this->_dShippingAddress['state']},
      {$this->_dShippingAddress['zip']}
    ");
    $itemsTable = "";
    $total = 0;
    if($this->_dOrderItems) {
      foreach ($this->_dOrderItems as $item) {
        if ($item['status'] == \Framework\Commander\OrderItems::reconciled) {
          $total += $item['price'];
          $item['price'] = "$" . number_format ($item['price'], 2);
          $itemsTable .= "<tr><td>1</td><td>{$item['name']}</td><td>{$item['price']}</td></tr>";
        }
      }
    }
    $total = '$'.number_format( $total, 2);
    $this->_cFunnel->registerToken('{sub-total-value}', $total);
    $this->_cFunnel->registerToken('{order-total-value}', $total);
    $this->_cFunnel->registerToken('{order-items}', $itemsTable);
    $this->_tokens = $this->_cFunnel->getTokens();
    return "
    <div id=\"funnel-content\">\n
      <div class=\"funnel-content-box thank-you-pre-reciept\">
        <h3>{$tokens['content-one-title']}</h3>
        <div class=\"content\">{$tokens['content-one-content']}</div>
      </div>
  		<div class=\"funnel-content-box thank-you-billing\">
  			<h3>{$tokens['billing-information-title']}</h3>
  			<div class=\"content\">{billing-information}</div>
  		</div>
  		<div class=\"funnel-content-box thank-you-shipping\">
  			<h3>{$tokens['shipping-information-title']}</h3>
  			<div class=\"content\">{shipping-information}</div>
  		</div>
  		<div class=\"funnel-content-box thank-you-order-items\">
        <h3>{$tokens['order-summary-title']}</h3>
        <div class=\"content\">
    			<table cellspacing=\"0\" cellpadding=\"0\" class=\"thank-you-order-items\">
    				<tr>
              <th>{$tokens['quantity-label']}</th>
              <th>{$tokens['product-name-label']}</th>
              <th>{$tokens['product-price-label']}</th>
            </tr>
  			    {order-items}
          </table>
    			<table cellspacing=\"0\" cellpadding=\"0\" class=\"thank-you-order-items-totals\">
    				<tr><td colspan=\"2\">{$tokens['sub-total-label']}</td><td>{sub-total-value}</td></tr>
    				<tr><td colspan=\"2\">{$tokens['shipping-label']}</td><td>{$tokens['shipping-value']}</td></tr>
    				<tr><td colspan=\"2\">{$tokens['tax-label']}</td><td>{$tokens['tax-value']}</td></tr>
    				<tr><td colspan=\"2\">{$tokens['order-total-label']}</td><td>{order-total-value}</td></tr>
    			</table>
        </div>
  		</div>
      <div class=\"funnel-content-box\">
  			<h3>{$tokens['content-two-title']}</h3>
  	    <div class=\"content\">{$tokens['content-two-content']}</div>
      </div>
    </div>";
  }
}
