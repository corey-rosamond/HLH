<?php
/**
 * BaseException
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Exceptional;

/**
 * BaseException
 *
 * This is the base for the whole system. It converts exceptions from being bad things to being
 * amazingly useful
 */
class BaseException extends \ErrorException implements ExceptionInterface, \Serializable
{
  /** @var string _sExceptionName */
  protected $_sExceptionName;


  public function __construct($message, $code = 0, $severity = 0, $file = "Undefined!", $line = 0, $previous = null)
  {
    /** @var string _sExceptionName */
    $this->_sExceptionName = get_called_class();
    if (!is_a($previous, 'Exception')){
      $previous = null;
    }
    /** Update the last exception in admit */
    \Framework\Core\Admit::_setLastException($this);
    /** Call the final parent construct this way we get all the functionality we can */
    return parent::__construct($message, $code, $severity, $file, $line, $previous);
  }

  /**
   * Return the exception nam,e
   * @method getName
   * @return string
   */
  public function getName()
  { return $this->_sExceptionName; }

  /**
   * Serialize the exception
   * @method serialize
   * @return string
   */
  public function serialize()
  {
    try{
      return serialize(array($this->validator, $this->arguments, $this->message, $this->file, $this->line, $this->code, $this->severity, print_r($this->getTrace(), true)));
    } catch ( \Framework\Exceptional\BaseException $exception ){
      return print_r($this,true);
    }
  }

  /**
   * Unserialize the exception
   * @method unserialize
   * @param string $exception
   */
  public function unserialize($exception)
  {
    list( $this->validator, $this->arguments, $this->message, $this->file, $this->line, $this->code, $this->severity, $this->trace ) = unserialize( $exception );
  }
}
