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

use function array_combine;
use function array_intersect_key;
use function ctype_alnum;
use function ctype_alpha;
use function ctype_digit;
use function ctype_xdigit;
use function filter_var;
use function implode;
use function is_string;
use function preg_replace;
use function str_split;
use function strlen;
use function strtolower;

use const FILTER_FLAG_ALLOW_HEX;
use const FILTER_FLAG_ALLOW_OCTAL;
use const FILTER_FLAG_ALLOW_THOUSAND;
use const FILTER_FLAG_GLOBAL_RANGE;
use const FILTER_FLAG_HOSTNAME;
use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_REQUIRE_ARRAY;
use const FILTER_VALIDATE_BOOLEAN;
use const FILTER_VALIDATE_DOMAIN;
use const FILTER_VALIDATE_EMAIL;
use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;
use const FILTER_VALIDATE_IP;
use const FILTER_VALIDATE_MAC;
use const FILTER_VALIDATE_REGEXP;

/**
 * VerifyValue
 *
 * Utility class providing static methods for validating and verifying common
 * value types such as booleans, emails, integers, floats, IP addresses, MAC
 * addresses, domains, and regex-matched strings.
 *
 * All methods wrap PHP's native `filter_var` function with a consistent,
 * expressive API and sensible defaults.
 */
class VerifyValue {
    /**
     * Builds a `filter_var` options array for numeric validation.
     *
     * Constructs the `options` subarray only when at least one of `$default`,
     * `$min`, or `$max` is provided; otherwise returns a bare flags array.
     *
     * @param mixed    $default The fallback value returned when validation fails. Pass `false` to omit.
     * @param null|int $min     Optional minimum range value (inclusive).
     * @param null|int $max     Optional maximum range value (inclusive).
     *
     * @return array Filter options array suitable for passing to `filter_var`.
     */
    private static function buildOptions(mixed $default = false, ?int $min = null, ?int $max = null): array {
        $options = [];

        // Only include keys that were explicitly provided
        if ($default !== false) $options['default'] = $default;
        if ($min !== null) $options['min_range'] = $min;
        if ($max !== null) $options['max_range'] = $max;

        if (!empty($options)) return [
            'options' => $options,
            'flags'   => 0,
        ];

        return ['flags' => 0,];
    }

