<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Stdlib
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\Exception;

/**
 * ExampleException
 *
 * Example of new Exception
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.0
 */
class ExampleException extends Exception {
    protected $message = 'Example exception';    // exception message
    protected $code = 100;                       // user defined exception code
    protected string $file;                      // source filename of exception
    protected int $line;                         // source line of exception

    /**
     * Exception constructor
     *
     * Redefine the exception so message isn't optional
     *
     * @param string|null $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(?string $message = null, $code = 0, Exception $previous = null) {
        $message = $this->message . ($message ? ': ' . $message : '');
        $code = $this->code + $code;

        $debugBacktrace = array_pop(debug_backtrace(0, 3));
        $this->file = $debugBacktrace['file'];
        $this->line = $debugBacktrace['line'];

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}
