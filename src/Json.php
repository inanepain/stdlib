<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Stdlib
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib;

use function json_decode;
use function json_encode;
use const false;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_NUMERIC_CHECK;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const true;

/**
 * JSON en/decoder
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.0
 */
class Json {
    /**
     * Private constructor
     *
     * @return void
     */
    private function __construct() {
    }

    /**
     * Encode a value to a json string
     *
     * OPTIONS:
     *  - (bool) [pretty=false] format result
     *  - (bool) [numeric=true] check for numeric values
     *  - (bool) [hex=true] encode ',<,>,",& are encoded to \u00*
     *  - (int) [flags=0] Bitmask consisting of JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT, JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE, JSON_THROW_ON_ERROR. The behaviour of these constants is described on the JSON constants page.
     *
     * @param mixed $data The value being encoded. Can be any type except a resource.
     * @param array $options encoding options
     *
     * @return string|false Returns a JSON encoded string on success or false on failure.
     */
    public static function encode(mixed $data, array $options = []): string|false {
        $options += [
            'pretty' => false,
            'numeric' => true,
            'hex' => true,
            'flags' => 0,
        ];

        ['pretty' => $pretty, 'numeric' => $numeric, 'hex' => $hex, 'flags' => $flags] = $options;

        $flags |= $hex ? JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP : 0;
        $flags |= $numeric ? JSON_NUMERIC_CHECK : 0;
        $flags |= $pretty ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : 0;

        return json_encode($data, $flags);
    }

    /**
     * Takes a JSON encoded string and converts it into a PHP value.
     *
     * OPTIONS:
     *  - (bool) [assoc=true] When true, JSON objects will be returned as associative arrays; when false, JSON objects will be returned as objects. When null, JSON objects will be returned as associative arrays or objects depending on whether JSON_OBJECT_AS_ARRAY is set in the flags.
     *  - (int) [depth=512] Maximum nesting depth of the structure being decoded. The value must be greater than 0, and less than or equal to 2147483647.
     *  - (int) [flags=0] Bitmask of JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR. The behaviour of these constants is described on the JSON constants page.
     *
     * @param string $json json string to decode
     * @param array $options decoding options
     *
     * @return mixed Returns the value encoded in json in appropriate PHP type. Values true, false and null are returned as true, false and null respectively. null is returned if the json cannot be decoded or if the encoded data is deeper than the nesting limit.
     */
    public static function decode(string $json, array $options = []): mixed {
        $options += [
            'assoc' => true,
            'depth' => 512,
            'flags' => 0,
        ];

        ['assoc' => $assoc, 'depth' => $depth, 'flags' => $flags] = $options;

        return json_decode($json, $assoc, $depth, $flags);
    }
}
