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

namespace Inane\Stdlib\Converters;

/**
 * Arrayable
 *
 * Array Export Interface. Methods to export object as a standard array type.
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
