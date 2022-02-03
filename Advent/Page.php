<?php
/**
 * Page
 *
 * @package Framework\Advent
 * @version 1.0.0
 */
namespace Framework\Advent;

/**
 * Page
 *
 * This is the base page object for all page events this enables you to build
 * Out a page event without needing to really know how any of the rest of the
 * System operates
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT."Advent".DSEP."Object".DSEP."MainCollection.php");
\Framework\_IncludeCorrect( FRAMEWORK_ROOT."Advent".DSEP."Event.php");
abstract class Page extends Event{
  /** Position constants */
  const header = 0;       /** Header */
  const footer = 1;       /** Footer */
  /** @var array $tokens */
  protected $tokens;
  /** @var array $styles */
  protected $styles;
  /** @var array $scripts */
  protected $scripts;
  /** @var integer $scriptSection */
  protected $scriptSection = self::footer;
  /** @var array $breadCrumb */
  protected $breadCrumb = [];
  /** @var string $body */
  protected $body;
  /** @var boolean $noFollow */
  protected $noFollow = false;

  /**
   * @param array $paramaters
   * @return \Framework\Advent\Page
   * @throws \Framework\Exceptional\AdventFault
   */
  public static function initalize( array $paramaters = array() ){
    try{
      /** @var $instance Instances of the page object */
      $instance = self::getInstance();
      /** @var /Framework/Advent/Event $instance This is an instance of the base event */
      parent::initalize( $paramaters );
      /** Set the body buffer */
      $instance->clearBody();
      /** @var \Framework\Advent\Object\MainCollection $tokens  Tokens that for page content */
      $instance->tokens   = new \Framework\Advent\Object\MainCollection();
      /** @var \Framework\Advent\Object\MainCollection $styles  Page style sheets */
      $instance->styles   = new \Framework\Advent\Object\MainCollection();
      /** @var \Framework\Advent\Object\MainCollection $scripts Script files for this page */
      $instance->scripts  = new \Framework\Advent\Object\MainCollection();
      /** Basic page tokens that every page has */
      $instance->tokens->add( 'title', 'Document Title' );
      $instance->tokens->add( 'header', 'Document header' );
      $instance->tokens->add( 'sub-header', 'Document sub header' );
      $instance->tokens->add( 'body-class', false );
      $instance->tokens->add( 'image-directory', $instance->location[ self::application ][ self::image ] );
    } catch( \Framework\Exceptional\BaseException $exception ){
      /**
       * We failed to load the page throw an exception so someone
       * Smarter than us can fix the issue and load a page
       */
      throw new \Framework\Exceptional\AdventFault(
        $instance->exceptionMessage[self::excptError],
        self::eventError,
        self::eventError,
        $exception->getFile(),
        $exception->getLine(),
        $exception
      );
    }
    return $instance;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        SET BASIC TOKENS                                             */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  protected function setDocumentTitle( $text ){
    $this->tokens->set('title', $text);
  }

  protected function setPageHeader( $text ){
    $this->tokens->set('header', $text);
  }

  protected function setPageSubHeader( $text ){
    $this->tokens->set('sub-header', $text);
  }

  /**
   * Set the body class name
   * @method  setBodyClass
   * @param   string $text
   * @return  void
   * @access  protected
   */
  protected function setBodyClass( $text ){
    $this->tokens->set('body-class', $text);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        ADD SINGLE ASSET                                             */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Add Single Script file
   * @method  addScript
   * @param   string    $script
   * @param   string    $location
   * @uses    ScriptFile->add()
   * @return  void
   * @access  protected
   */
  protected function addScript( $file, $location )
  {
    $this->scripts->add( [$file, $location] );
  }

  /**
   * Add Single style sheet
   * @method  addStyle
   * @param   string   $style
   * @param   string   $location
   * @return  Page
   * @uses    styles->add()
   * @access  protected
   */
  protected function addStyle( $file, $location )
  {
    $this->styles->add( [$file, $location] );
  }

  /**
   * Add Single token
   * @method  addToken
   * @param   string   $token The token you want to set
   * @param   string   $value The value of the token
   * @return  object          A reference to the added token
   * @uses    Page->add()
   * @access  protected
   */
  protected function addToken($token, $value)
  {
    return $this->tokens->add($token, $value);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        ADD ASSET MULTIPLE                                           */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Add Multiple script files
   * @method  addScripts
   * @param   [][]     $scripts
   * @return  Page
   * @uses    Page->addJavaScript()
   * @access  protected
   */
  protected function addScripts( $scripts )
  {
    if( !is_array( $scripts )){ return false; }
    array_map(
      function( $arguments ){
        list( $file, $location ) = $arguments;
        $this->addScript( $file, $location );
      }, $scripts );
    return $this;
  }

  /**
   * Add Multiple style sheets
   * @method  addStyles
   * @param   [][]    $styles
   * @return  Page
   * @uses    Page->addStyleSheet()
   * @access  protected
   */
  protected function addStyles( $styles ){
    if( !is_array( $styles ) ){ return false; }
    array_map( function( $arguments ){
      list( $file, $location ) = $arguments;
      $this->addStyle( $file, $location );
    }, $styles );
    return $this;
  }

  /**
   * Add Multiple tokens to the collection
   * @method  addTokens
   * @param   array   $tokens   The token you want to set
   * @return  Page
   * @uses    Page->addToken()
   * @access  protected
   */
  protected function addTokens($tokens){
    if(!is_array($tokens)){ return false; }
    array_map(
      function($token, $value){
        $this->addToken($token, $value);
      }, array_keys($tokens), $tokens);
    return $this;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        RENDER METHODS                                               */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Render this page
   * @method  render
   * @return  void      No need to return the page has been output
   * @uses    Page->startHTML()
   * @uses    Page->documentBody()
   * @uses    Page->endHTML()
   * @uses    Page->processTokens()
   * @uses    Page->chunkRender()
   */
  public function render()
  {
    $this->startHTML( $this->tokens, $this->noFollow );
    $this->write( $this->getBody() );
    $this->endHTML();
    $this->chunkRender();
  }

  /**
   * Append the start of the html document to the page
   * @method  startHTML
   * @return  Page
   * @uses    Page->write()
   * @uses    styles->each()
   * @uses    ScriptFile->generageTags()
   * @access  private
   */
  private function startHTML( $tokens="", $noFollow ){
    $this->write( "<!DOCTYPE html>\n<html lang='en'>\n<head>\n<meta charset='utf-8' />\n<title>{$tokens->title}</title>\n<meta http-equiv='X-UA-Compatible' content='IE=edge'>\n");
    $this->styles->map(
      function( $arguments ){
        list( $file, $location ) = $arguments;
        $location = $this->assetPath( $location, self::style );
        $this->write( sprintf( "<link rel='stylesheet' type='text/css' href='%s%s'/>\n", $location, $file ) );
      }
    );
    $bodyClass = $this->tokens['body-class'];
    if($bodyClass){
      $this->write("</head>\n<body class=\"{$bodyClass}\">\n");
      return $this;
    }
    $this->write("</head>\n<body>\n");
    return $this;
  }

  /**
   * Append the end of the html file to the buffer
   * @method  endHTML
   * @return  Page
   * @uses    ScriptFile->generageTags()
   * @uses    Page->write()
   * @return  void
   * @access  private
   */
  private function endHTML(){
    if($this->scriptSection === self::footer){
      $this->scripts->map(
        function( $arguments ){
          list( $file, $location ) = $arguments;
          $location = $this->assetPath( $location, self::script );
          $this->write( sprintf( "<script src='%s%s' type='text/javascript'></script>\n", $location, $file ) );
        }
      );
    }
    $this->write("</body>\n</html>\n");
    return $this;
  }

  /**
   * Loop through the tokes as quickly as we can replacing the tokens with their values in the buffer
   * @method  processTokens
   * @return  Page
   * @uses    Token->processTokens()
   * @access  private
   */
  private function processTokens(){
    $this->tokens->forAll(
      function($token, $value){
        $this->bufffer = str_replace(
          $token,$value,
          $this->bufffer
        );
      }
    );
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        BODY BUFFER METHODS                                          */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Write content to the end of the body
   * @method  writeBody
   * @param   string    $content  This is the content that will be added to the end of body
   * @return  Page
   * @access  protected
   */
  protected function writeBody( $content ){
    $this->body .= $content;
    return $this;
  }

  /**
   * Set the body content and overwrite anything that was previously in it
   * @method  setBody
   * @param   string    $content Content to set body to
   * @access  protected
   */
  protected function setBody($content){
    $this->body = $content;
    return $this;
  }

  /**
   * Get the body content
   * @method  getBody
   * @return  string    Return all of the body content
   * @access  protected
   */
  protected function getBody(){
    return $this->body;
  }

  /**
   * Clear all content out of the body
   * @method  clearBody
   * @return  Page
   * @access  protected
   */
  protected function clearBody(){
    unset($this->body);
    $this->body = '';
    return $this;
  }
}
