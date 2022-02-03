<?php
/**
 * MegaMenu
 *
 * @package App\Objects
 * @version 1.0.0
 */
namespace App\Objects;

/**
 * MegaMenu
 *
 * This is the interface for building mega  menus
 */
class MegaMenu{

  /**
   * the title that goes on the tab for this mega menu
   * @var string
   */
  private $title;

  /**
   * index / id for this mega menu
   * @var string|int
   */
  private $index;

  /**
   * indexed array of the sections and their content
   * @var array
   */
  private $sections       = [];

  /**
   * this is an indexed array of associative arrays that contains
   * the notes and their titles
   * @var array
   */
  private $notes          = [];

  /**
   * SectionIndex for the NEXT section we create
   * @var integer $sectionIndex
   */
  private $sectionIndex   = 0;

  /**
   * noteIndex for the NEXT note we create
   * @var integer
   */
  private $noteIndex      = 0;

  /**
   * this is the flag used to set this menu item to active
   * @var boolean
   */
  private $active         = false;

  /**
   * set the title
   * @method setTitle
   * @param  string   $title title for the mega menu
   * @return This
   * @access public
   */
  public function setTitle( $title ){
    $this->title = $title;
    return $this;
  }

  /**
   * set the index for this mega menu
   * @method setIndex
   * @param  string id for this mega menu
   * @return this
   * @access public
   */
  public function setIndex( $index ){
    $this->index = $index;
    return $this;
  }

  /**
   * Set the active flag to true
   * @method makeActive
   * @return this
   * @access public
   */
  public function makeActive(){
    $this->active = true;
    return $this;
  }

  /**
   * Add a section to this megaMenu
   * @method addSection
   * @param  string     $title  The title for this section
   * @param  array()    $links['title'] $links['links']
   * @access public
   */
  public function addSection( $title, $links = array() ){
    /** Get an index for this note */
    $sectionIndex = $this->getSectionIndex();
    /** make the array */
    $this->sections[$sectionIndex] = [];
    /** set the title */
    $this->sections[$sectionIndex]['title'] = $title;
    $this->sections[$sectionIndex]['links'] = [];
    /** check the links to seee if we have links to add */
    if( count( $links ) >= 1 ){
      /** we have links lets add them */
      foreach( $links as $link ){
        $this->addSectionLink( $link['text'], $link['destination'], $sectionIndex );
      }
    }
    /** we are done return this for method chaining */
    return $this;
  }

  /**
   * Add a link to a specific section
   * @method addSectionLink
   * @param  string         $text         Display text
   * @param  string         $destination  The link destination
   * @param  int            $sectionIndex The index of the section we are adding to
   * @return this
   * @access public
   */
  public function addSectionLink( $text, $destination, $sectionIndex ){
    array_push(
      $this->sections[$sectionIndex]['links'],
      [ 'text'=>$text, 'destination'=>$destination ]
    );
    /** return this for method chaining */
    return $this;
  }

  /**
   * add a note
   * @method addNote
   * @param  string  $title     Note title
   * @param  string  $message   The note itself
   * @param  string  $color     The color scheme for this note
   * @param  index   $noteIndex The index for this note
   * @return this
   * @access public
   */
  public function addNote( $title, $message, $color, $noteIndex ){
    /** Get an index for this note */
    $noteIndex = $this->getNoteIndex();
    /** make the array */
    $this->notes[$noteIndex] = [];
    /** propigate the data **/
    $this->notes[$noteIndex]['title']     = $title;
    $this->notes[$noteIndex]['message']   = $message;
    $this->notes[$noteIndex]['color']     = $color;
    $this->notes[$noteIndex]['noteIndex'] = $noteIndex;
    /** return this for method chaining */
    return $this;
  }

  /**
   * get a note index
   * @method getNoteIndex
   * @return int the new index
   * @access private
   */
  private function getNoteIndex(){
    $index = $this->noteIndex;
    $this->noteIndex++;
    return $index;
  }

  /**
   * get a section index
   * @method getSectionIndex
   * @return int  the index
   * @access public
   */
  private function getSectionIndex(){
    $index = $this->sectionIndex;
    $this->sectionIndex++;
    return $index;
  }

  /**
   * Construct the mega menu and pass a reference back for method chaining
   * @method __construct
   * @return this
   * @access public
   */
  public function __construct( )
  { return $this; }

