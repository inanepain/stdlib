<?php

/**
 * Inane: Stdlib
 *
 * Common classes, tools and utilities used throughout the inanepain libraries.
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
 * Exception thrown if an argument is not of the expected type.
 *
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.3.0
 */
class InvalidArgumentException extends LogicException {
    // base code for invalid argument exceptions
    protected $code = 355;
}
