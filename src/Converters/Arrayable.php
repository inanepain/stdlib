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
 * Array Export Interface
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.0
 */
interface Arrayable {
    /**
     * Return Array representation of data
     *
     * @return array as Array
     */
    public function toArray(): array;
}
