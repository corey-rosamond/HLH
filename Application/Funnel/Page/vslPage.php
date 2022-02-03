<?php
/**
 * VSL Page
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * VSLPage
 *
 * The VSL Page object
 */
class VslPage extends \App\Abstracts\FunnelPage
{
  /** @var int Include the pageType */
  protected $_pageType = self::vsl;

  /**
   * LandingPage constructor
   * @method __construct
   * @param $system
   * @return \App\Page\VslPage
   */
  public function __construct($system)
  { return parent::__construct($system); }

  /**
   * Render the content for this page
   * @method _content
   * @param $tokens
   */
  protected function _content($tokens)
  {
    /** @var array $tokens */
    $tokens = json_decode($this->_cFunnel->content(), true);
    /** Write the content to the buffer */
    $this->_write("<div id=\"funnel-content\">{$this->_vsl()}{$this->_tsl($tokens['tsl'])}</div>");

    if(isset($tokens['xtra-one-title'])&&$tokens['xtra-one-title']!==false){
      $this->_write("

      <div id=\"funnel-container\">
        <div id=\"funnel-content\">
           <h1 class=\"title\">{$tokens['xtra-one-title']}</h1>
           {$tokens['xtra-one-content']}
        </div>
      </div>
      ");
    }
  }

  /**
   * Return the _vsl html
   * @method _vsl
   * @param $title
   * @param $text
   * @return string
   */
  private function _vsl($title, $text)
  {
    return "<div id=\"vsl-container\">".
      $this->_vslTitle($title).
      $this->_videoTag($tokens).
      $this->_vslButton($text).
    "</div>";
  }

  /**
   * Return the VSL title html
   * @method _vslTitle
   * @param $title
   * @return string
   */
  private function _vslTitle($title)
  { return "<h1 class=\"title\" id=\"title\">{$title}</h1>"; }

  /**
   * Return the VSL button html
   * @method _vslButton
   * @param $text
   * @return string
   */
  private function _vslButton($text)
  { return "<a href=\"{destination-success}\" id=\"vsl-buy-button\" class=\"button\">{$text}</a>"; }

  /**
   * Return the tsl html
   * @method _tsl
   * @param $tsl
   * @return string
   */
  private function _tsl($tsl)
  { return "<div id=\"tsl-container\">{$tsl}</div>"; }

  /**
   * Output a video tag
   * @method _videoTag
   * @param $tokens
   * @return string
   */
  private function _videoTag($tokens)
  {
    $videoTag = "<video autoplay id=\"vsl-player\" class=\"framework-funnel-video-player\">";
    if(!isset($tokens['videos'])){
      return "{$videoTag}{$this->_sourceTag($tokens)}</video>";
    }
  }

  /**
   * Build the source tags for the video tag
   * @method _sourceTag
   * @param      $src
   * @param      $type
   * @param null $query
   * @return string
   */
  private function _sourceTag($src, $type, $query=null)
  {
    if(!is_null($query)){
     $query = " media=\"{$query}\"";
    }
    return "<source src=\"{$src}\" type=\"{$type}\"{$query}>";
  }
}
