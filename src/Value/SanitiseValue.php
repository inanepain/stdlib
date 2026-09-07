<?php

/**
 * inane-fw
 *
 * Inane Framework
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author   Philip Michael Raab <philip@cathedral.co.za>
 * @package  inanepain\inane-fw
 * @category inane-fw
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $Version$
 *
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Value;

use function array_map;
use function filter_var;
use function is_array;
use function strip_tags;

use const FILTER_FLAG_ALLOW_FRACTION;
use const FILTER_FLAG_ALLOW_SCIENTIFIC;
use const FILTER_FLAG_ALLOW_THOUSAND;
use const FILTER_REQUIRE_ARRAY;
use const FILTER_SANITIZE_EMAIL;
use const FILTER_SANITIZE_NUMBER_FLOAT;
use const FILTER_SANITIZE_NUMBER_INT;
use const FILTER_SANITIZE_URL;

/**
 * Provides static value sanitisation helpers.
 */
class SanitiseValue {
    /**
     * Removes HTML and PHP tags from a string or each string in an array.
     *
     * @param string|array      $string      The value to sanitise.
     * @param null|array|string $allowedTags Tags permitted in the result.
     *
     * @return string|array The sanitised value.
     */
    public static function stripTags(string|array $string, array|string|null $allowedTags = null): string|array {
        if (!is_array($string)) return strip_tags($string, $allowedTags);

        return array_map(static fn(string $v): string => strip_tags($v, $allowedTags), $string);
    }

    /**
     * Sanitises an email address or each address in an array.
     *
     * @param string|array $value     The email address or addresses to sanitise.
     * @param bool         $stripTags Whether to remove tags before sanitising.
     *
     * @return false|string|array The sanitised email value or values.
     */
    public static function emailSanitise(string|array $value, bool $stripTags = false): false|string|array {
        if ($stripTags) $value = static::stripTags($value);

        // Single address — validate and return directly
        if (!is_array($value)) return filter_var($value, FILTER_SANITIZE_EMAIL);

        // Array of addresses — validate each element, then re-key by original input
        return filter_var($value, FILTER_SANITIZE_EMAIL, FILTER_REQUIRE_ARRAY);
    }

    /**
     * Sanitises a URL or each URL in an array.
     *
     * @param string|array $value     The URL or URLs to sanitise.
     * @param bool         $stripTags Whether to remove tags before sanitising.
     *
     * @return false|string|array The sanitised URL value or values.
     */
    public static function urlSanitise(string|array $value, bool $stripTags = false): false|string|array {
        if ($stripTags) $value = static::stripTags($value);

        // Single address — validate and return directly
        if (!is_array($value)) return filter_var($value, FILTER_SANITIZE_URL);

        // Array of addresses — validate each element, then re-key by original input
        return filter_var($value, FILTER_SANITIZE_URL, FILTER_REQUIRE_ARRAY);
    }

    /**
     * Sanitises an integer value or each value in an array.
     *
     * @param int|float|string|array $value The value or values to sanitise.
     *
     * @return false|int|array The sanitised integer value or values.
     */
    public static function intSanitise(int|float|string|array $value): false|int|array {
        // Single address — validate and return directly
        if (!is_array($value)) return filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        // Array of addresses — validate each element, then re-key by original input
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
    }

    /**
     * Sanitises a floating-point value or each value in an array.
     *
     * @param int|float|string|array $value           The value or values to sanitise.
     * @param bool                   $allowFraction   Whether to retain decimal separators.
     * @param bool                   $allowThousand   Whether to retain thousand separators.
     * @param bool                   $allowScientific Whether to retain scientific notation.
     *
     * @return false|float|array The sanitised floating-point value or values.
     */
    public static function floatSanitise(int|float|string|array $value, bool $allowFraction = false, bool $allowThousand = false, bool $allowScientific = false): false|float|array {
        $flag = 0;
        $flag |= $allowFraction ? FILTER_FLAG_ALLOW_FRACTION : 0;
        $flag |= $allowThousand ? FILTER_FLAG_ALLOW_THOUSAND : 0;
        $flag |= $allowScientific ? FILTER_FLAG_ALLOW_SCIENTIFIC : 0;

        // Single address — validate and return directly
        if (!is_array($value)) return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, $flag);

        // Array of addresses — validate each element, then re-key by original input
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_REQUIRE_ARRAY | $flag);
    }
}
