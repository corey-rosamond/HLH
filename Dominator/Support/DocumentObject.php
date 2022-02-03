<?php
/**
 * DocumentObject
 *
 * @package Framework\Dominator\Support
 * @version 1.0.0
 */
namespace Framework\Dominator\Support;

/**
 * DocumentObject
 *
 * This is the base for all Document Objects
 */
class DocumentObject{

  /***************************
  *     Object Variables     *
  /**************************/

  /**
   * The tag for this document object
   * @var     string|false  $this->tag
   * @access  protected
   */
  protected $tag;

  /**
   * The nodeName of this object
   * @var     false|string $this->nodeName
   * @access  private
   */
  private $nodeName = false;

  /**
   * Id attribute
   * @var     false|string $this->id
   * @access  private
   */
  private $id = false;

  /**
   * Name attribute
   * @var     false|string  $this->name
   * @access  private
   */
  private $name = false;

  /**
   * Reference to the text node child or false
   * @var     \Framework\Dominator\Objects\Text|false $this->text
   * @access  private
   */
  private $text = false;

  /**
   * Array of child elements
   * @var     []\Framework\Dominator\Support\DocumenObject|[]\Framework\Dominator\Objects\Text  $this->children
   * @access  private
   */
  private $children = [];

  /*********************************
  *   	  OBJECT COLLECTIONS       *
  /********************************/

  /**
   * Attribute object
   * @var     \Framework\Dominator\Support\AttributeObject  $this->attributes
   * @access  private
   */
  private $attributes;

  /**
   * Class object
   * @var     \Framework\Dominator\Support\ClassObject $this->classes
   * @access  private
   */
  private $classes;

  /**
   * Style object
   * @var     \Framework\Dominator\Support\StyleObject  $this->styles
   * @access  private
   */
  private $styles;

  /*********************************
  *   	    NODE REFERENCES        *
  /********************************/

  /**
   * Reference to the parent node of this object or false
   * @var     \Framework\Dominator\Support\DocumenObject|false  $this->parentNode
   * @access  private
   */
  private $parentNode;

  /**
   * Reference to the first child node or false
   * @var     \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false  $this->firstChild
   * @access  private
   */
  private $firstChild = false;

  /**
   * Reference to the last child node or false
   * @var     \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false  $this->lastChild
   * @access  private
   */
  private $lastChild = false;

  /*********************************
  *   	 CONSTRUCT / DESTRUCT      *
  /********************************/

  /**
   * __construct
   * @method  __construct
   * @param   false|array     $options    Construct options to allow setting much of the object up on construct
   * @return  $this
   * @uses    \Framework\Dominator\Support\AttributeObject
   * @uses    \Framework\Dominator\Support\ClassObject
   * @uses    \Framework\Dominator\Support\StyleObject
   * @access  public
   */
  public function __construct( $options = false )
  {
    /** Set collection defaults */
    $attributes = [];
    $classes    = [];
    $styles     = [];
    /** Check if we have options */
    if( $options ){
      /** Check if id is set and not false */
      if( isset( $options['id'] ) && $options['id'] ){ $this->id = $options['id']; }
      /** Check if name is set and not false */
      if( isset( $options['name']) && $options['name'] ){ $this->name = $options['name']; }
      /** Check if attributes isset and not false */
      if( isset( $options['attributes']) && $options['attributes'] ){ $attributes = $options['attributes']; }
      /** Check if classes isset and not false */
      if( isset( $options['classes']) && $options['classes'] ){ $classes = $options['classes']; }
      /** Check if styles isset and not false */
      if( isset( $options['styles']) && $options['styles'] ){ $styles = $options['styles']; }
    }
    /** @var string $this->nodeName String representation  of the node name */
    $this->nodeName   = $this->tag;
    /** @var \Framework\Dominator\Support\AttributeObject $this->attributes */
    $this->attributes = new \Framework\Dominator\Support\AttributeObject( $attributes );
    /** @var \Framework\Dominator\Support\ClassObject $this->classes */
    $this->classes    = new \Framework\Dominator\Support\ClassObject( $classes );
    /** @var \Framework\Dominator\Support\StyleObject $this->styles */
    $this->styles     = new \Framework\Dominator\Support\StyleObject( $styles );
    /** Return */
    return $this;
  }

