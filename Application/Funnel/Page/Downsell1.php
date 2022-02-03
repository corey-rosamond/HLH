<?php
/**
 * Downsell1
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * Downsell1
 *
 * The Downsell1
 */
class Downsell1 extends \App\Abstracts\FunnelPage
{
  /** @var int Include the pageType */
  protected $_pageType = self::downsell1;

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