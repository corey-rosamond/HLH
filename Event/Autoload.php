<?php
\Framework\_IncludeCorrect(FRAMEWORK_ABSTRACT."Singleton.php");
\Framework\_IncludeCorrect(FRAMEWORK_OBJECT."ArrayCollection.php");
/**
 * The auto load object uses the class name to
 * include the resource that was requested
 * @package Framework\Events
 * @version 1.0.0
 * @todo Add support for files with extensions
 * @todo Work out a way to make this in the framework namespace
 * @todo Improve the removal of framework from the namespace
 **/
class Autoload extends \Framework\Support\Abstracts\Singleton
{
  private $initalized = false;
  private $loaded;

  /**
   * Load a file using its namespace
   * @method  file
   * @param   string    $target   Target class to include
   * @return  boolean             ALWAYS TRUE OR PHP CAN THROW A FATAL EXCEPTION
   * @access  public
   * @static
   */
  public static function file( $target )
  {
    try{
      $instance = self::construct();
      list( $directory, $class ) = explode( "\\", $target, 2);
      $fullpath = $instance->fullpath( $directory, $class );
      try{ \Framework\_IncludeCorrect( $fullpath );
      } catch ( \Framework\Exceptional\IncludeOnceException $exception ){
        throw new \Framework\Exceptional\AutoloadFault( "Failed to include File($fullpath) no such file or directory!" );
      }
      if(!class_exists($target)){
        throw new \Framework\Exceptional\AutoloadFault( "Included File($fullpath) $target still undefined!" );
      }
      //$instance->loaded->add( $fullpath );
      return true;
    } catch ( \Framework\Exceptional\AutoloadFault $exception ){
      throw new \Framework\Exceptional\AutoloadFault(
        $exception->getMessage(), $exception->getCode(), $exception->getSeverity(), $exception->getFile(), $exception->getLine(), $exception );
    }
  }

  /**
   * Get an instances of autoload if initalized is true just return the instances
   * if initalized is false add the base components then return it
   * @method  construct
   * @return  \Autoload
   * @access  private
   * @static
   */
  private static function construct()
  {
    $instance = Autoload::getInstance();
    if( $instance->initalized ){
      return $instance;
    }
    //$instance->loaded     = new \Framework\Support\Object\ArrayCollection();
    $instance->initalized = true;
    return $instance;
  }

  /**
   * Gets the file path based on the name space
   * @method  fullpath
   * @param   string   $target  The target class name we want to include
   * @return  string            Assumed path
   * @access  private
   */
  private function fullpath( $directory, $class ){
    if( $directory === "App" ){
      return APP_ROOT."{$class}.php";
    }
    return FRAMEWORK_ROOT."{$class}.php";
  }
}
spl_autoload_register( 'Autoload::file' );
