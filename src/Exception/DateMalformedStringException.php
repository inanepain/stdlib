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
 * Thrown when an invalid Date/Time string is detected.
 *
 * Parent class of Date/Time exceptions, for issues that come to light due to user input or free form text arguments that need to be parsed.
 *
 * @implements ExceptionInterface
 * @version 0.1.0
 */
class DateMalformedStringException extends DateException {
    /**
     * Exception code for DateMalformedStringException.
     */
    protected $code = 810;
}
