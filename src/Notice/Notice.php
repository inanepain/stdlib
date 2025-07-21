<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package Inane\Stdlib
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\Notice;

use Stringable;
use Throwable;

/**
 * Notice
 *
 * Generates a user-level error/warning/notice message
 *
 * @package Inane\Notice
 *
 * @version 0.1.0
 */
class Notice implements NoticeInterface, Stringable {
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
