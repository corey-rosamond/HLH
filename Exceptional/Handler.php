<?php
/**
 * Handler
 *
 * Exception Handler
 * @package Framework\Exceptional\Handler
 * @version 1.0.0
 */
namespace Framework\Exceptional;

/**
 * Framework\Exceptional\Handler
 *
 * This is the main exception handler it supports both standard Exceptions
 * @todo find a faster way to identiey the exception type
 */
class Handler
{
  /**
   * Try and rethrow the exception to something more specific
   * @method E_NOTICE_handler
   * @param $severity
   * @param $message
   * @param $filename
   * @param $lineno
   * @param $previous
   * @throws \Framework\Exceptional\ArrayToStringConversion
   * @throws \Framework\Exceptional\NoticeException
   * @throws \Framework\Exceptional\PropertyOfNoneObjectException
   * @throws \Framework\Exceptional\UndefinedConstantException
   * @throws \Framework\Exceptional\UndefinedIndexException
   * @throws \Framework\Exceptional\UndefinedVariableException
   */
  public static function E_NOTICE_handler( $severity, $message, $filename, $lineno, $previous )
  {

    if('Array to string conversion' === substr($message, 0, 26)) {
      throw new ArrayToStringConversion($message, E_NOTICE, $severity, $filename, $lineno, $previous);
    }

    if('Undefined index:' === substr($message, 0, 16)) {
      throw new UndefinedIndexException($message, E_NOTICE, $severity, $filename, $lineno, $previous);
    }

    if('Undefined variable:' === substr($message, 0, 19)) {
      throw new UndefinedVariableException($message, E_NOTICE, $severity, $filename, $lineno, $previous);
    }

    if ('Use of undefined constant' === substr($message, 0, 25)) {
      throw new UndefinedConstantException($message, E_NOTICE, $severity, $filename, $lineno, $previous);
    }

    if('Trying to get property of non-object' === substr($message, 0, 37)) {
      throw new PropertyOfNoneObjectException($message, E_NOTICE, $severity, $filename, $lineno, $previous);
    }

    throw new NoticeException($message, E_NOTICE, $severity, $filename, $lineno, $previous);
  }

  /**
   * Try and rethrow the exception to something more specific
   * @method E_WARNING_handler
   * @param $severity
   * @param $message
   * @param $filename
   * @param $lineno
   * @param $previous
   * @throws \Framework\Exceptional\IncludeOnceException
   * @throws \Framework\Exceptional\MissingArgumentException
   * @throws \Framework\Exceptional\WarningException
   */
  public static function E_WARNING_handler($severity, $message, $filename, $lineno, $previous)
  {

    if('include_once(' === substr($message, 0, 13)){
      throw new IncludeOnceException($message, E_WARNING, $severity, $filename, $lineno, $previous);
    }

    if('Missing argument' === substr($message, 0, 16)){
      throw new MissingArgumentException($message, E_WARNING, $severity, $filename, $lineno, $previous);
    }

    throw new WarningException($message, E_WARNING, $severity, $filename, $lineno, $previous);
  }

  /**
   * Try and rethrow the exception to something more specific
   * @method E_RECOVERABLE_ERROR_handler
   * @param $severity
   * @param $message
   * @param $filename
   * @param $lineno
   * @param $previous
   * @throws \Framework\Exceptional\BadTypeExcetpion
   * @throws \Framework\Exceptional\RecoverableErrorException
   */
  public static function E_RECOVERABLE_ERROR_handler($severity, $message, $filename, $lineno, $previous)
  {
    if( strpos($message, 'must be an instance of ') ){
      throw new BadTypeExcetpion( $message, E_RECOVERABLE_ERROR, $severity, $filename, $lineno, $previous );
    }
    throw new RecoverableErrorException($message, E_RECOVERABLE_ERROR, $severity, $filename, $lineno, $previous);
  }