  /**
   * Make the described mega menu and return the html
   * @method make
   * @return  string  All of the code for the mega menu
   * @access public
   */
  public function make(){
    /** start the menu */
    $menu = '<li class="mega-menu-dropdown mega-menu-full'.(($this->active)?' active':'').'" data-hover="megamenu-dropdown" data-close-others="true">';
      /** make the tab */
      $menu .= $this->makeTab( $this->title );
      /** start the drop down */
      $menu .= '<ul class="dropdown-menu">';
        /** start the nested menu wrap **/
        $menu .= '<li><div class="mega-menu-content "><div class="row"><div class="col-md-8"><div class="row">';
          /** make all the sections */
          foreach( $this->sections as $index => $section ){ $menu .= $this->makeSection( $section, $index ); }
          /** make the seperation */
          $menu .= '</div></div>';
          $menu .= '<div class="col-md-4"><div id="accordion" class="panel-group">';
          /** make the notes section */
          $menu .= $this->makeNotesSection();
        /** end the nested menu wrap */
        $menu .= '</div></div></div></div></li>';
      /** end the drop down */
      $menu .= '</ul>';
    /** end the menu */
    return $menu.'</li>';
  }

  /**
   * Make the tab portion of the mega menu
   * @method  makeTab
   * @param   string  $title The title for this tab
   * @return  string  The html content
   * @access private
   */
  private function makeTab( $title )
  {
    return '
    <a data-close-others="true" data-hover="megamenu-dropdown" class="dropdown-toggle hover-initialized" href="javascript:;">
      '.$title.' <i style="margin-left:7px;" class="fa fa-angle-down"></i><span style="margin-bottom:-5px;" class="selected"></span>
    </a>';
  }

  /**
   * Make a complete section
   * @method makeSection
   * @param  array        $data  all data need to make the section
   * @param  int          $index thie index of this section
   * @return string              The html for this section including the links
   * @access private
   */
  private function makeSection( $data, $index ){
    /** build the container for this section */
    $section = '<div class="col-md-3"><ul class="mega-menu-submenu">';
      /** make the tab for header for this section */
      $section .= $this->makeSectionHeader( $data['title'] );
      /** add the section links */
      foreach( $data['links'] as $link ){ $section .= $this->makeSectionLink( $link['text'], $link['destination'] ); }
    /** end the section and list */
    return $section.'</ul></div>';
  }

  /**
   * Make a section header
   * @method makeSectionHeader
   * @param  string            $title The header text for this section
   * @return string
   * @access private
   */
  private function makeSectionHeader( $title )
  { 
    return '<li><h3 style="border-bottom:1px solid white;width:100%;font-size:16px;padding:0px 0px 3px 0px;">'.$title.'</h3>'; 
  }

  /**
   * Make a section link
   * @method makeSectionLink
   * @param  string          $text        Display text
   * @param  string          $destination Destination on click
   * @return string                       Html to make the link
   * @access private
   */
  private function makeSectionLink( $text, $destination )
  { return '<li><a href="'.$destination.'">'.$text.'</a></li>'; }

  /**
   * Make the notes section of the mega menu
   * @method makeNotesSection
   * @return string           html content includeing the notes
   * @access private
   */
  private function makeNotesSection(){
    $notes = '';
      foreach( $this->notes as $key => $note ){
        $notes .= $this->makeNote(
          $note['title'], $note['message'], $note['color'], $this->index, $key
        );
      }
    return $notes;
  }

  /**
   * Make a single note and return it
   * @method makeNote
   * @param  string   $title     The note title
   * @param  string   $message   The note
   * @param  string   $color     The color scheme
   * @param  int      $menuIndex The index for this mega menu
   * @param  int      $noteIndex The index for this note
   * @return string              Html for this note
   * @access private
   */
  private function makeNote( $title, $message, $color, $menuIndex, $noteIndex )
  {
    return '
    <div class="panel panel-'.$color.'">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#note_'.$menuIndex.'_'.$noteIndex.'" class="collapsed"> '.$title.' </a>
        </h4>
      </div>
      <div id="note_'.$menuIndex.'_'.$noteIndex.'" class="panel-collapse collapse">
        <div class="panel-body"> '.$message.' </div>
      </div>
    </div>';
  }
}
