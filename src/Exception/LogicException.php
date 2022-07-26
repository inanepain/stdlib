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

use Inane\Stdlib\Exception\Exception;

/**
 * Exception that represents error in the program logic. This kind of exception should lead directly to a fix in your code.
 *
 * @package Inane\Exception
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.2.0
 */
class LogicException extends Exception implements ExceptionInterface {
}