    /**
     * Validates and converts a given value into a boolean.
     *
     * Accepts the same truthy/falsy strings that PHP's `filter_var` recognises
     * (e.g. `"true"`, `"yes"`, `"1"`, `"on"` and their negatives).
     *
     * @param mixed $value         The value to validate and convert to boolean.
     * @param bool  $nullOnFailure When `true`, returns `null` on failure instead of `false`.
     *
     * @return null|bool The boolean result, or `null` when conversion fails and `$nullOnFailure` is `true`.
     */
    public static function boolVerify(mixed $value, bool $nullOnFailure = false): ?bool {
        // Map the null-on-failure preference to the appropriate filter flag
        $onFailure = $nullOnFailure ? FILTER_NULL_ON_FAILURE : 0;

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, $onFailure);
    }

    /**
     * Validates an alphabetic string.
     *
     * @param string $value The value to validate.
     *
     * @return false|string The original value when alphabetic; otherwise, `false`.
     */
    public static function alphaVerify(string $value): false|string {
        if (ctype_alpha($value)) return $value;

        return false;
    }

    /**
     * Validates a value containing only decimal digits.
     *
     * @param mixed $value The value to validate.
     *
     * @return mixed The original value when it contains only decimal digits; otherwise, `false`.
     */
    public static function digitVerify(mixed $value): mixed {
        if (ctype_digit($value)) return $value;

        return false;
    }

    /**
     * Validates a value containing only hexadecimal digits.
     *
     * @param mixed $value The value to validate.
     *
     * @return mixed The original value when it contains only hexadecimal digits; otherwise, `false`.
     */
    public static function xdigitVerify(mixed $value): mixed {
        if (ctype_xdigit($value)) return $value;

        return false;
    }

    /**
     * Validates an alphanumeric string.
     *
     * @param string $value The value to validate.
     *
     * @return false|string The original value when alphanumeric; otherwise, `false`.
     */
    public static function alphaNumericVerify(string $value): false|string {
        if (ctype_alnum($value)) return $value;

        return false;
    }

    /**
     * Validates an email address or an array of email addresses.
     *
     * When an array is supplied, each element is validated individually, and the
     * method returns an associative array keyed by the original input values.
     *
     * @param string|array $value A single email address string, or an array of email address strings.
     *
     * @return false|string|array The validated email string, an associative array of results for array input,
     *                            or `false` if a single address fails validation.
     */
    public static function emailVerify(string|array $value): false|string|array {
        // Single address — validate and return directly
        if (is_string($value)) return filter_var($value, FILTER_VALIDATE_EMAIL);

        // Array of addresses — validate each element, then re-key by original input
        $result = filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_REQUIRE_ARRAY);

        return array_combine($value, $result);
    }

    /**
     * Validates an integer value with optional range and base constraints.
     *
     * Supports octal (prefix `0`) and hexadecimal (prefix `0x`) notation when
     * the corresponding flags are enabled.
     *
     * @param mixed    $int        The value to validate as an integer.
     * @param mixed    $default    Fallback value returned on validation failure. Pass `false` to omit.
     * @param null|int $min        Optional minimum allowed value (inclusive).
     * @param null|int $max        Optional maximum allowed value (inclusive).
     * @param bool     $allowOctal When `true`, octal notation is accepted.
     * @param bool     $allowHex   When `true`, hexadecimal notation is accepted.
     *
     * @return mixed The validated integer, the `$default` fallback, or `false` when validation fails.
     */
    public static function integerVerify(mixed $int, mixed $default = false, ?int $min = null, ?int $max = null, bool $allowOctal = false, bool $allowHex = false): mixed {
        $opts = static::buildOptions($default, $min, $max);

        // Conditionally enable alternative numeric base flags
        if ($allowOctal) $opts['flags'] |= FILTER_FLAG_ALLOW_OCTAL;
        if ($allowHex) $opts['flags'] |= FILTER_FLAG_ALLOW_HEX;

        return filter_var($int, FILTER_VALIDATE_INT, $opts);
    }

    /**
     * Validates an integer value using an option array.
     *
     * A convenience wrapper around {@see integerVerify()} that accepts a named
     * options array instead of individual parameters. Unrecognised keys are
     * silently ignored.
     *
     * Recognised option keys:
     * - `default`    — fallback value on failure
     * - `min`        — minimum allowed value
     * - `max`        — maximum allowed value
     * - `allowOctal` — accept octal notation (default `false`)
     * - `allowHex`   — accept hexadecimal notation (default `false`)
     *
     * @param mixed $int     The value to validate as an integer.
     * @param array $options Named validation options (see above).
     *
     * @return mixed The validated integer, the `default` fallback, or `false` when validation fails.
     */
    public static function intVerify(mixed $int, array $options = []): mixed {
        // Strip any unrecognised keys before forwarding to integerVerify
        $opts = array_intersect_key($options, [
            'default'    => null,
            'min'        => null,
            'max'        => null,
            'allowOctal' => false,
            'allowHex'   => false,
        ]);

        return static::integerVerify($int, ...$opts);
    }

    /**
     * Validates a float value with optional range and thousand-separator support.
     *
     * @param mixed    $int         The value to validate as a float.
     * @param mixed    $default     Fallback value returned on validation failure. Pass `false` to omit.
     * @param null|int $min         Optional minimum allowed value (inclusive).
     * @param null|int $max         Optional maximum allowed value (inclusive).
     * @param bool     $acceptFloat When `true`, values containing a thousand separator (`,`) are accepted.
     *
     * @return mixed The validated float, the `$default` fallback, or `false` when validation fails.
     */
    public static function floatVerify(mixed $int, mixed $default = false, ?int $min = null, ?int $max = null, bool $acceptFloat = false): mixed {
        $opts = static::buildOptions($default, $min, $max);

        // Allow thousand-separator notation when requested (e.g. "1,234.56")
        if ($acceptFloat) $opts['flags'] |= FILTER_FLAG_ALLOW_THOUSAND;

        return filter_var($int, FILTER_VALIDATE_FLOAT, $opts);
    }

    /**
     * Validates a value against a regular expression pattern.
     *
     * Returns the original value when it matches the pattern, the `$default`
     * string when provided, and the match fails, or `null` otherwise.
     *
     * @param mixed       $value   The value to validate.
     * @param string      $pattern A valid PCRE regular expression (including delimiters).
     * @param null|string $default Optional fallback string returned when validation fails.
     *
     * @return null|string The matched value, the default fallback, or `null` on failure.
     */
    public static function regexVerify(mixed $value, string $pattern, ?string $default = null): ?string {
        $options = ['regexp' => $pattern,];

        // Include a default only when one was explicitly supplied
        if ($default !== null) $options['default'] = $default;

        $result = filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => $options]);

        return $result === false ? null : $result;
    }

    /**
     * Validates a domain name, optionally enforcing strict hostname rules.
     *
     * @param mixed $value    The value to validate as a domain name.
     * @param bool  $hostname When `true`, applies stricter hostname validation rules (`FILTER_FLAG_HOSTNAME`).
     *
     * @return null|string The validated domain string, or `null` if validation fails.
     */
    public static function domainVerify(mixed $value, bool $hostname = false): ?string {
        $flags = 0;

        // Hostname mode enforces stricter label rules (e.g. no leading hyphens)
        if ($hostname) $flags |= FILTER_FLAG_HOSTNAME;

        $result = filter_var($value, FILTER_VALIDATE_DOMAIN, $flags);

        return $result === false ? null : $result;
    }

    /**
     * Validates an IP address with configurable version and range policies.
     *
     * By default, both IPv4 and IPv6 addresses are accepted. Passing `false` for
     * one version while leaving the other as `true` restricts validation to the
     * remaining version. When both are `false`, the filter still runs, but no
     * version flag is set, which may yield unexpected results.
     *
     * @param mixed $value        The value to validate as an IP address.
     * @param bool  $allowV4      Accept IPv4 addresses (default `true`).
     * @param bool  $allowV6      Accept IPv6 addresses (default `true`).
     * @param bool  $denyPrivate  Reject private-range addresses (e.g. `192.168.x.x`).
     * @param bool  $denyReserved Reject reserved-range addresses (e.g. `0.0.0.0`).
     * @param bool  $globalOnly   Accept only globally routable addresses.
     *
     * @return null|string The validated IP address string, or `null` if validation fails.
     */
    public static function ipVerify(mixed $value, bool $allowV4 = true, bool $allowV6 = true, bool $denyPrivate = false, bool $denyReserved = false, bool $globalOnly = false,
    ): ?string {
        $flags = 0;

        // Version gating — set a specific flag only when one version is excluded
        if ($allowV4 && !$allowV6) {
            $flags |= FILTER_FLAG_IPV4;
        } elseif ($allowV6 && !$allowV4) {
            $flags |= FILTER_FLAG_IPV6;
        }
        // If both are true → no version flag, accept both; if both false → undefined behaviour

        // Range policies — each flag independently restricts the accepted address space
        if ($denyPrivate) $flags |= FILTER_FLAG_NO_PRIV_RANGE;
        if ($denyReserved) $flags |= FILTER_FLAG_NO_RES_RANGE;
        if ($globalOnly) $flags |= FILTER_FLAG_GLOBAL_RANGE;

        $result = filter_var($value, FILTER_VALIDATE_IP, $flags);

        return $result === false ? null : $result;
    }

    /**
     * Validates a MAC address and optionally normalises its format.
     *
     * PHP's `filter_var` accepts colons (`:`), hyphens (`-`), and dots (`.`) as
     * separators. When `$normalise` is `true`, the validated address is stripped
     * of its original separators and rebuilt using `$separator`.
     *
     * @param mixed  $value     The value to validate as a MAC address.
     * @param bool   $normalise When `true`, the returned address is normalised to a consistent separator.
     * @param string $separator The separator character used when normalising (default `:`).
     *
     * @return null|string The validated (and optionally normalised) MAC address, or `null` if validation fails.
     */
    public static function macVerify(mixed $value, bool $normalise = false, string $separator = ':'): ?string {
        $result = filter_var($value, FILTER_VALIDATE_MAC);

        // Return null immediately when the value isn't a valid MAC address
        if ($result === false) return null;

        // Return the raw validated value when normalisation isn't requested
        if (!$normalise) return $result;

        // Normalise: strip all separator characters and rebuild with the chosen separator
        $hex = preg_replace('/[^0-9a-f]/i', '', $result);

        // Defensive guard — a valid MAC must yield exactly 12 hex characters
        if (strlen($hex) !== 12) return null;

        $hex = strtolower($hex);
        $chunks = str_split($hex, 2);

        return implode($separator, $chunks);
    }
}
