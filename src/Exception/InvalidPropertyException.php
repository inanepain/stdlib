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
 * Magic GET/SET reject property
 *
 * @package Inane\Exception
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.2.0
 */
class InvalidPropertyException extends LogicException implements ExceptionInterface {
    protected $message = 'Property exception: `magic_property_properties` property invalid';   // exception message
    protected $code = 200;                        // user defined exception code

    /**
     * __construct
     *
     * @param null|string $message
     * @param int $code
     * @param Exception|null $previous
     * @return void
     */
    public function __construct(?string $message = null, $code = 0, Exception $previous = null) {
        if ($previous === null) $this->message = str_replace('magic_property_properties', 'Object', $this->message);
        $message = $this->message . ($message ? ': ' . $message : '');
        $code = $this->code + $code;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}
