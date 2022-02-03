<?php
/**
 * SCRIPT
 *
 * @package Framework\Dominator\Objects
 * @version 1.0.0
 */
namespace Framework\Dominator\Objects;

/**
 * SCRIPT
 *
 * This is the framework script tag object it is used to normalize the creation of script tags
 */
class SCRIPT{

  /**
   * The tagname
   * @var     string
   * @access  private
   */
  private $tag = 'script';

  /**
   * The src attribute
   * @var     string
   * @access  private
   */
  private $src;

  /**
   * The type attribute
   * @var     string
   * @access  private
   */
  private $type = 'text/javascript';

  /**
   * Construct the tag
   * @method  __construct
   * @param   string      $src  The src for this script tag
   * @return  this              Return this to enable method chaining
   * @access  public
   */
  public function __construct( $src ){
    $this->src = $src;
    return $this;
  }

  /**
   * Return the html for this tag
   * @method  html
   * @return  string  Return the html needed to render this tag
   * @access public
   */
  public function html(){
    return "<{$this->tag} src='{$this->src}' type='{$this->type}'></{$this->tag}>";
  }
}
