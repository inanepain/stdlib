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
 * @category converter
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\Converters;

/**
 * JSON Export Interface
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.0
 */
interface JSONable {
    /**
     * Return JSON representation of data
     *
     * @return array as JSON
     */
    public function toJSON(): string;
}
