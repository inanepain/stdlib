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
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\stdlib
 * @category stdlib
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Merge;

use Iterator;

/**
 * Defines methods for merging arrays, with optional customization using a merge method strategy.
 */
interface MergeInterface {
    /**
     * Merges source arrays into a target array based on the specified merge method.
     *
     * @param MergeMethod $mergeMethod The merge behavior to apply (e.g., UpdateOnly, AddOnly).
     * @param Iterator|array       $target      The target array that will be updated or supplemented.
     * @param Iterator|array       ...$sources  One or more source arrays to merge into the target array.
     *
     * @return Iterator|array The resulting array after merging the source arrays into the target array.
     */
    public static function mergeOptionsWithMethod(MergeMethod $mergeMethod, Iterator|array $target, Iterator|array ...$sources): Iterator|array;

    /**
     * Merges source arrays into the target array.
     *
     * @param Iterator|array $target
     * @param Iterator|array ...$sources
     *
     * @return Iterator|array
     */
    public function mergeOptions(Iterator|array $target, Iterator|array ...$sources): Iterator|array;
}
