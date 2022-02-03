<?php
/**
 * Header
 *
 * @package App\Objects
 * @version 1.2.0
 */
namespace App\Objects;

/**
 * Header
 *
 * This builds both the top and side navigation elements
 */
class Header
{
  /**
   * Navigation Object For making the top navigation
   * @var     Navigation    $Navigation   This is the Navigation object
   * @access  private
   */
  private $Navigation;

  /**
   * This the the Collection object with all of the events Tokens
   * @var     \Framework\Advent\MainCollection  $Tokens  Token Collection
   * @access  private 
   */
  private $Tokens;

  private $userMenu = [
    [ "title" => "My Profile",  "icon" => "icon-user",  "id" => "my-profile",   "on-click"  => "" ]
    ,"DIVIDER", 
    [ "title" => "Lock Screen", "icon" => "icon-lock",  "id" => "lock-screen",  "on-click"  => "" ],
    [ "title" => "Log Out",     "icon" => "icon-key",   "id" => "log-out",      "on-click"  => "" ]
  ];

  /**
   * Construct the Heder object
   * @method  __construct
   * @param   Navigation    $Navigation The Navigation object
   * @return  Header
   * @access  public
   */
  public function __construct( $Navigation, $Tokens ){
    $this->Navigation = $Navigation;
    $this->Tokens = $Tokens;
    return $this;
  }

  /**
   * Build the Administration Header Html
   * @method  build
   * @return  string HTML for Header
   * @access  public
   */
  public function Build(){
    return $this->StartHeader() .
      $this->ToggleBar( "\\Main\\Dashboard\\Dashboard", "{$this->Tokens['image-directory']}hlhlogo.png", "HolylandHealth Dashboard" ) .
      $this->Navigation->TopNavigation() . 
      $this->SearchForm( "POST", "Search...", "search-form" ) .
      $this->StartHeaderRightMenu() . 
      $this->BuildUserDropDown() .
      $this->QuickBarToggle() . 
      $this->EndHeaderRightMenu() .
      $this->EndHeader();
  }

  /**
   * QuickBar Toggle
   * @method  QuickBarToggle
   * @return  string HTML fpr the quick bar toggle
   * @access  private
   */
  private function QuickBarToggle(){
    return "
    <li class=\"dropdown dropdown-quick-sidebar-toggler\">
      <a class=\"dropdown-toggle\" href=\"javascript:;\" id=\"QuickSidebar-Toggle-Open\"><i class=\"icon-logout\"></i></a>
    </li>";
  }

  /**
   * Build the user drop down
   * @method BuildUserDropdown
   * @uses \Framework\Core\Patron::getInstance()
   * @uses \Framework\Core\Patron->getLoggedInUserData()
   *
   * @access private
   */
  private function BuildUserDropDown(){
    /** @var \Framework\Core\Patroit $Patron This is an instance of the Patron object */
    $Patron = \Framework\Core\Patron::getInstance();
    /** @var array $userData  Get the full user data of the user currently logged in */
    $userData = $Patron->getLoggedInUserData();
    /** Start adding the UserRight meuu to the HTML output */
    $html = $this->StartRightMenuDropDown(
      $userData['first-name']." ".$userData['last-name']."s Profile Photo", 
      $userData['photo'], 
      "dropdown dropdown-user",
      "img-circle",
      "username username-hide-on-mobile", 
      $userData['first-name']." ".$userData['last-name'] 
    );
    array_map(
      function($item) use (&$html){
        if( is_array($item) ){
          return $html .= $this->RightMenuDropItem( 
            $item['id'], $item['icon'], $item['title'] 
          );
        }
        /** If it not an array it is a seperator */
        $html .= "<li class=\"divider\"> </li>";
      }, $this->userMenu
    );
    $html .= $this->EndRightMenuDropDown();
    /** Return the html for the user section of the header */
    return $html;
  }


  
  /**
   * Start the header html
   * @method  StartHeader
   * @return  string HTML for StartHeader
   * @access  private
   */
  private function StartHeader()
  {
    return "
    <header class='page-header navbar navbar-fixed-top' id='logged-in'>
      <div class='page-header-inner'>";
  }

  /**
   * Toggle Bar This is the bar on the left hand side of the page to minimize and maxamize the menu
   * @method  ToggleBar
   * @param   string    $href   OnClick Destination
   * @param   string    $src    Image src / Location 
   * @param   string    $alt    Alternate Image text
   * @return  string            HTML for ToggleBar
   * @access  private
   */
  private function ToggleBar( $href, $src, $alt )
  {
    return "
    <div class='page-logo' style=''>
      <a href='{$href}'><img src='{$src}' alt='{$alt}' style='margin-top:0px;' class='logo-default' /></a>
      <div class='menu-toggler sidebar-toggler'> </div>
    </div>";
  }

  /**
   * Make the Search Form
   * @method  SearchForm
   * @param   string      $method       Search Form Method
   * @param   string      $placeholder  Placeholder Text
   * @param   string      $name         Input Name
   * @return  string                    HTML for the search form
   * @access  private
   */
  private function SearchForm( $method, $placeholder, $name )
  {
    return "
    <form id='search-form' class='search-form' action='' method='{$method}'>
      <div class='input-group'>
        <input type='text' class='form-control' placeholder='{$placeholder}' name='{$name}'>
        <span class='input-group-btn'><a href='javascript:;' class='btn submit'><i class='icon-magnifier'></i></a></span>
      </div>
    </form>";
  }

  /**
   * Start the Header Right Menu
   * @method    StartHeaderRightMenu
   * @return    string                HTML For Header Right Menu Start
   * @access    private               
   */ 
  private function StartHeaderRightMenu()
  {
    return "
    <a href='javascript:;' class='menu-toggler responsive-toggler' data-toggle='collapse' data-target='.navbar-collapse'></a>
    <div class='top-menu'>
      <ul class='nav navbar-nav pull-right'>";
  }


  private function StartRightMenuDropDown( $alt, $src, $liClass, $imageClass, $spanClass, $title )
  {
    return "
    <li class='{$liClass}'>
      <a href='javascript:;' class='dropdown-toggle' data-toggle='dropdown' data-hover='dropdown' data-close-others='true'>
        <img alt='{$alt}' class='{$imageClass}' src='{$src}' />
        <span class='{$spanClass}'> {$title} </span>
        <i class='fa fa-angle-down'></i>
      </a>
      <ul class='dropdown-menu dropdown-menu-default'>";
  }

  /**
   * Right Menu Drop Item
   * @method RightMenuDropItem
   * @param     string    $id     Drop Item ID
   * @param     string    $icon   Drop Item Icon
   * @param     string    $title  Drop Item Title
   * @return    string            HTML Right Menu Drop Item
   * @access    private
   */
  private function RightMenuDropItem( $id, $icon, $title )
  {
    return "<li><a id='{$id}'><i class='{$icon}'></i> {$title} </a></li>";
  }

  /**
   * End The Right Menu Drop Down
   * @method  EndRightMenuDropDown
   * @return  string    HTML  EndRightMenuDropDown
   * @access  private
   */
  private function EndRightMenuDropDown()
  {
    return "</ul></li>";
  }

  /**
   * End Header Right Menu
   * @method  EndHeaderRightMenu
   * @return  string    HTML  EndHeaderRightMenu
   * @access  private
   */
  private function EndHeaderRightMenu()
  {
    return "</ul></div>";
  }

 /**
   * End Header
   * @method  EndHeader
   * @return  string    HTML  EndHeader
   * @access  private
   */
  private function EndHeader()
  {
    return "</div></header>";
  }
}
