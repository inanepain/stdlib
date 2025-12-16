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

use Inane\File\File;
use Inane\Stdlib\Exception\JsonException;
use JsonException as SystemJsonException;
use function is_array;
use function is_null;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function is_string;
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
use const JSON_THROW_ON_ERROR;
use const null;
use const true;

/**
 * JSON en/decoder
 *
 * @todo: version bumped
 * @version 0.3.0
 */
class Json {
    /**
     * Determines whether an error should throw an exception or not.
     *
     * @since 0.3.0
     */
    public static bool $throwOnError = true;

    /**
     * Holds the last exception encountered, if any.
     *
     * @since 0.3.0
     */
    protected(set) static ?JsonException $lastException = null;

    /**
     * Private constructor
     *
     * @return void
     */
    private function __construct() {
    }

    /**
     * Retrieves the throw flag used to determine error handling mode.
     *
     * @since 0.3.0
     *
     * @return int The flag indicating whether to throw JSON errors or not.
     */
    protected static function getThrowFlag(): int {
        return self::$throwOnError ? JSON_THROW_ON_ERROR : 0;
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
     * Encodes the given data into a JSON string with specified options.
     *
     * Optionally also write to $file.
     *
     * @version 0.1.3 - [options->hex=false]
     * @version 0.3.0 - [options->escape=true]
     * @version 0.3.0 - also write to file
     *
     * @param mixed            $data    The data to be encoded. Can be of any type.
     * @param array            $options An associative array of encoding options. Supported keys:
     *                                  - 'pretty' (bool): Whether to format the output with indents and whitespace.
     *                                  - 'numeric' (bool): Whether to convert numeric strings to numbers.
     *                                  - 'escape' (bool): Whether to use unescaped Unicode and slashes.
     *                                  - 'hex' (bool): Whether to encode JSON in a hex-safe format.
     *                                  - 'flags' (int): Additional flags for JSON encoding.
     * @param null|string|File $file    A file path or File object where the encoded JSON string should be written.
     *                                  If null, the JSON is not written to a file.
     *
     * @return string|false Returns the JSON-encoded string on success, or false on failure.
     *
     * @throws JsonException
     */
    public static function encode(mixed $data, array $options = [], null|string|File $file = null): string|false {
        self::$lastException = null;

        if (is_string($file)) $file = new File($file);

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

        try {
            $json = json_encode($data, self::getThrowFlag() | $flags);
        } catch (SystemJsonException $e) {
            self::$lastException = new JsonException($e->getMessage(), $e->getCode());
            throw self::$lastException;
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::$lastException = new JsonException(json_last_error_msg(), json_last_error());
        }

        if ($file && $file->isWritable()) $file->write($json);

        return $json;
    }

    /**
     * Takes a JSON encoded string and converts it into a PHP value.
     *
     * OPTIONS:
     *  - (bool) [assoc=true] When true, JSON objects will be returned as associative arrays; when false, JSON objects will be returned as objects. When null, JSON objects will be returned as associative arrays or objects depending on whether JSON_OBJECT_AS_ARRAY is set in the flags.
     *  - (int) [depth=512] Maximum nesting depth of the structure being decoded. The value must be greater than 0, and less than or equal to 2147483647.
     *  - (int) [flags=0] Bitmask of JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR. The behaviour of these constants is described on the JSON constants page.
     *  - (bool) [asOptions=false] Return an Options object instead of an array.
     *
     * @param string $json    json string to decode
     * @param array  $options decoding options
     *
     * @return mixed Returns the value encoded in JSON in the appropriate PHP type. Values true, false, and null are returned as true, false, and null respectively. null is returned if the JSON cannot be decoded or if the encoded data is deeper than the nesting limit.
     *
     * @throws JsonException
     */
    public static function decode(string $json, array $options = []): mixed {
        self::$lastException = null;

        $options += [
            'assoc' => true,
            'depth' => 512,
            'flags' => 0,
            'asOptions' => false,
        ];

        ['assoc' => $assoc, 'depth' => $depth, 'flags' => $flags, 'asOptions' => $asOptions] = $options;

        try {
            $array = json_decode($json, $assoc, $depth, self::getThrowFlag() | $flags);
        } catch (SystemJsonException $e) {
            self::$lastException = new JsonException($e->getMessage(), $e->getCode());
            throw self::$lastException;
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::$lastException = new JsonException(json_last_error_msg(), json_last_error());
        }

        return is_null($array) ? null : ($asOptions ? new Options($array) : $array);
    }

    /**
     * Decodes the content of a file.
     *
     * @since 0.3.0
     *
     * @param string|File $file    The file to decode. Can be a file path (string) or a File object.
     * @param array       $options Optional settings for the decoding process.
     *
     * @return mixed The decoded data, or null if the file is not readable.
     */
    public static function decodeFile(string|File $file, array $options = []): mixed {
        if (is_string($file)) $file = new File($file);
        if (!$file->isReadable()) return null;

        return static::decode($file->read(), $options);
    }
}
