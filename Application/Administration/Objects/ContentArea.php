<?php
/**
 * ContentArea
 *
 * @package App\Objects
 * @version 1.0.0
 */
namespace App\Objects;

/**
 * ContentArea
 *
 * Handles the quick sidebar
 */
class ContentArea
{

  private $_dTokens;

  public function __construct($tokens)
  {
    $this->_dTokens = $tokens;
    return $this;
  }

  public function build()
  {
    return "
    <div class='page-content-wrapper'>
      <div class='page-content'>
        <div class='page-bar'>
          <ul class='page-breadcrumb'>
              <li><a href=''>Home</a><i class=''></i></li>
          </ul>
          <div class='page-toolbar'></div>
        </div>
        <h3 class='page-title'>
          {$this->_dTokens['header']}
          <small>{$this->_dTokens['sub-header']}</small>
        </h3>";
  }
}