  /**
   * This is a highly used object we want to make sure that
   * we do not have any memory leaks
   * @method  __destruct
   * @return  void
   * @access  public
   */
  public function __destruct()
  {
    /** Unset the flat variables */
    unset( $this->tag );
    unset( $this->id );
    unset( $this->name );
    unset( $this->parentNode );
    /** desruct the attributes object */
    $this->attributes->__destruct();
    /** unset the attributes reference */
    unset( $this->attributes );
    /** destroy the styles object */
    $this->styles->__destruct();
    /** unset the styles reference */
    unset( $this->styles );
    /** destruct the class object*/
    $this->classes->__destruct();
    /** unset the class reference */
    unset( $this->classes );
    /** We have to unset the first and last child references last */
    unset( $this->firstChild );
    unset( $this->lastChild );
  }

  /*********************************
  *   	      NODE METHOD          *
  /********************************/

  /**
   * Get the nodeName for this object
   * @method  nodeName
   * @return  false|string   The string representation of the nodeName or false
   * @access  public
   */
  public function nodeName(){
    /** If not set return false */
    if( !$this->nodeName ){ return false; }
    /** nodeName set return its value */
    return $this->nodeName;
  }

  /**
   * Return the parentNode reference or false
   * @method  parentNode
   * @return  \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false
   * @access  public
   */
  public function parentNode(){
    /** if not set return false */
    if( !$this->parentNode ){ return false; }
    /** parentNode was set return it */
    return $this->parentNode;
  }

  /**
   * Return the first child element or false
   * @method  firstChild
   * @return  \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false
   * @access  public
   */
  public function firstChild(){
    /** If not set return false */
    if(!$this->firstChild()){ return false; }
    /** firstChild was set return it */
    return $this->firstChild;
  }

  /**
   * Return the last child or false
   * @method  lastChild
   * @return  \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false
   * @access  public
   */
  public function lastChild(){
    /** If not set return false */
    if(!$this->lastChild){ return false; }
    /** it was set return it */
    return $this->lastChild;
  }

  /**
   * Find the node we are given in the level 1 children
   * @method  findChild
   * @param   \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text
   * @return  \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false
   * @access  private
   */
  private function findChild( $node )
  {
    /** Loop through the nodes */
    foreach( $this->children as $index => $node ){
      /** Compare the Objects */
      if( $node === $child ){ return $this->child[$index]; }
    }
    /** Node doesent exist return false */
    return false;
  }

  /**
   * Append the new node to the end of the collection
   * @method  append
   * @param   \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false
   * @return  \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text
   * @access  public
   */
  public function append( $node )
  {
    /** Set the node variables parentNode to this */
    $node->setParentNode( $this );
    /* Get the next index to be used */
    $index = count($this->children);
    /** Insert the new node */
    $this->children[$index] = $node;
    /** Check if this is the first child */
    if($index === 0){
      /** This is the first child elemement set the reference */
      $this->firstChild = $this->children[$index];
    }
    /** Append is always the last child so set the reference */
    $this->lastChild = $this->children[$index];
    /** Now return the refetence */
    return $this->children[$index];
  }

  /*********************************
  *   	      UPDATE METHOD        *
  /********************************/

  /**
   * Update the value stored in an existing
   * Text node
   * @method  updateText
   * @param   string     $text    value to change text to
   * @return  \Framework\Dominator\Support\DocumenObject|Framework\Dominator\Objects\Text|false
   * @access  public
   */
  public function updateText( $text )
  {
    /** Set text to the value passed */
    if( $this->text->setText( $text ) ){ return $this; }
    /** It failed return false */
    return false;
  }

  /*********************************
  *   	      VALUE METHOD         *
  /********************************/

  /**
   * Return the value of a style
   * @method  styleValue
   * @param   string        $style    Style to get the value of
   * @return  string|false
   * @uses    \Framework\Dominator\Support\StyleObject
   * @access  public
   */
  public function styleValue( $style )
  { return $this->styles->value( $style ); }

  /**
   * Return the value of the attribute
   * @method  hasAttrValue
   * @param   string        $attribute  Attribute to get the value of
   * @return  string|false
   * @uses    \Framework\Dominator\Support\AttributeObject
   * @access  public
   */
  public function attrValue( $attribute )
  { return $this->attributes->value( $attribute ); }

  /*********************************
  *   	       ADD METHOD          *
  /********************************/

  /**
   * Passthrough to enable simple use
   * @method  addClass
   * @param   string        $name Classname to add
   * @return  $this|false         False if something went very wrong this normaly
   * @uses    \Framework\Dominator\Support\ClassObject
   * @access  public
   */
  public function addClass( $name )
  { return (( $this->classes->add( $name ))? $this : false ); }

