<?php
/**
 * Footer
 *
 * @package App\Objects
 * @version 1.2.0
 */
namespace App\Objects;

/**
 * Footer
 *
 * This builds both the top and side navigation elements
 */
class Footer
{

  private $_dTokens;

  public function __construct($tokens)
  {
    $this->_dTokens = $tokens;
    return $this;
  }

  public function build(){
    return "
    <div class='page-footer'>
      <div class='page-footer-inner'> {$this->_dTokens['copyright-year']} &copy; {$this->_dTokens['company-name']}
        <a target='_blank' title='{$this->_dTokens['company-website']}' href='{$this->_dTokens['company-website-full']}'>{$this->_dTokens['company-website']}</a>
      </div>
      <div class='scroll-to-top' style='display: block;'>
        <i class='icon-arrow-up'></i>
      </div>
    </div>\n";
  }
}