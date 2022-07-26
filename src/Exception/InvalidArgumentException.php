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

use Inane\Stdlib\Exception\LogicException;

/**
 * Exception thrown if an argument is not of the expected type.
 *
 * @package Inane\Exception
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.3.0
 */
class InvalidArgumentException extends LogicException implements ExceptionInterface {
    // base code for invalid argument exceptions
    protected $code = 750;
}
