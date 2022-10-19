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

use Exception as SystemException;
use Stringable;
use Throwable;

/**
 * Exception
 *
 * @package Inane\Exception
 *
 * @version 0.3.1
 */
class Exception extends SystemException implements ExceptionInterface, Stringable {
    /**
     * Custom construct template
     *
     * @param string $message error description
     * @param int $code unique identifier
     * @param \Throwable|null $previous error if any
     *
     * @return void
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null) {
        // modifications
        $code = $this->code + $code;

        // make sure everything is assigned properly, call parent construct
        parent::__construct($message, $code, $previous);
    }

    /**
     * toString
     *
     * @return string error as string
     */
    public function __toString(): string {
        $class = __CLASS__;

        return <<<MESSAGE
{$this->getMessage()}:
{$class}[{$this->getCode()}]:
{$this->getFile()}({$this->getLine()}):
{$this->getTraceAsString()}
{$this->getPrevious()}
MESSAGE;
    }
}
