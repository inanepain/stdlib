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

use Inane\Stdlib\Exception\LogicException;

use function array_unshift;
use function explode;
use function str_contains;
use function str_replace;
use const null;

/**
 * Magic GET/SET reject property
 *
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.3.0
 */
class InvalidPropertyException extends LogicException {
    protected $message = 'Property exception: `magic_property_properties` property invalid';   // exception message
    protected $code = 360;                        // user defined exception code

    /**
     * __construct
     *
     * message: Object:=message
     *  split on := with [0] replacing `Object` and [1] the message
     *
     * @param null|string $message error message
     * @param int $code
     * @param Exception|null $previous
     * @return void
     */
    public function __construct(?string $message = null, $code = 0, Exception $previous = null) {
        if ($previous === null) {
            $values = [$message];
            if (str_contains($message, ':=')) {
                $values = explode(':=', $message);
            } else array_unshift($values, 'Object');
            $this->message = str_replace('magic_property_properties', $values[0], $this->message);
        }
        $message = $this->message . ($values[1] ? ': ' . $values[1] : '');
        $code = $this->code + $code;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}