  /**
   * Add a style to this object
   * @method  addstyle
   * @param   string   $style     Style to add
   * @param   string   $value     Value to set
   * @return  $this|false
   * @uses    \Framework\Dominator\Support\StyleObject
   * @access public
   */
  public function addStyle( $style, $value )
  { return (( $this->styles->add( $style, $value ))? $this : false ); }

  /**
   * Add an attribute
   * @method  addAttr
   * @param   string   $attribute     Attribute to add
   * @param   string   $value         Value of the attribute
   * @return  \Framework\Dominator\Support\DocumenObject|false
   * @uses    \Framework\Dominator\Support\AttributeObject
   * @access  public
   */
  public function addAttr( $attribute, $value )
  { return (( $this->attributes->add( $attribute, $value ))? $this : false ); }

  /*********************************
  *   	     REMOVE METHOD         *
  /********************************/

  /**
   * Remove a child element from the collection
   * @method  remove
   * @param   \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|int
   * @return  boolean
   * @access  public
   */
  public function remove( $argument )
  {
    /** Check if the arguent is an integer */
    if( is_object( $argument) ){
      return $this->removeChild( $argument );
    }
    /** It is not an object */
    return $this->removeIndex( $argument );
  }

  /**
   * Find and remove the child node
   * @method  removeChild
   * @param   \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text
   * @return  boolean
   * @access  private
   */
  private function removeChild( $node )
  {
    /** @var $index The index of the child we want to remove */
    $index = $this->findChild( $node );
    /** Check if index is false */
    if( !$index ){
      /** Index false return false */
      return false;
    }
    /** Remove the clild */
    return $this->removeIndex( $index );
  }

  /**
   * Remove a child from the collection using its index
   * @method  removeChildIndex
   * @param   int               $index    Index to be removed
   * @return  boolean
   * @access  private
   */
  private function removeChildIndex( $index )
  {
    /** Unset that array position */
    unset( $this->children[ $index ] );
    /** Re Index the array */
    $this->children = array_values( $this->children );
    /** Set the first Child*/
    $this->firstChild = $this->children[0];
    /** Set the lastChild */
    $this->lastChild = $this->children[count($this->children)-1];
    /** return true */
    return true;
  }

  /**
   * Passthrough to enable simple use
   * @method  removeClass
   * @param   string        $name Classname to remove
   * @return  $this|false         False if something went very wrong this normaly
   * @uses    \Framework\Dominator\Support\ClassObject
   * @access  public
   */
  public function removeClass( $name )
  { return (( $this->classes->remove( $name ))? $this : false ); }

  /**
   * Remove a style
   * @method  removeStyle
   * @param   string          $style    Style to remove
   * @return  $this|false
   * @uses    \Framework\Dominator\Support\StyleObject
   * @access  public
   */
  public function removeStyle( $style )
  { return (( $this->styles->remove( $style ))? $this : false ); }

  /**
   * Remove attribute
   * @method  removeAttr
   * @param   string   $attribute   Attribute to remove
   * @return  $this|false
   * @uses    \Framework\Dominator\Support\AttributeObject
   * @access  public
   */
  public function removeAttr( $attribute )
  { eturn (( $this->attributes->remove( $attribute ))? $this : false ); }

  /*********************************
  *   	       HAS METHOD          *
  /********************************/

  /**
   * Determine if this object has children
   * @method  hasChildren
   * @return  boolean
   * @access  public
   */
  public function hasChildren()
  { return (( empty( $this->children ))? false : true); }

  /**
   * Determine if the object has text
   * @method  hasText
   * @return  boolean
   * @access  public
   */
  public function hasText()
  { return (( $this->text )? true : false ) ; }

  /**
   * Passtrhough to enable simple use
   * @method  hasClass
   * @param   string        $name Classname to look for
   * @return  boolean
   * @uses    \Framework\Dominator\Support\ClassObject
   * @access  public
   */
  public function hasClass( $name )
  { return $this->classes->has( $name ); }

  /**
   * Determine if the object has this style
   * @method  hasStyle
   * @param   string   $style Attribute to look for
   * @return  boolean
   * @uses    \Framework\Dominator\Support\StyleObject
   * @access  public
   */
  public function hasStyle( $style )
  { return $this->styles->has( $style ); }

  /**
   * Determine if the object has this attribute
   * @method  hasAttr
   * @param   string   $attribute   Attribute to look for
   * @return  boolean
   * @uses    \Framework\Dominator\Support\AttributeObject
   * @access  public
   */
  public function hasAttr( $attribute )
  { return $this->attributes->has( $attribute ); }

