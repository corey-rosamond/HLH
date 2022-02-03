<?php
/**
 * ErrorPage
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * ErrorPage
 *
 * The ErrorPage
 */
class ErrorPage extends \App\Abstracts\FunnelPage
{
  /** @var integer Include the pageType */
  protected $_pageType = self::error;
  /** @var \Framework\Exceptional\BaseException $_exception */
  protected $_exception;

  /**
   * Builds the content area and calls all sub functions to build
   * the ui specific to this page
   * @method _content
   * @param $tokens
   */
  protected function _content($data)
  {
    /** Write the content to the buffer */
    return "
    <div id=\"funnel-content\">
      <h1 class=\"title\" id=\"title\">{$data['title']}</h1>
      {$data['content']}
    </div>";
  }
}
