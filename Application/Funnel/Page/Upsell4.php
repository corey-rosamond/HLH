<?php
/**
 * Upsell4
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * Upsell4
 *
 * The Upsell4
 */
class Upsell4 extends \App\Abstracts\FunnelPage
{
  /** @var int Include the pageType */
  protected $_pageType = self::upsell4;
  /**
   * Builds the content area and calls all sub functions to build
   * the ui specific to this page
   * @method _content
   * @param $data
   * @return string|void
   */
  protected function _content($data)
  {
    return "
      <div id=\"funnel-content\">
        <form method=\"post\" action=\"{destination-success}\" id=\"funnel-upsell-form\">
          {$data['content']} {$this->_products()}
        </form>
      </div>
    ";
  }
}
