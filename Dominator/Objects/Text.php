<?php
/**
 * Text
 *
 * @package Framework\Dominator\Objects
 * @version 1.0.0
 */
namespace Framework\Dominator\Objects;

/**
 * Text
 *
 * The text object
 */
class Text {

  /**
   * The text
   * @var string $this->text
   * @access private
   */
  private $text;

  /**
   * The node name for this object
   * @var string $this->nodeName
   * @access private
   */
  private $nodeName;

  /**
   * Reference to the parent node of this object or false
   * @var     \Framework\Dominator\Support\DocumenObject|false  $this->parentNode
   * @access  private
   */
  private $parentNode;

  /**
   * Construct the text element
   * @method __construct
   * @param  string      $text Value we are setting text to
   * @return $this
   * @access public
   */
  public function __construct( $text = null ){
    /** Initalize text with the value passed */
    $this->setText( $text );
    /** Set the node name */
    $this->nodeName = 'text';
    /** Return this */
    return $this;
  }

  /**
   * Destroy the text object
   * @method __destruct
   * @access public
   */
  public function __destruct(){
    unset( $this->text );
    unset( $this->nodeName );
  }

  /**
   * Return the node name
   * @method nodeName
   * @return string
   * @access public
   */
  public function nodeName(){
    return $this->nodeName;
  }

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
   * Return the parentNode reference or false
   * @method  parentNode
   * @return  \Framework\Dominator\Support\DocumenObject|\Framework\Dominator\Objects\Text|false
   * @access  public
   */
  public function parentNode(){
    /** Check if it has been set */
    if( !$this->parentNode ){
      /** Not set return false */
      return false;
    }
    /** parentNode was set return it */
    return $this->parentNode;
  }

  /**
   * Set the text attribute for the object
   * @method setText
   * @param  string  $text Value we are setting text to
   * @return boolean
   * @access public
   */
  public function setText( $text ){
    /** Set text to the value passed **/
    if( !$this->text = $text ){
      /** if we fail return false */
      return false;
    }
    /** we did it return true */
    return true;
  }

  /**
   * get the value of text and return it
   * @method getText
   * @return string  The value stored in the text attribute
   * @access public
   */
  public function getText(){
    return $this->text;
  }

  /**
   * Render the text node
   * @method render
   * @return string
   * @access public
   */
  public function render(){
    return $this->getText();
  }
}
