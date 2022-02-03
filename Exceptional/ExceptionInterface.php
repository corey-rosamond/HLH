<?php
namespace Framework\Exceptional;

/**
 * Exception Interface
 *
 * the interface for all exceptions
 * @package Framework\Exceptional
 * @version 1.0.0
 */
interface ExceptionInterface
{
    /* Protected methods inherited from Exception class */
    public function getMessage(); // Exception message
    public function getCode(); // User-defined Exception code
    public function getFile(); // Source filename
    public function getLine(); // Source line
    public function getTrace(); // An array of the backtrace()
    public function getTraceAsString(); // Formated string of trace
    /* Overrideable methods inherited from Exception class */
    public function __toString(); // formated string for display
    /**
     * @param $message
     * @param $code
     * @param $severity
     * @param $filename
     * @param $lineno
     * @param $previous
     */
    public function __construct($message, $code = 0, $severity = 1, $filename = __FILE__,
        $lineno = __LINE__, $previous = null);
}
