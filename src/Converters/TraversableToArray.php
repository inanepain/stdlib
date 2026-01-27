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

namespace Inane\Stdlib\Converters;

use Iterator;
use Traversable;
use const true;

/**
 * Traversable To Array Trait
 *
 * @version 0.1.0
 */
trait TraversableToArray {
    /**
     * Recursively converts a Traversable object to an array.
     *
     * Iterates through the given Traversable, converting all nested Traversable instances
     * to arrays as well. Optionally preserves keys if $use_keys is true.
     *
     * @param Traversable $iterator The traversable object to convert.
     * @param bool $use_keys Whether to preserve keys in the resulting array.
     *
     * @return array The resulting array representation of the traversable.
     */
    protected static function iteratorToArrayDeep(Traversable $iterator, bool $use_keys = true): array {
        $array = [];
        foreach ($iterator as $key => $value) {
            if ($value instanceof Iterator) $value = static::iteratorToArrayDeep($value, $use_keys);

            if ($use_keys) $array[$key] = $value;
            else $array[] = $value;
        }

        return $array;
    }
}
