<?php

/** Inane: Stdlib
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
 * Arrayable
 * 
 * Array Export Interface. Methods to export object as a standard array type.
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.1
 */
interface Arrayable {
    /**
     * Return Array representation of data
     *
     * @return array as Array
     */
    public function toArray(): array;
}
