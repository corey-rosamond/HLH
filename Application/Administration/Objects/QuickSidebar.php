<?php
/**
 * QuickSidebar
 *
 * @package App\Objects
 * @version 1.0.0
 */
namespace App\Objects;

/**
 * QuickSidebar
 *
 * Handles the quick sidebar
 */
class QuickSidebar
{


  public function __construct()
  {
    return $this;
  }


  /**
   * Return the html for the toggle
   * @method headerToggle
   */
  public function headerToggle()
  {
    return "
    <li class=\"dropdown dropdown-quick-sidebar-toggler\">
      <a class=\"dropdown-toggle\" href=\"javascript:;\" id=\"QuickSidebar-Toggle-Open\"><i class=\"icon-logout\"></i></a>
    </li>";
  }

  /**
   * Return the html for the sidebar
   * @method sidebar
   * @return string
   */
  public function build()
  {
    return "
    <a href='javascript:;' class='page-quick-sidebar-toggler' id=\"QuickSidebar-Toggle-Close\">
      <i class='icon-login'></i>
    </a>
    <div class='page-quick-sidebar-wrapper' id=\"QuickSidebar-Wrapper\">
      <div class='page-quick-sidebar'>
        <ul class='nav nav-tabs'>
          <li class='active'>
            <a href='javascript:;' data-target='#quick_sidebar_tab_1' data-toggle='tab'></a>
          </li>
        </ul>
      </div>
    </div>";
  }
}