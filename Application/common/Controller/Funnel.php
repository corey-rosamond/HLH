<?php
/**
 * Funnel
 *
 * @package Framework\Commander
 * @version 1.0.0
 */
namespace Framework\Commander;

/**
 * 	Funnel
 *
 *	This is the main interfase for all funnel related tasks
 *
 * 	$_  Variables and methods are private none useable objects and datamembers for the controller only.
 * 	$_p Variables are pointers to a data set in use at that moment
 * 	$_m Variables are containers for modals or will be
 * 	$_c Variables are containers for controller objects or will be
 *  $_d Variables are data stoarge objects for some type of data this controller uses
 */
class Funnel extends \Framework\Support\Abstracts\Singleton
{

  private $_mFunnel;

  private $_dFunnel;

  /**
   * The intial public construct is is only an entry point it calls the
   * private constructor and wraps it in the try catch block with the router
   * @method  construct
   * @param   int    $orderKey  Int order key or false
   * @return  Order             An instances of the controller
   * @access  public
   */
  public static function construct( $key = false )
  {
    $instance = self::getInstance();
    $instance->_mFunnel = \Framework\Core\Modulus::get( 'Tracking', "Holylandhealth" );
    return $instance;
  }

  /**
   * Load a funnel with its funnel key
   * @method  load
   * @param   int    $key  Key of the funnel we are loading
   * @return  void
   * @uses    \Framework\Commander\eThrow()
   * @throws
   * @access  private
   */
  public function load( $key )
  {
    try {
      if( !isset($this->_mFunnel) ){
        $this->_mFunnel = \Framework\Core\Modulus::get( 'Funnel', "Holylandhealth" );
      }
      return $this->_dFunnel = $this->_mFunnel->get( $key );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      $this->_eThrow( self::_emLoad, self::_eeFatal, $exception );
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       EXCEPTION HANDLING                                            */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * The exception prefix to be used in all exception messages
   * @var     string    $_ePrefix
   * @access  private
   */
  private $_ePrefix = '[FunnelController]: ';

  /**
   * Constants for all possible exception messages
   *
   */
  const _emConstructor                = 100;
  const _emLoad                       = 101;



  /**
   * Return the exception message for the code that was provided
   * @method  _eMessage
   * @param   integer     $code     Code we want the message for
   * @return  string                The exception message specific to that error code or the default
   * @access  private
   */
  private function _eMessage( $code = 0 )
  {
    switch( $code ){
      case self::_emConstructor:    return "{$this->_ePrefix} Construtor Exception fatal!";         break;
      case self::_emLoad:           return "{$this->_ePrefix} ";                                    break;

      default:                      return "{$this->_ePrefix} Default Exception fatal!";            break;
    }
  }

  /**
   * Route a thrown exception to an event
   * @method  _eRouter
   * @param   \Framework\Exceptional\BaseException  $exception
   * @return  void
   * @access  private
   * @todo    Finish the router
   */
  private function _eRouter( $exception )
  {
    switch( $exception->getCode() ){
      default:
        \Framework\Core\Architect::fallenException(
          'Common\OrderController',
          'Order Controller Exception',
          $exception->getMessage(),
          $exception->getFile(),
          $exception->getLine()
        );
      break;
    }
  }

  /**
   * Rethrow an exception to an exception more specific to the current controller
   * @method  _eThrow
   * @param   string                                $message   The exception message you want to rethrow with
   * @param   int                                   $code      The controller specific error code you want to throw
   *                                                           this exception with this will most likely related to an
   *                                                           exception event. Allowing the controller to recover from
   *                                                           exceptions without ever needing to ask for help
   * @param   \Framework\Exceptional\BaseException  $exception The core exception object that was orinally thrown
   * @return  void
   * @access  private
   */
  private function _eThrow( $message, $code, $exception )
  {
    /** Check if this is an interger */
    if( is_int( $message ) ){
      /** This is an integer that means it is a message for this sytem get it from the message function */
      $message = $this->_eMessage( $message );
    }
    throw new \Framework\Exceptional\OrderControllerException(
      $message,
      $code,
      $exception->getCode(),
      $exception->getFile(),
      $exception->getLine(),
      $exception
    );
  }
}
