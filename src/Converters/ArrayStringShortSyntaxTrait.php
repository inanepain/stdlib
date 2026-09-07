<?php

/**
 * Inane: Stdlib
 *
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
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

use function preg_replace;
use function var_export;

/**
 * Array to Code Trait
 *
 * Provides a method to convert an array to its PHP code string representation using short array syntax.
 *
 * @package Inane\Stdlib\Converters
 */
trait ArrayStringShortSyntaxTrait {
    /**
     * Convert an array to its PHP code string representation
     *
     * @param array $array Array to convert
     *
     * @return string
     */
    public static function arrayToString(array $array): string {
        // Convert var_export's 'array()' syntax to short array syntax '[]'
        return preg_replace(
            ['/array \\(/', '/\\)(,?)/'],
            ['[', ']$1'],
            var_export($array, true)
        );
    }
}
