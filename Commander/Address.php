<?php

namespace Framework\Commander;

class Address extends \Framework\Support\Abstracts\Singleton
{

  private $_document;
  private $_modal;

  private $_apType  = "box";
  private $_asTitle = "Shipping Address";
  private $_abTitle = "Shipping Address";
  private $_asIcon  = "fa fa-pencil";
  private $_abIcon  = "icon-paper-clip";
  private $_asColor = "red-sunglo";
  private $_abColor = "green-meadow";
  private $_asId    = "aShippingDt";
  private $_abId    = "aShippingDt";
  private $_asEBid  = "edit-shipping-address";
  private $_asEPid  = "edit-shipping-address-popup";
  private $_aeSBid  = "edit-shipping-address-save";


  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_modal     = \Framework\Core\Receptionist::modal( "Address", "Holylandhealth", true );
    $instance->_document  = \Framework\Core\Receptionist::controller( 'Document', true );
    return $instance;
  }

  public function get( $key )
  { return $this->_modal->get( $key ); }

  public function makeAddress( $name, $lineOne, $lineTwo, $city, $state, $zip)
  { return $this->_modal->makeAddress( $name, $lineOne, $lineTwo, $city, $state, $zip); }

  public function billingPortlet( $key, $name )
  {
    return $this->_document->portlet(
      $this->_apType,$this->_abColor,$this->_abIcon,$this->_abTitle,'',
      $this->_document->datatable(
        $this->_abId,[],$this->_datatableData($this->_modal->get($key),$name)
      ),[],[],true
    );
  }

  public function shippingPortlet( $key, $name, $enable = true )
  {
    $address  = $this->_modal->get( $key );
    $tools    = [];
    $actions  = [['name'=>'button','text'=>'Edit','button-class'=>'btn btn-default btn-sm','button-id'=>$this->_asEBid,'icon'=>$this->_asIcon]];
    $this->_document->makePopupForm(
      $this->_asEBid,$this->_asEPid,$this->_aeSBid,$this->_editAddressJS(),
      "Edit {$this->_asTitle}",$this->_editAddressForm($address),$this->_asIcon
    );
    return $this->_document->portlet(
      $this->_apType,$this->_asColor,$this->_asIcon,$this->_asTitle,'',
      $this->_document->datatable(
        $this->_asId,[],$this->_datatableData($this->_modal->get($key),$name)
      ),$tools,$actions,true
    );
  }

  private function _editAddressForm( $address )
  {
    return
    "<form class=\"form-inline\">
      <input type=\"hidden\" id=\"key\" value=\"{$address['key']}\" />
      <table class=\"table table-bordered table-striped\">
        <tbody>
          <tr>
            <td style=\"width:35%\"> Name </td>
            <td style=\"width:50%\">{$address['name']}</td>
          </tr>
          <tr>
            <td style=\"width:35%\"> Address Line One </td>
            <td style=\"width:50%\">{$address['address-line-one']}</td>
          </tr>
          <tr>
            <td style=\"width:35%\"> Address Line Two </td>
            <td style=\"width:50%\">{$address['address-line-two']}</td>
          </tr>
          <tr>
            <td style=\"width:35%\"> City </td>
            <td style=\"width:50%\">{$address['city']}</td>
          </tr>
          <tr>
            <td style=\"width:35%\"> State </td>
            <td style=\"width:50%\">{$address['state']}</td>
          </tr>
          <tr>
            <td style=\"width:35%\"> Zip </td>
            <td style=\"width:50%\">{$address['zip']}</td>
          </tr>
        </tbody>
      </table>
    </form>";
  }

  private function _editAddressJS()
  {
    return "alert(\"save clicked\");";
  }

  private function _datatableData( $address, $name )
  {
    $data = [];
    $data[] = [ '<strong>Name</strong>',   (($address['name']=="")?$name:$address['name']) ];
    $data[] = [ '<strong>Address</strong>',$address['address-line-one'] ];
    if( $address['address-line-two'] != "" ){ $data[] = [ '<strong>Address Two</strong>', $address['address-line-two'] ]; }
    $data[] = [ '<strong>City</strong>',   $address['city'] ];
    $data[] = [ '<strong>State</strong>',  $this->_states[ltrim(rtrim(strtoupper($address['state'])))]];
    $data[] = [ '<strong>Zip</strong>',    $address['zip'] ];
    return $data;
  }

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
