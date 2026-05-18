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

use ArrayAccess;
use Iterator;

use function array_key_exists;
use function is_array;

/**
 * Provides functionality to merge arrays with customizable merge behaviors.
 */
trait MergeTrait {
    /**
     * Sets how and what is merged.
     */
    public MergeMethod $mergeMethod = MergeMethod::UpdateOnly;

    /**
     * Merges source arrays into a target array based on the specified merge method.
     *
     * @param MergeMethod $mergeMethod The merge behavior to apply (e.g., UpdateOnly, AddOnly).
     * @param Iterator|array       $target      The target array that will be updated or supplemented.
     * @param Iterator|array       ...$sources  One or more source arrays to merge into the target array.
     *
     * @return Iterator|array The resulting array after merging the source arrays into the target array.
     */
    public static function mergeOptionsWithMethod(MergeMethod $mergeMethod, Iterator|array $target, Iterator|array ...$sources): Iterator|array {
        foreach($sources as $source) {
            foreach($source as $key => $sourceValue) {
                if (is_array($target)) $targetHasKey = array_key_exists($key, $target);
                elseif ($target instanceof ArrayAccess) $targetHasKey = $target->offsetExists($key);
                else $targetHasKey = isset($target[$key]);

                if (($mergeMethod === MergeMethod::AddOnly && $targetHasKey) || ($mergeMethod === MergeMethod::UpdateOnly && !$targetHasKey)) {
                    continue;
                }

                $targetValue = $target[$key] ?? null;
                $isObject = (is_array($sourceValue) || $sourceValue instanceof ArrayAccess) && (is_array($targetValue) || $targetValue instanceof ArrayAccess);

                if ($isObject) {
                    $target[$key] = static::mergeOptionsWithMethod(
                        $mergeMethod,
                        $targetValue,
                        $sourceValue,
                    );
                } else {
                    $target[$key] = $sourceValue;
                }
            }
        }

        return $target;
    }

    /**
     * Merges options from multiple sources into a target using the "Add Only" strategy.
     * This method adds only the keys that do not already exist in the target.
     *
     * @param Iterator|array $target     The target container where the options will be merged into.
     * @param Iterator|array ...$sources One or more source containers to merge options from.
     *
     * @return Iterator|array The target container after merging the options.
     */
    public static function mergeOptionsWithAddOnly(Iterator|array $target, Iterator|array ...$sources): Iterator|array {
        return static::mergeOptionsWithMethod(MergeMethod::AddOnly, $target, ...$sources);
    }

    /**
     * Merges options from multiple sources into a target using the "Update Only" strategy.
     * This method updates only the keys that already exist in the target.
     *
     * @param Iterator|array $target     The target container where the options will be merged into.
     * @param Iterator|array ...$sources One or more source containers to merge options from.
     *
     * @return Iterator|array The target container after merging the options.
     */
    public static function mergeOptionsWithUpdateOnly(Iterator|array $target, Iterator|array ...$sources): Iterator|array {
        return static::mergeOptionsWithMethod(MergeMethod::UpdateOnly, $target, ...$sources);
    }

    /**
     * Merges source arrays into a target array, adding new elements and updating existing ones.
     *
     * @param Iterator|array $target     The target array that will be updated with new elements or supplemented.
     * @param Iterator|array ...$sources One or more source arrays to merge into the target array.
     *
     * @return Iterator|array The resulting array after merging the source arrays into the target array.
     */
    public static function mergeOptionsWithAddAndUpdate(Iterator|array $target, Iterator|array ...$sources): Iterator|array {
        return static::mergeOptionsWithMethod(MergeMethod::AddAndUpdate, $target, ...$sources);
    }

    /**
     * Merges source iterators into the target iterator using the pre-defined merge method.
     *
     * @param Iterator|array $target     The target iterator to be updated with merged values.
     * @param Iterator|array ...$sources One or more source iterators to merge into the target iterator.
     *
     * @return Iterator|array The resulting iterator or array after merging the source iterators into the target.
     */
    public function mergeOptions(Iterator|array $target, Iterator|array ...$sources): Iterator|array {
        return static::mergeOptionsWithMethod($this->mergeMethod, $target, ...$sources);
    }
}
