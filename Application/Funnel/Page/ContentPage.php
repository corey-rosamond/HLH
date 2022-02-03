<?php
/**
 * ContentPage
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * ContentPage
 *
 * The ContentPage page
 */
class ContentPage extends \App\Abstracts\FunnelPage
{
  /** @var int Include the pageType */
  protected $_pageType = self::contentPage;

  /**
   * Builds the content area and calls all sub functions to build
   * the ui specific to this page
   * @method _content
   * @param $content
   * @return string
   */
  protected function _content($content)
  { return "<div id=\"funnel-content\">{$content['content']}</div>"; }
}
