<?php
/**
 * View
 *
 * @package Framework\Dominator
 * @version 1.2.0
 */
namespace Framework\Dominator;

/**
 * View
 *
 * This is the main view abstract
 * by edxtending this you get get all of the
 * tools needed to create and render a view
 */
abstract class View
{
  /**
   * The constant for app resources
   * @var     int
   * @access  private
   */
  const appResource         = 0;

  /**
   * The constant for Direct Resources
   * @var     int
   * @access  private
   */
  const directResource      = 1;

  /**
   * The constant for framework resources
   * @var     int
   * @access  private
   */
  const frameworkResource   = 2;

  /**
   * The styles collection object
   * @var     \Framework\Dominator\Styles
   * @access  private
   */
  private $styles;

  /**
   * The scripts collection object
   * @var     \Framework\Dominator\Styles
   * @access  private
   */
  private $scripts;

  /**
   * Do we want the scripts in the header
   * @var     boolean
   * @access  private
   */
  private $scriptsInHeader  = false;

  /**
   * Do we want the scripts in the footer
   * @var     boolean
   * @access  private
   */
  private $scriptsInFooter = true;

  /**
   * Construct
   * @method  __construct
   * @return  this             Return a reference to this for method chaining
   * @access  public
   */
  public function __construct()
  {
    $this->resourceLocation = \Framework\Core\Architect::getResourceLocation();
    /** @var Tokens collection object for view tokens */
    $this->tokens   = new Tokens();
    /** @var Styles collection object for style sheets */
    $this->styles   = new Styles( $this->resourceLocation );
    /** @var Scripts collection object for javascript files */
    $this->scripts  = new Scripts( $this->resourceLocation );
    /** return a copy of ourself for late state binding */
    return $this;
  }

  /**
   * Render the style sheet objects
   * @method  render
   * @return  void
   * @access  public
   */
  public function render()
  {
    /** start the html doc */
    self::HtmlStart();
    /** Call the registerd build function */
    $this->build();
    /** end the html doc */
    self::htmlStop();
    /** render the document */
    Document::render();
  }

  /**
   * Add a style sheet for this view
   * @method  addCss
   * @param   string    $sheet    Style sheet to include
   * @param   int       $type     The resource type index this tells the collection where to get this resource
   * @return  this                Return a reference to this for method chaining
   * @access  public
   */
  public function addCss( $sheet, $type = self::appResource )
  {
    $this->styles[] = [ 'location' => $sheet, 'type' => $type ];
    return $this;
  }

  /**
   * Add javascript file for this view
   * @method  addJavascript
   * @param   string    $script   The script to include
   * @param   int       $type     The resource type index this tells the collection where to get this resource
   * @return  this                Return a reference to this for method chaining
   * @access  public
   */
  public function addJavascript( $script, $type = self::appResource )
  {
    $this->scripts[] = [ 'location' => $script, 'type' => $type ];
    return $this;
  }

  /**
   * Add a token for this view
   * @method  addToken
   * @param   int       $key    The key of the token key to add
   * @param   string    $value  The value of the token
   * @return  this              Return a reference to this for method chaining
   * @access  public
   */
  public function addToken( $key, $value )
  {
    $this->tokens[$key] = $value;
    return $this;
  }

  /**
   * Add the html start to the document
   * If the scriptsInHeader is set we will add the script files here
   * @method  htmlStart
   * @return  this      Return this to enable method chaining
   * @access  protected
   */
  protected function htmlStart()
  {
    /** Start the html document */
    self::write("<!DOCTYPE html><html lang='en'><head><meta charset='utf-8' /><title>" . $this->tokens['title'] . "</title><meta http-equiv='X-UA-Compatible' content='IE=edge'>\n");
    /** if they want a no follow add it */
    if ($this->tokens['no-follow']) {
      /** No follow is set so add the no follow tag to the document */
      self::write("<meta name='robots' content='NOINDEX, NOFOLLOW' />");
    }
    /** Write the style sheets to the document */
    $this->styles->write();
    /** Check if scriptsInHeader is enabled */
    if( $this->scriptsInHeader ){
      /** ScriptsInHeader is set add the script files here */
      $this->scripts->write();
    }
    /** write the the end of the header and start of the body */
    self::write("</head><body class='" . $this->tokens['body-class'] . "'>");
    /** return ourself for method chaining */
    return $this;
  }

  /**
   * Add the stop element to the document content
   * If scriptsInFooter is enabled we will also add the script files here
   * @method  htmlStop
   * @return  this     Return this to enable method chaining
   * @access  protected
   */
  protected function htmlStop()
  {
    /** Check if scriptsInFooter is set */
    if( $this->scriptsInFooter ){
      /** scriptsInFooter set write the scripts here */
      $this->scripts->write();
    }
    self::write("</body></html>");
    return $this;
  }

  /**
   * Write some content to the document buffer
   * @method  write
   * @param   string $content The content we want to add to the document buffer
   * @return  void
   * @access  protected
   * @static
   */
  final protected static function write( $content )
  { \Framework\Dominator\Document::getInstance()->write($content); }

  /**
   * Tell the view to render the script includes in the header
   * @method  scriptsInHeader
   * @return  this            Return this to enable method chaining
   * @access public
   */
  public function scriptsInHeader(){
    $this->scriptsInHeader = true;
    $this->scriptsInFooter = false;
    return $this;
  }

  /**
   * Tell the view to render the script includes in footer
   * @method  scriptsInFooter
   * @return  this            Return this to enable method chaining
   * @access  public
   */
  public function scriptsInFooter(){
    $this->scriptsInheader = false;
    $this->scriptsInFooter = true;
  }

  /**
   * All views need a build method it is the main process that framework uses to
   * render the view itself
   * @method  build
   * @return  void
   * @access  protected
   */
  abstract protected function build();
}
