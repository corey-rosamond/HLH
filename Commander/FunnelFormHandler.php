<?php

namespace Framework\Commander;

class FunnelFormHandler extends \Framework\Support\Abstracts\Singleton
{

  const emailAddress  = 0;
  const phoneNumber   = 1;
  const firstName     = 2;
  const lastName      = 3;
  const addressOne    = 4;
  const addressTwo    = 5;
  const city          = 6;
  const state         = 7;
  const zip           = 8;
  const ccNumber      = 9;
  const ccCVV         = 10;
  const ccExpire      = 11;

  private $_sPrefix;
  private $_configurationGroups;
  private $_buffer;

  private $_groupID;
  private $_elementID;
  private $_unique;
  private $_groupMap;
  private $_elementMap;

  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_unique = time();
    return $instance;
  }

  public function setPrefix( $prefix )
  { return $this->_sPrefix = $prefix; }

  public function form($configuration,$products)
  {
    $product = '';
    foreach($products as $key => $value){
      $product .= "<input type=\"hidden\" value=\"{$key}\" name=\"products[]\">";
    }
    $groups = $this->_configuration($configuration);
    foreach( $groups as $group => &$members ){
      $groupID = $this->_groupID();
      $this->_groupMap[$groupID] = $members;
      if(!is_array($members)){
        //$this->_write("{$group}<br />not array<br />");
        continue;
      }
      switch ($group){
        case 'billing': $this->_write($this->_group($members)); break;
        case 'billing-same-as-shipping':
          $checked = (($members['checked'])?' checked="checked"':'');
          if(isset($_SESSION[$this->_sPrefix]['order-data']['billing-is-shipping'])){
            $checked = '';
            if($_SESSION[$this->_sPrefix]['order-data']['billing-is-shipping']=='on'){
              $checked = ' checked="checked"';
            }
          }
          $this->_write("<div id=\"$groupID\" class=\"funnel-input-row\">\n");
            $this->_write("<input
              type=\"checkbox\"
              id=\"billing-is-shipping\"
              name=\"billing-is-shipping\"
              class=\"\"{$checked} />&nbsp;&nbsp;");
            $this->_write($members['label']);
          $this->_write("</div>");
        break;
        case 'shipping':
          $hidden = "style=\"display:block;\" ";
          if($members['hidden']){
            $hidden = "style=\"display:none;\" ";
          }
          if(isset($_SESSION[$this->_sPrefix]['order-data']['billing-is-shipping'])){
            $hidden = "style=\"display:block;\" ";
            if($_SESSION[$this->_sPrefix]['order-data']['billing-is-shipping']=='on'){
              $hidden = "style=\"display:none;\" ";
            }
          }
          $this->_write("<div id=\"shipping-address\" {$hidden}>");
            $this->_write("<h3 class=\"funnel-form-title\">{$members['title']}</h3>\n");
            $this->_write($this->_group($members));
          $this->_write("</div>");
        break;
        case 'credit-card': $this->_creditCard($members); break;
      }
    }
    return "{$this->_form($groups)}{$this->_buffer}{$this->_submitButton($groups)}{$product}</form>\n";
  }

  private function _group($group)
  {
    foreach($group as $id => &$configuration){
      if(!is_array($configuration)){
        if($id=='clear'){
          $this->_write("<div style=\"clear:{$configuration};\"></div>");
        }
        continue;
      }
      $elementID = $this->_elementID();
      $this->_elementMap[$id] = $configuration;
      $value = ((isset($_SESSION[$this->_sPrefix]['order-data'][$id]))?$_SESSION[$this->_sPrefix]['order-data'][$id]:'');
      $this->_write("<div class=\"{$id} funnel-input-row\">\n");
        if(isset($configuration['label'])){
          $this->_write("<label class=\"funnel-input-label\" for=\"{$id}\">{$configuration['label']}</label>\n");
        }
        switch($id){
          case 'shipping-state':
          case 'billing-state':
          $this->_write("<select id=\"{$id}\" name=\"{$id}\">");
          foreach($this->_states as $key=>$val){
            if($key==$value){
              $this->_write("<option value=\"{$key}\" SELECTED=\"SELECTED\">{$val}</option>");
            } else {
              $this->_write("<option value=\"{$key}\">{$val}</option>");
            }
          }
          $this->_write("</select>");
          break;
          default:
            $this->_write("<input id=\"{$id}\" name=\"{$id}\" type=\"text\" value=\"$value\" />\n");
          break;
        }
        $this->_write("<span class=\"funnel-input-description\">\n");
          $this->_write("<span class=\"funnel-input-error\"></span>\n");
          if(isset($configuration['helper'])&&$configuration['helper']!==false){
            $this->_write($this->_helperBox($id, $configuration['helper']));
          }
        $this->_write("</span>\n");
      $this->_write("</div>\n");
    }
  }

  private function _creditCard($members)
  {
    $this->_write("<h3 class=\"funnel-form-title\">{$members['title']}</h3>\n");
    foreach($members as $id => &$configuration){
      if(!is_array($configuration)){
        if($id=='clear'){
          $this->_write("<div style=\"clear:{$configuration};\"></div>");
        }
        continue;
      }
      $elementID = $this->_elementID();
      $this->_elementMap[$id] = $configuration;
      $this->_write("<div class=\"funnel-input-row {$id}\">\n");
        if(isset($configuration['label'])){
          $this->_write("<label class=\"funnel-input-label\" for=\"{$id}\">{$configuration['label']}</label>\n");
        }
        switch($id){
          case 'expiration-year':
            $this->_ccExpireYear($elementID,$id);
          break;
          case 'expiration-month':
            $this->_ccExpireMonth($elementID,$id);
          break;
          default: $this->_write("<input id=\"{$id}\" name=\"{$id}\" type=\"text\" value=\"\" />\n"); break;
        }
        $this->_write("<span class=\"funnel-input-description\">\n");
          $this->_write("<span class=\"funnel-input-error\"></span>\n");
          if(isset($configuration['helper'])&&$configuration['helper']!==false){
            $this->_write($this->_helperBox($id, $configuration['helper']));
          }
        $this->_write("</span>\n");
      $this->_write("</div>\n");
    }
  }

  private function _ccExpireMonth($elementID,$id)
  {
    $this->_write("<select id=\"{$id}\" name=\"{$id}\" >\n");
    $months = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    for($month = 1; $month <= 12; $month++){
      $this->_write("<option value=\"{$month}\">{$months[$month]}</option>");
    }
    $this->_write("</select>\n");
  }

  private function _ccExpireYear($elementID,$id)
  {
    $start = (date("Y"));
    $end  = (date("Y")+10);
    $this->_write("<select id=\"{$id}\" name=\"{$id}\">");
    for($year=$start;$year<=$end;$year++){
      $shortYear = substr($year, -2);
      $this->_write("<option value=\"{$shortYear}\">{$year}</option>");
    }
    $this->_write("</select>");
  }

  private function _helperBox($elementID, $helper)
  {
    return "
    <span class=\"checkout-help\">\n
      <i class=\"fa fa-question-circle fa-2x\"></i>\n
    </span>\n
    <span class=\"funnel-input-helper\">\n
      <span class=\"header\">\n
        <span class=\"title\">\n<i class=\"{$helper['icon']}\"></i>\n{$helper['title']}</span>\n
        <span class=\"close\">\n
          <span class=\"fa-stack\">\n
            <i class=\"fa fa-square fa-stack-2x\"></i>\n
            <i style=\"margin: -1px 0px 0px -1px;\" class=\"fa fa fa-times fa-stack-1x fa-inverse\"></i>\n
          </span>\n
        </span>\n
      </span>\n
      <span class=\"content\">\n{$helper['message']}\n</span>\n
    </span>\n";
  }


  public function validation()
  {
    if(is_null($this->_configurationGroups)){
      return "You need to generage a form before making validation!";
    }
    return "ValidationData = ".json_encode($this->_elementMap).";";
  }

  private function _groupID()
  {
    if(is_null($this->_groupID)){
      $this->_groupID = 0;
      return "{$this->_unique}:{$this->_groupID}";
    }
    $this->_groupID++;
    return "G:{$this->_unique}:{$this->_groupID}";
  }

  private function _elementID()
  {
    if(is_null($this->_elementID)){
      $this->_elementID = 0;
      return "{$this->_unique}:{$this->_elementID}";
    }
    $this->_elementID++;
    return "{$this->_unique}:{$this->_elementID}";
  }

  private function _form($configuration)
  {
    return "<form
      method=\"post\"
      id=\"checkout-form\"
      name=\"checkout-form\"
      action=\"{destination-success}\">\n";
  }

  private function _submitButton($configuration)
  {
    return "<input
      class=\"button\"
      type=\"submit\"
      value=\"{$configuration['submit-button-text']}\">\n";
  }

  private function _configuration($configuration)
  {
    $this->_setBuffer();
    $this->_groupMap    = [];
    $this->_elementMap  = [];
    return $this->_configurationGroups = $configuration;
  }

  private function _write( $content )
  { $this->_buffer .= $content; }

  private function _setBuffer()
  { $this->_buffer = ""; }

  private $_states = array(
    "AL" => "Alabama",
    "AK" => "Alaska",
    "AZ" => "Arizona",
    "AR" => "Arkansas",
    "CA" => "California",
    "CO" => "Colorado",
    "CT" => "Connecticut",
    "DE" => "Delaware",
    "FL" => "Florida",
    "GA" => "Georgia",
    "HI" => "Hawaii",
    "ID" => "Idaho",
    "IL" => "Illinois",
    "IN" => "Indiana",
    "IA" => "Iowa",
    "KS" => "Kansas",
    "KY" => "Kentucky",
    "LA" => "Louisiana",
    "ME" => "Maine",
    "MD" => "Maryland",
    "MA" => "Massachusetts",
    "MI" => "Michigan",
    "MN" => "Minnesota",
    "MS" => "Mississippi",
    "MO" => "Missouri",
    "MT" => "Montana",
    "NE" => "Nebraska",
    "NV" => "Nevada",
    "NH" => "New Hampshire",
    "NJ" => "New Jersey",
    "NM" => "New Mexico",
    "NY" => "New York",
    "NC" => "North Carolina",
    "ND" => "North Dakota",
    "OH" => "Ohio State",
    "OK" => "Oklahoma",
    "OR" => "Oregon",
    "PA" => "Pennsylvania",
    "RI" => "Rhode Island",
    "SC" => "South Carolina",
    "SD" => "South Dakota",
    "TN" => "Tennessee",
    "TX" => "Texas",
    "UT" => "Utah",
    "VT" => "Vermont",
    "VA" => "Virginia",
    "WA" => "Washington",
    "WV" => "West Virginia",
    "WI" => "Wisconsin",
    "WY" => "Wyoming"
  );
}

/*
if(isset($members['title'])){
  if($group=='credit-card'){
    $members['title'] .= "
    \n";
  }
  $this->_write("<h3 class=\"funnel-form-title\">{$members['title']}</h3>\n");
 */
