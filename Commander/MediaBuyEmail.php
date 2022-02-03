<?php
/**
 * MediaBuyEmail
 *
 * @package Framework\Advent
 * @version 1.2.0
 */
namespace Framework\Commander;

class MediaBuyEmail extends Commander
{

  private $_mMediaBuyEmailLinks;
  private $_mMediaBuyPromoters;

  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_mMediaBuyEmailLinks = \Framework\Core\Receptionist::modal("MediaBuyEmailLinks", true);
    //$instance->_mMediaBuyPromoters  = \Framework\Core\Receptionist::modal("MediaBuyEmailLinks", "Holylandhealth", true);
    return $instance;
  }


  public function campaignExists($funnelKey, $campaignKey)
  { return $this->_mMediaBuyEmailLinks->campaignExists($funnelKey, $campaignKey); }


  /**
   * Build the email link select form element
   * @method buildEmailLinkSelect
   * @param      $selected
   * @param null $documentId
   * @return string
   */
  public function buildEmailLinkSelect($selected, $funnelKey, $documentId=null)
  {
    /** @var string $id */
    $id = "emailLinkKey";
    /** Check if a different id was specified */
    if(!is_null($documentId)){
      /** @var string $id set id to the specified id */
      $id = $documentId;
    }
    /** @var array $funnels */
    $links = $this->_mMediaBuyEmailLinks->getFunnelLinksKeyAndName($funnelKey);
    /** @var string $select Build the select box into the select variable */
    $select = "<select id=\"{$id}\" name=\"{$id}\">";
    /** @var array $funnel */
    foreach($links as $link){
      $isSelected = (($selected==$link['key'])?' selected=selected':'');
      /** add the option to the select */
      $select .= "<option value=\"{$link['key']}\"{$isSelected}>{$link['name']}</option>";
    }
    /** return the select box */
    return "$select</select>";
  }


  /**
   * This is the event interface for this command object
   * This allows javascript to make calls to this object
   * to preform pre-defined tasks
   * @method event
   */
  public function event()
  {
    /** Configure the event */
    $this->_eConfigure();
    /** Find the requested task */
    switch($this->_dEventAction){
      /** Return an array of funnel links */
      case 'funnel-links':
        /** @var array|false $links */
        $links = $this->_mMediaBuyEmailLinks->getFunnelLinksKeyAndName($this->_dEventData['funnelKey']);
        /** Check if false was returned this means there were no records */
        if(!$links){
          /** Return an empty array */
          $this->_eResponse(true, []);
        }
        /** Return the data */
        $this->_eResponse(true, $links);
        break;
    }
  }
}
