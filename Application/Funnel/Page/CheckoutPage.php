<?php
/**
 * CheckoutPage
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * CheckoutPage
 *
 * The checkout page
 */
class CheckoutPage extends \App\Abstracts\FunnelPage
{
  const compTypeCheckoutTitle = 0;
  const compTypeSupportMessage = 1;
  const compTypeErrorBox = 2;
  const compTypeCheckoutForm = 3;
  const compTypeProductBox = 4;
  const compTypeTotalsBox = 5;
  const compTypeGuaranteeBox = 6;
  const compTypeTrustBox = 7;
  const compTypeTermsBox = 8;


  /** @var int Include the pageType */
  protected $_pageType = self::checkout;
  /** @var boolean $_formHandler */
  protected $_formHandler;
  /** @var boolean $_sHideErrors */
  protected $_sHideErrors = true;
  /** @var boolean $_sPreQualify */
  protected $_sPreQualify = false;

  /**
   * Builds the content area and calls all sub functions to build
   * the ui specific to this page
   * @method _content
   * @param $data
   * @return string|void
   * @internal param $tokens
   */
  protected function _content($data)
  {
    /** Setup the form handler */
    $this->_formHandler = \Framework\Core\Receptionist::controller("FunnelFormHandler",true);
    /** Set the funnel prefix for the handler */
    $this->_formHandler->setPrefix($this->_sPrefix);
    /** Now just let it run */
    return $this->_contentBox($this->_buildComponents($data['components']), "funnel-content", null).$this->_productPopups().$this->_validation();
  }


  private function _buildComponents($components)
  {
    $content = null;
    foreach($components as $component){
      $content .= $this->_buildComponent($component);
    }
    return $content;
  }

  private function _buildComponent($component)
  {
    if(!$component['settings']['enabled']){
      return null;
    }
    switch($component['type']){
      case self::compTypeCheckoutTitle:
        return $this->_checkoutTitle($component['settings'], $component['components']);
        break;
      case self::compTypeSupportMessage:
        return $this->_supportMessage($component['settings'], $component['components']);
        break;
      case self::compTypeErrorBox:
        return $this->_errorBox($component['settings'], $component['components']);
        break;
      case self::compTypeCheckoutForm:
        return $this->_checkoutForm($component['settings'], $component['components']);
        break;
      case self::compTypeProductBox:
        return $this->_productBox($component['settings'], $component['components']);
        break;
      case self::compTypeTotalsBox:
        return $this->_totalsBox($component['settings'], $component['components']);
        break;
      case self::compTypeGuaranteeBox:
        return $this->_guaranteeBox($component['settings'], $component['components']);
        break;
      case self::compTypeTrustBox:
        return $this->_trustBox($component['settings'], $component['components']);
        break;
      case self::compTypeTermsBox:
        return $this->_termsBox($component['settings'], $component['components']);
        break;
    }
  }


  private function _checkoutTitle($settings, $components)
  {
    return "<div id=\"checkout-title\">".
      "{$this->_icon($components['icon'], $settings['icon'])}&nbsp;".
      "{$this->_text($components['text'], $settings['text'])}".
    "</div>";
  }

  private function _supportMessage($settings, $components)
  {
    if($settings['icon-position']==0){
      return "<div id=\"support-message\">".
        "{$this->_icon($components['icon'], $settings['icon'])}&nbsp;".
        "{$this->_text($components['text'], $settings['text'])}".
      "</div>";
    }
    return "<div id=\"support-message\">".
      "{$this->_text($components['text'], $settings['text'])}&nbsp;".
      "{$this->_icon($components['icon'], $settings['icon'])}".
    "</div>";
  }

  private function _errorBox($settings, $components)
  {
    $components['title']['text'] = "<span class=\"count\">0</span>&nbsp;".$components['title']['text'];
    return "<div id=\"funnel-checkout-errors\" class=\"checkout-box\">".
      "{$this->_title($components['title'], $settings)}".
      "<div class=\"content\"><ul></ul></div>".
    "</div>";
  }

  private function _checkoutForm($settings, $components)
  {
    $title = null;
    if($settings['title']){
      $title = "<h3 class=\"title\">".
        "<span class=\"icon\">{$this->_icon($components['title']['icon'], $settings['icon'])}</span>".
        "<span class=\"title\">{$this->_text($components['title']['text'], $settings['text'])}</span>".
      "</h3>";
    }
    return "<div id=\"checkout-box\" class=\"checkout-box\">".
      "{$title}".
      "<div class=\"content\">".
        $this->_formHandler->form($components['form'],$this->_data[self::dataProducts]).
      "</div>".
    "</div>";
  }

  private function _productBox($settings, $components)
  {
    $content = "";
    foreach($this->_data[self::dataProducts] as &$product) {
      $content .= "<div class=\"funnel-product-image product-image-{$product['key']}\" id=\"image-{$product['key']}\">".
        "<img src=\"{$product['image']}\" alt=\"{$product['name']}\" />".
      "</div>";
    }
    return "<div id=\"product-box\" class=\"checkout-box\">".
      "{$this->_title($components['title'], $settings)}".
      "<div class=\"content\">{$content}</div>".
    "</div>";
  }

