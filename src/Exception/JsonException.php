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
 * Exception thrown if JSON_THROW_ON_ERROR option is set for Json::encode() or Json::decode(). code contains the error type, for possible values see json_last_error().
 *
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.1.0
 */
class JsonException extends Exception {
    protected $code = 800;
}
