<?php
/**
 * Navigation
 *
 * @package App\Objects
 * @version 1.0.0
 */
namespace App\Objects;

/**
 * Navigation
 *
 * This builds both the top and side navigation elements
 */
class Navigation
{

  /**
   * This is the full menu system for admin pulled from Contour
   * @var     array     $menu
   * @access  private
   */
  private $menu;

  /**
   * This is the event data which will be pulled from advent
   * @var     array     $event
   * @access  private
   */
  private $event;

  /**
   * This is the active or currently selected section
   * @var     array     $selectedSection
   * @access  private
   */
  private $selectedSection;

  /**
   * This is the active section group for the left side bar
   * @var     array     $selectedGroup
   * @access  private
   */
  private $selectedGroup;

  /**
   * This is the active sections left side bar menu data
   * @var     array     $sectionMenu
   * @access  private
   */
  private $sectionMenu;

  /**
   * Construct the navigation object and the event object
   * Set the selected section and group
   * @method  __construct
   * @return  Navigation
   * @uses    \Framework\Core\Contour::getInstance()
   * @uses    \Framework\Core\Contour->navigation()
   * @uses    \Framework\Core\Advent->getInstance()
   * @uses    \Framework\Core\Advent->getEvent()
   * @access  public
   */
  public function __construct()
  {
    /** @var array $menu Get the menu object from contour */
    $this->menu = \Framework\Core\Contour::getInstance()->navigation();
    /** @var array $event Get the event object from advent */
    $this->event = \Framework\Core\Advent::getInstance()->activeEvent();
    /** @var string $selectedSection The selected section of the menu */
    $this->selectedSection = $this->event[0];
    /** @var string $selectedGroup The section group currently selected */
    $this->selectedGroup = $this->event[1];
    /** @var string Name of the selected section Element */
    $this->selectedSectionElement = $this->event[2];
    /** @var string $sectionMenu The menu to show in the SideNavigation */
    $this->sectionMenu = $this->findSection( $this->selectedSection );
    /** Return the navigation Object */
    return $this;
  }

  /**
   * Find the section to use for the sideNavigation
   * @param   string $section  This is the section we are looking for
   * @return  array            The section of the menu we want to output in the sidebar
   * @access  private
   */
  private function findSection( $selectedSection )
  {
    /** Map ourselves throught the navigation array */
    array_map(
      /** Iterate over the groups use the navigation variable to write to */
      function( $section ) use ( &$selectedSection ){
        if( $section['event-space'] === $selectedSection ){
          $selectedSection = $section['sections'];
        }
      }, $this->menu 
    );
    return $selectedSection;
  }

  /**
   * TopNavigation
   * @method  TopNavigation
   * @return  string
   * @access  public
   */
  public function topNavigation()
  {
    /** @var string $navigation We are going to push all the html into navigation */
    $navigation = "";
    /** Map our way throuogh the navigation structure */
    array_map( 
      /** The section is the title attributes is The attributes */
      function( $section, $attributes ) use (&$navigation){
        /** Check that the user has the needed permission sto view this section */
        if( self::canDisplayMenu( $attributes ) ){
          /** @var MegaMenu $MegaMenu Make our mega menu object */
          $MegaMenu = ( new \App\Objects\MegaMenu() )
            ->setTitle( $section )
            ->setIndex( $section );
          /** Check if this is the active event */
          if( $attributes['event-space'] == $this->selectedSection ){ 
            /** This is the active mega menu mark it */
            $MegaMenu->makeActive(); 
          }
          /** Map our way through the sections */
          array_map(
            /** Get each section */
            function( $section ) use ( &$navigation, &$MegaMenu ){
              /** Add sections to the mega menu */
              $MegaMenu->addSection( $section['header'], $section['menu'] );
              /** Add the mega menu to the navigation  */
            }, $attributes['sections']
          );
          /** Complete the mega menu */
          $navigation .= $MegaMenu->make();
        }
      }, array_keys( $this->menu ), $this->menu
    );
    /** Return the top navigation in its wrapper */
    return $this->TopNavigationWrap( $navigation );
  }

  /**
   * The wrapper for the top navigation area
   * @method  TopNavigationWrap
   * @param   string $navigation This is the html for the top navigation
   * @return  string The top navigation html in the wrapper
   * @access  private
   */
  private function TopNavigationWrap( $navigation )
  {
    return "
    <div class=\"hor-menu hidden-sm hidden-xs\">
      <ul class=\"nav navbar-nav\">{$navigation}</ul>
    </div>";
  }

