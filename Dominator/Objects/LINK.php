<?php
/**
 * LINK
 *
 * @package Framework\Dominator\Objects
 * @version 1.0.0
 */
namespace Framework\Dominator\Objects;

/**
 * LINK
 *
 * This is the framework STYLE tag object it is used to normalize the creation of style tags
 */
class LINK{

  /**
   * The tagname
   * @var     string
   * @access  private
   */
  private $tag  = 'link';

  /**
   * The rel attribute
   * @var     string
   * @access  private
   */
  private $rel  = 'stylesheet';

  /**
   * The type attribute
   * @var     string
   * @access  private
   */
  private $type   = 'text/css';

  /**
   * The href attribute
   * @var     string
   * @access  private
   */
  private $href;

  /**
   * Construct the tag
   * @method  __construct
   * @param   string      $src  The src for this script tag
   * @return  this              Return this to enable method chaining
   * @access  public
   */
  public function __construct( $href ){
    $this->href = $href;
    return $this;
  }

  /**
   * Return the html for this tag
   * @method  html
   * @return  string  Return the html needed to render this tag
   * @access  public
   */
  public function html(){
    return "<{$this->tag} rel='{$this->rel}' type='{$this->type}' href='{$this->href}'/>";
  }
}
