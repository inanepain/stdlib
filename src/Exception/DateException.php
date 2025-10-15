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
 * Parent class of Date/Time exceptions, for issues that come to light due to user input, or free form text arguments that need to be parsed.
 *
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.1.0
 */
class DateException extends Exception {
    protected $code = 800;
}