  /*********************************
  *   	       SET METHOD          *
  /********************************/

  /**
   * Set the parent node refererence
   * @method  setParentNode
   * @param   \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false
   * @access  public
   */
  public function setParentNode( $node ){
    $this->parentNode = $node;
  }

  /**
   * Set id
   * @method  setId
   * @param   string    $value    Value for the id attribute
   * @access  public
   */
  public function setId( $value )
  { $this->id = $value; }

  /**
   * Set Name
   * @method  setName
   * @param   string    $value    Value for name attribute
   * @access  public
   */
  public function setName( $value )
  { $this->name = $value; }

  /**
   * Make a text node insert into children
   * and set the text reference
   * @method  setText
   * @param   string      $text
   * @return  \Framework\Dominator\Support\DocumenObject
   * @access  public
   */
  public function setText( $text )
  {
    /** Check if text is already set */
    if( $this->text ){ return $this->updateText( $text ); }
    /** Text is not Set make a fake DomObject */
    $this->text = $this->append( ( new \Framework\Dominator\Objects\Text( $text ) ) );
    /** Done return this */
    return $this;
  }

  /*********************************
  *   	      GET METHOD           *
  /********************************/

  /**
   * Get id
   * @method  getId
   * @return  string
   * @access public
   */
  public function getId()
  { return $this->id; }

  /**
   * Get name
   * @method  getName
   * @return  string    The value of name
   * @access  public
   */
  public function getName()
  { return $this->name; }

  /**
   * Get the value of text and return it
   * @method  getText
   * @return  string|false
   * @access  public
   */
  public function getText()
  {
    /** check if text is false */
    if( !$this->text ){
      /** Text hasent been set return false */
      return false;
    }
    /** Text is set return it */
    return $this->text;
  }

  /*********************************
  *   	     RENDER METHOD         *
  /********************************/

  /**
   * Render the Object and children
   * @method  render
   * @return  string html
   * @access  public
   */
  public function render(){
    /** write the open tag to html */
    $html = $this->open();
    /** Check if the object has children */
    if( $this->hasChildren() ){
      /** Loop through the children */
      foreach( $this->children as $child ){
        /** Tell each child to render */
        $html .= $child->render();
      }
    }
    /** Output the end tag and return */
    return $html.$this->close();
  }

  /**
   * Renders the open tag
   * @method  open
   * @return  string Close tag
   * @access  public
   */
  public function open()
  {
    /** Return false if tag is false */
    if( !$this->tag ){ return false; }
    /** Generate the string and return it  */
    return "<{$this->tag}{$this->renderId()}{$this->renderName()}{$this->renderClass()}{$this->renderStyle()}{$this->renderAttr()}>";
  }

  /**
   * Renders the close tag
   * @method  close
   * @return  string Close tag
   * @access  public
   */
  public function close()
  {
    /** If tag is empty retun false */
    if( !$this->tag ){ return false; }
    /** Return the close tag */
    return "</{$this->tag}>";
  }

  /**
   * render the id attribute
   * @method  renderId
   * @return  string
   * @access  private
   */
  private function renderId()
  {
    /** Check if it was set **/
    if(!$this->id){ return ""; }
    /** return the id attribute */
    return " {id=\"{$this->id}\"}";
  }

  /**
   * render the name attribute
   * @method  renderName
   * @return  string      The html content for this attribute
   * @access  private
   */
  private function renderName()
  {
    /** Check if it was set **/
    if(!$this->name){ return ""; }
    /** return the attribute */
    return " id=\"{$this->name}\"";
  }

  /**
   * Passthrough to make this a bit clearer
   * @method  renderClass
   * @return  string        String containing the class attribute
   * @uses    \Framework\Dominator\Support\ClassObject
   * @access  private
   */
  private function renderClass()
  { return $this->classes->render(); }

  /**
   * Render the styles
   * @method  renderStyle
   * @return  string     String containing the style tag
   * @uses    \Framework\Dominator\Support\StyleObject
   * @access  private
   */
  private function renderStyle()
  { return $this->styles->render(); }

  /**
   * Render the attribute string
   * @method  renderFAttr
   * @return  string          String containing the Attributes
   * @uses    \Framework\Dominator\Support\AttributeObject
   * @access  private
   */
  private function renderAttr()
  { return $this->attributes->render(); }
}
