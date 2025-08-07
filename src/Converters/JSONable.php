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
 * @version $version
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
