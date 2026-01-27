<?php

/**
 * Inane: Stdlib
 *
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package inanepain\stdlib
 * @category stdlib
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\Stdlib\Exception;

/**
 * ExampleException
 *
 * Example of new Exception
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
