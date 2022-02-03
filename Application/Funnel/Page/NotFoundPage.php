<?php
/**
 * NotFoundPage
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * NotFoundPage
 *
 * The NotFoundPage
 */
class NotFoundPage extends \App\Abstracts\FunnelPage
{
  /** @var int Include the pageType */
  protected $_pageType = self::notFound;
  /** @var array $_requested */
  protected $_requested;

  /**
   * Builds the content area and calls all sub functions to build
   * the ui specific to this page
   * @method _content
   * @param $tokens
   */
  protected function _content($data)
  {
    return "
      <div id=\"funnel-content\">
        <h1 class=\"title\" id=\"title\">{$data['title']}</h1>
        {$data['content']}
      </div>\n
    ";
  }
}
