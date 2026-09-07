<?php

/**
 * Inane: Stdlib
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
 * $Id$
 * $Date$
 * PHP version 8.5
 *
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\stdlib
 * @category stdlib
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Bitmask;

use function array_filter;
use function array_reduce;

/**
 * Trait for adding bitmask functionality to enums.
 *
 * @version 0.2.0
 */
trait EnumBitmaskTrait {
    /**
     * Parse a given bitmask and convert it to an integer value.
     *
     * @param mixed $mask The bitmask to be parsed. Can be of any type.
     *
     * @return int The parsed integer value of the bitmask.
     */
    public static function parseBitmask(mixed $mask): int {
        if ($mask instanceof self) {
            return $mask->value;
        }

        return (int)($mask ?? 0);
    }

    /**
     * Combines multiple flags into a single bitmask.
     *
     * @param self ...$flags The flags to combine.
     *
     * @return int The combined bitmask.
     */
    public static function combine(self ...$flags): int {
        return array_reduce(
            $flags,
            static fn(int $carry, self $flag) => $carry | $flag->value,
            0,
        );
    }

    /**
     * Check if any case is enabled in the given bitmask.
     *
     * @param int $mask The bitmask to check against.
     *
     * @return bool Returns true if any case is enabled, false otherwise.
     */
    public static function hasAny(int $mask): bool {
        return array_any(self::cases(), fn($case) => $case->in($mask));
    }

    /**
     * Checks if all cases are enabled in the provided bitmask.
     *
     * @param int $mask The bitmask to evaluate.
     *
     * @return bool True if all cases are enabled in the bitmask, otherwise false.
     */
    public static function hasAll(int $mask): bool {
        return array_all(self::cases(), fn($case) => $case->in($mask));
    }

    /**
     * Checks if a bitmask contains a specific flag or flags.
     *
     * @param int      $mask The bitmask to check.
     * @param int|self $flag The flag or flags (as int) to look for.
     *
     * @return bool True if the flag is present in the mask.
     */
    public static function has(int $mask, int|self $flag): bool {
        $value = self::parseBitmask($flag);

        return ($mask & $value) === $value;
    }

    /**
     * Adds a flag to a bitmask.
     *
     * @param int      $mask The bitmask to add to.
     * @param int|self $flag The flag or flags (as int) to add.
     *
     * @return int The new bitmask.
     */
    public static function add(int $mask, int|self $flag): int {
        $value = self::parseBitmask($flag);

        return $mask | $value;
    }

    /**
     * Removes a flag from a bitmask.
     *
     * @param int      $mask The bitmask to remove from.
     * @param int|self $flag The flag to remove.
     *
     * @return int The new bitmask.
     */
    public static function remove(int $mask, int|self $flag): int {
        $value = self::parseBitmask($flag);

        return $mask & ~$value;
    }

    /**
     * Checks if a flag is present in a bitmask.
     *
     * @param int $mask The bitmask to check.
     *
     * @return bool True if the flag is present, false otherwise.
     */
    public function in(int $mask): bool {
        return ($mask & $this->value) === $this->value;
    }

    /**
     * Add this method in a bitmask.
     *
     * @param int $mask The bitmask to add to.
     *
     * @return int The new bitmask.
     */
    public function addTo(int $mask): int {
        return $mask | $this->value;
    }

    /**
     * Remove this method in a bitmask.
     *
     * @param int $mask The bitmask to remove from.
     *
     * @return int The new bitmask.
     */
    public function removeFrom(int $mask): int {
        return $mask & ~$this->value;
    }

    /**
     * Retrieves a filtered list of cases based on the provided bitmask.
     *
     * @param int $mask The bitmask used to filter the cases.
     *
     * @return array The filtered list of cases matching the bitmask.
     */
    public static function list(int $mask): array {
        return array_filter(
            self::cases(),
            static fn(self $case) => ($mask & $case->value) !== 0,
        );
    }
}