  private function _totalsBox($settings, $components)
  {
    $count = 0;
    $total = 0;
    $content = "<table cellspacing=\"0\" cellpadding=\"0\">";
    foreach($this->_data[self::dataProducts] as &$product){
      $class = (($count%2)?'even':'odd');
      $content .= "<tr class=\"{$class}\">";
      $content .= "<td id=\"product-name-{$product['key']}\">{$product['name']}:</td>";
      $content .= "<td id=\"product-price-{$product['key']}\">$".number_format($product['price'],2)."</td>";
      $total += $product['price'];
      $content .= "</tr>";
      $count++;
    }
    $class = (($count%2)?'even':'odd');
    $content .= "<tr class=\"{$class}\">";
    $content .= "<td id=\"shipping-label\">Shipping:</td>";
    $content .= "<td id=\"shipping-value\"><span>FREE</span></td>";
    $content .= "</tr>";
    $count++;
    $class = (($count%2)?'even':'odd');
    $content .= "<tr class=\"{$class}\">";
    $content .= "<td id=\"salestax-label\">Sales Tax:</td>";
    $content .= "<td id=\"salestax-value\"><span>$0.00</span></td>";
    $content .= "</tr>";
    $count++;
    $class = (($count%2)?'even':'odd');
    $content .= "<tr class=\"{$class}\">";
    $content .= "<td id=\"total-label\">Order Total:</td>";
    $content .= "<td id=\"total-value\"><span>$".number_format($total,2)."</span></td>";
    $content .= "</tr></table>";
    return "<div id=\"totals-box\" class=\"checkout-box\"><h3 class=\"title\"></h3>".
      "{$this->_title($components['title'], $settings)}".
      "<div class=\"content\">{$content}</div>".
    "</div>";
  }

  private function _guaranteeBox($settings, $components)
  {
    $components['image']['src'] = $this->_data[self::dataAssetLocation]."images/".$components['image']['src'];
    return "<div id=\"guarantee-box\" class=\"checkout-box\">".
      "<div class=\"content\">".
        "{$this->_image($components['image'])}".
        "{$components['content']}".
      "</div>".
    "</div>";
  }

  private function _trustBox($settings, $components)
  {
    $content = null;
    $index = 0;
    foreach($components['images'] as &$image){
      $image['src'] = $this->_data[self::dataAssetLocation]."images/".$image['src'];
      $content .= $this->_image($image, "trust-{$index}");
      $index++;
    }
    return "<div id=\"trust-box\" class=\"checkout-box\">".
      "{$this->_title($components['title'], $settings)}".
      "<div class=\"content\">{$content}</div>".
    "</div>";
  }

  private function _termsBox($settings, $components)
  {
    return "<div id=\"terms-box\" class=\"checkout-box\">".
      "{$this->_title($components['title'], $settings)}".
      "<div class=\"content\">{$components['content']}</div>".
    "</div>";
  }

  private function _title($configuration, $settings)
  {
    if(!isset($settings['close'])||!$settings['close']){
      return "<h3 class=\"title\">".
        "<span class=\"icon\">{$this->_icon($configuration['icon'], $settings['icon'])}</span>&nbsp;".
        "<span class=\"text\">{$this->_text($configuration['text'], $settings['text'])}</span>".
      "</h3>";
    }
    return "<h3 class=\"title\">".
      "<span class=\"icon\">{$this->_icon($configuration['icon'], $settings['icon'])}</span>&nbsp;".
      "<span class=\"text\">{$this->_text($configuration['text'], $settings['text'])}</span>".
      "<span class=\"close\">{$this->_close($configuration['close'], $settings['close'])}</span>".
    "</h3>";
  }

  private function _productPopups()
  {
    $html = "";
    foreach( $this->_data[self::dataProducts] as &$product ){
      $html .= "<div class=\"product-description-popup\" id=\"product-{$product['key']}\">";
      $html .= "<div class=\"header\">";
      $html .= "<div class=\"title\"><i class=\"fa fa-info-circle\"></i> {$product['name']}</div>";
      $html .= "<div class=\"close\">";
      $html .= "<span class=\"fa-stack\">";
      $html .= "<i class=\"fa fa-square fa-stack-2x\"></i>";
      $html .= "<i class=\"fa fa fa-times fa-stack-1x fa-inverse\" style=\"margin: -1px 0px 0px -1px;\"></i>";
      $html .= "</span>";
      $html .= "</div>";
      $html .= "</div>";
      $html .= "<div class=\"content\">";
      $html .= "<img class=\"image\" src=\"{$product['image']}\" alt=\"{$product['image']} Image\" />";
      $html .= "<span class=\"product-description\">{$product['description']}</span>";
      $html .= "</div>";
      $html .= "</div>";
    }
    return $html;
  }

  private function _validation(){
    return "<script type=\"text/javascript\">{$this->_formHandler->validation()}</script>";
  }



  private function _checkoutBox($id)
  {
    return "<div {$this->_id($id)} {$this->_class('checkout-box')}>".

    "</div>";
  }



  private function _text($text, $setting)
  {
    if(!$setting){
      return null;
    }
    return $text;
  }

  private function _icon($configuration, $setting)
  {
    if(!$setting){
      return null;
    }
    $size = null;
    if($configuration['size']!=0){

    }
    return "<i class=\"{$configuration['type']}{$size}\">&nbsp;</i>";
  }

  private function _close($content, $setting)
  {
    if(!$setting){
      return null;
    }
    return "{$content}";
  }

  private function _image($configuration, $class=null, $id=null)
  {
    return "<img
      src=\"{$configuration['src']}\"
      alt=\"{$configuration['alt']}\"
      {$this->_id($id)}
      {$this->_class($class)}
     />";
  }

  private function _id($id)
  {
    if(is_null($id)){
      return null;
    }
    return " id=\"{$id}\"";
  }

  private function _class($class)
  {
    if(is_null($class)){
      return null;
    }
    return " class=\"{$class}\"";
  }
}
