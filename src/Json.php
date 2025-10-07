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

namespace Inane\Stdlib;

use function is_array;
use function is_null;
use function json_decode;
use function json_encode;
use function json_last_error;
use const false;
use const JSON_ERROR_CTRL_CHAR;
use const JSON_ERROR_DEPTH;
use const JSON_ERROR_INF_OR_NAN;
use const JSON_ERROR_NONE;
use const JSON_ERROR_RECURSION;
use const JSON_ERROR_STATE_MISMATCH;
use const JSON_ERROR_SYNTAX;
use const JSON_ERROR_UNSUPPORTED_TYPE;
use const JSON_ERROR_UTF8;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_NUMERIC_CHECK;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const null;
use const true;

/**
 * JSON en/decoder
 *
 * @todo: version bump
 * @version 0.2.0
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
	 * Test if jsonStr is a valid json string
	 *
	 * info:
	 *  - code		: response code `JSON_ERROR_NONE` for no error
	 *  - message	: response message, empty string for no error
	 *
	 * @since 0.2.0
	 *
	 * @param string		$jsonStr	string to test if valid json format
	 * @param null|array	$info		optional array to story the test message
	 *
	 * @return bool **true** if valid json formatted string
	 */
    public static function isJsonString(string $jsonStr, ?array &$info = null): bool {
        json_decode($jsonStr);

		if (is_array($info)) {
			$info['code'] = json_last_error();
			$info['message'] = match(json_last_error()) {
				JSON_ERROR_NONE => '',
				JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded.',
				JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON.',
				JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded.',
				JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON.',
				JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.',
				JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded.',
				JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded.',
				JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given.',
				default => 'Unknown JSON error occurred.',
			};
		}

		return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Encode a value to a json string
     *
     * OPTIONS:
     *  - (bool) [pretty=false] format result
     *  - (bool) [numeric=true] check for numeric values
     *  - (bool) [escape=true] unescape slashes and unicode
     *  - (bool) [hex=false] encode ',<,>,",& are encoded to \u00*
     *  - (int) [flags=0] Bitmask consisting of JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT, JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE, JSON_THROW_ON_ERROR. The behaviour of these constants is described on the JSON constants page.
     *
     * @todo: version bump
	 * @version 0.1.3 - [options->hex=false]
	 * @version version bump - [options->escape=true]
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
            'escape' => true,
            'hex' => false,
            'flags' => 0,
        ];

        ['pretty' => $pretty, 'numeric' => $numeric, 'escape' => $escape, 'hex' => $hex, 'flags' => $flags] = $options;

        $flags |= $hex ? JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP : 0;
        $flags |= $escape ? JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES : 0;
        $flags |= $numeric ? JSON_NUMERIC_CHECK : 0;
        $flags |= $pretty ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : 0;

        if ($data instanceof Options || $data instanceof ArrayObject) $data = $data->toArray();

		return json_encode($data, $flags);
    }

    /**
     * Takes a JSON encoded string and converts it into a PHP value.
     *
     * OPTIONS:
     *  - (bool) [assoc=true] When true, JSON objects will be returned as associative arrays; when false, JSON objects will be returned as objects. When null, JSON objects will be returned as associative arrays or objects depending on whether JSON_OBJECT_AS_ARRAY is set in the flags.
     *  - (int) [depth=512] Maximum nesting depth of the structure being decoded. The value must be greater than 0, and less than or equal to 2147483647.
     *  - (int) [flags=0] Bitmask of JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR. The behaviour of these constants is described on the JSON constants page.
     *  - (bool) [asOptions=false] Return Options object instead of an array.
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
            'asOptions' => false,
        ];

        ['assoc' => $assoc, 'depth' => $depth, 'flags' => $flags, 'asOptions' => $asOptions] = $options;

        $array = json_decode($json, $assoc, $depth, $flags);

        return is_null($array) ? null : ($asOptions ? new Options($array) : $array);
    }
}