  /**
   * Handle standard exceptions
   * @method StandardException
   * @param        $severity
   * @param        $message
   * @param string $filename
   * @param int    $lineno
   * @param null   $previous
   * @throws \Framework\Exceptional\ArrayToStringConversion
   * @throws \Framework\Exceptional\BadTypeExcetpion
   * @throws \Framework\Exceptional\CompileErrorException
   * @throws \Framework\Exceptional\CoreErrorException
   * @throws \Framework\Exceptional\CoreWarningException
   * @throws \Framework\Exceptional\DeprecatedException
   * @throws \Framework\Exceptional\E_PARSException
   * @throws \Framework\Exceptional\ErrorException
   * @throws \Framework\Exceptional\IncludeOnceException
   * @throws \Framework\Exceptional\MissingArgumentException
   * @throws \Framework\Exceptional\NoticeException
   * @throws \Framework\Exceptional\PropertyOfNoneObjectException
   * @throws \Framework\Exceptional\RecoverableErrorException
   * @throws \Framework\Exceptional\StandardException
   * @throws \Framework\Exceptional\StrictException
   * @throws \Framework\Exceptional\UndefinedConstantException
   * @throws \Framework\Exceptional\UndefinedIndexException
   * @throws \Framework\Exceptional\UndefinedVariableException
   * @throws \Framework\Exceptional\UserDeprecatedException
   * @throws \Framework\Exceptional\UserErrorException
   * @throws \Framework\Exceptional\UserNoticeException
   * @throws \Framework\Exceptional\UserWarningException
   * @throws \Framework\Exceptional\WarningException
   */
  public static function StandardException( $severity, $message, $filename = __FILE__, $lineno = __LINE__, $previous = null )
  {
    switch($severity){
      case E_ERROR:
          throw new ErrorException($message, E_ERROR, $severity, $filename, $lineno, $previous);
        break;

      case E_WARNING:
          self::E_WARNING_handler($severity, $message, $filename, $lineno, $previous);
        break;

      case E_PARSE:
          throw new E_PARSException($message, E_PARSE, $severity, $filename, $lineno, $previous);
        break;

      case E_NOTICE:
          self::E_NOTICE_handler($severity, $message, $filename, $lineno, $previous);
        break;

      case E_CORE_ERROR:
          throw new CoreErrorException($message, E_CORE_ERROR, $severity, $filename, $lineno, $previous);
        break;

      case E_CORE_WARNING:
          throw new CoreWarningException($message, E_CORE_WARNING, $severity, $filename, $lineno, $previous);
        break;

      case E_COMPILE_ERROR:
          throw new CompileErrorException($message, E_COMPILE_ERROR, $severity, $filename, $lineno, $previous);
        break;

      case E_COMPILE_WARNING:
          throw new CoreWarningException($message, E_COMPILE_WARNING, $severity, $filename, $lineno, $previous);
        break;

      case E_USER_ERROR:
          throw new UserErrorException($message, E_USER_ERROR, $severity, $filename, $lineno, $previous);
        break;

      case E_USER_WARNING:
          throw new UserWarningException($message, E_USER_WARNING, $severity, $filename, $lineno, $previous);
        break;

      case E_USER_NOTICE:
          throw new UserNoticeException($message, E_USER_NOTICE, $severity, $filename, $lineno, $previous);
        break;

      case E_STRICT:
          throw new StrictException($message, E_STRICT, $severity, $filename, $lineno, $previous);
        break;

      case E_RECOVERABLE_ERROR:
          self::E_RECOVERABLE_ERROR_handler($severity, $message, $filename, $lineno, $previous);
        break;

      case E_DEPRECATED:
          throw new DeprecatedException($message, E_RECOVERABLE_ERROR, $severity, $filename, $lineno, $previous);
        break;

      case E_USER_DEPRECATED:
          throw new UserDeprecatedException($message, E_DEPRECATED, $severity, $filename, $lineno, $previous);
        break;

      default:
          throw new StandardException($message, E_ALL, $severity, $filename, $lineno, $previous);
        break;
    }
  }


  /**
   * Handle fatal exceptions
   * @method FatalException
   */
  public static function FatalException()
  {
    $exception = error_get_last();
    if( !is_null( $exception ) ){
      \Framework\Core\Architect::getInstance()->fatalError($exception);
      exit();
    }
  }
}
