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

use Exception as SystemException;
use Throwable;

/**
 * Exception
 *
 * @version 0.3.2
 */
class Exception extends SystemException implements ExceptionInterface {
    /**
     * Custom construct template
     *
     * @param string          $message  error description
     * @param int|mixed             $code     unique identifier
     * @param \Throwable|null $previous error if any
     *
     * @return void
     */
    public function __construct(string $message = '', $code = 0, ?Throwable $previous = null) {
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