  /**
   * Generate the SideNavigation
   * @method  SideNavigation
   * @return  string
   * @access  public
   */
  public function SideNavigation()
  {
    /** Pull the first item off the top */
    $group = array_shift( $this->sectionMenu );
    /** @var string $navigation We are going to push all the html into navigation */
    $navigation = $this->SideGroupStart( 
      'nav-item'.(( $this->selectedGroup === $group['event-space'] )? ' active open' : '' ), 
      $group['icon'], $group['header'], (( $this->selectedGroup == $group['event-space'] )? true : false )
    );
    /** Map our way throught the menu */
    array_map(
      /** Iterate over the groups use the navigation variable to write to */
      function( $element ) use ( &$navigation ){
        /** Add this element to the navigation */
        $navigation .= $this->SideGroupElement(
          'start'.( ( $this->selectedGroup === $element['destination'] )? ' active open' : '' ), 
          $element['destination'], $element['icon'], $element['text'],
          (( $this->selectedGroup === $element['destination'] ) ? true : false )
        );
      }, $group['menu']
    );
    /** End the side group */
    $navigation .= $this->SideGroupEnd();
    /** Done with the first record now fill out the rest */
    array_map( 
      /** Setup the group */
      function( $group ) use ( &$navigation ){
        /** @var string $class This is the class name for the link group */
        $class = 'nav-item'.(( $this->selectedGroup === $group['event-space'] )?' active open':'');
        /** @var string $nvaigation Reference to the navigation variable */
        $navigation .= $this->SideGroupStart( 
          $class, $group['icon'], $group['header'], (( $this->selectedGroup == $group['event-space'] )? true: false )
        );
        array_map(
          /** Iterate over the groups use the navigation variable to write to */
          function( $element ) use ( &$navigation ){
            /** @var string $class This is the class name for the link group */
            $class = 'nav-item'.(( $this->selectedGroup === $element['destination'])? ' active open' : '' );
            /** @var string $nvaigation Reference to the navigation variable */
            $navigation .= $this->SideGroupElement( $class, $element['destination'], $element['icon'], $element['text'] );
          }, $group['menu']
        );
        /** End the SideGroup */
        $navigation .= $this->SideGroupEnd();
      }, $this->sectionMenu
    );
    /** Return the side inside the wrapper */
    return $this->SidebarWrap( $navigation );
  }

  /**
   * Wrap the sidebar Naviagion in the remaining sidebar html 
   * This way this is one more thing page does not have to worry about
   * @method    SidebarWrap
   * @param     string    $sidebar    Sidebar html content
   * @return    string                HTML for building the sideBar
   * @access    private
   */
  private function SidebarWrap( $sidebar )
  {
    return "
    <nav class='page-sidebar-wrapper'>
      <div class='page-sidebar navbar-collapse collapse'>
        <ul class='page-sidebar-menu  page-header-fixed hidden-sm hidden-xs' data-keep-expanded='false' data-auto-scroll='true' data-slide-speed='200' style='padding-top: 10px'>
          <li class='sidebar-toggler-wrapper hide'>
            <div class='sidebar-toggler'></div>
          </li>
          {$sidebar}
        </ul>
      </div>
    </nav>
    ";
  }

  /**
   * Return the html content needed to start a Side Navigation Group
   * @method  SideGropuStart
   * @param   string  $class  Class Name
   * @param   string  $icon   Element Icon
   * @param   string  $title  Display Text
   * @return  string          String of html to start the group
   * @access  private
   */
  private function SideGroupStart( $class, $icon, $title, $selected = false )
  {
    return 
    "<li class='{$class}'>
      <a href='javascript:;' class='nav-link nav-toggle'>
        <i class='{$icon}'></i>
        <span class='title'>{$title}</span>
        ".(($selected)?"<span class=\"selected\"></span>":"")."
        <span class='arrow'></span>
      </a><ul class='sub-menu'>";
  }

  /**
   * Return the html needed to make a Side Navigation Element
   * @method  SideGroupElement
   * @param   string    $class        Class Name
   * @param   string    $destination  OnClick Destination
   * @param   string    $icon         Element Icon
   * @param   string    $text         Display Text
   * @return  string                  String of html to make the element
   * @access  private
   */
  private function SideGroupElement( $class, $destination, $icon, $title )
  {
    return 
    "<li class='{$class}'>
      <a href='{$destination}' class='nav-link' style=\"padding: 6px 15px 6px 30px;\">
        <i class='{$icon}'>&nbsp;</i>&nbsp;
        <span class='title'>{$title}</span>
      </a>
    </li>";
  }

  /**
   * Return the html needed to end a Side Navigation Group
   * @method  SideGroupEnd
   * @return  String of html to end the group
   * @access  private
   */
  private function SideGroupEnd()
  {
    return "</ul></li>";
  }

  /**
   * check if we can display this menu for this user
   * @method canDisplayMenu
   * @param  array         $entry the entry to check
   * @return boolean
   * @access public
   */
  private static function canDisplayMenu( $entry )
  {
    /** if we have a permission we need to check it */
    if( $entry['permission'] ){
      if( !\Framework\Core\Patron::getInstance()->hasPermission( $entry['permission'], null ) ){ 
        return false; 
      }
    }
    /** if we get here they passed */
    return true;
  }
}
