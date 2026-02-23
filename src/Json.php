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

namespace Inane\Stdlib;

use Inane\File\File;
use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Exception\JsonException;
use function is_array;
use function is_null;
use function is_string;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function json_validate;
use const false;
use const JSON_ERROR_NONE;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_INVALID_UTF8_IGNORE;
use const JSON_INVALID_UTF8_SUBSTITUTE;
use const JSON_NUMERIC_CHECK;
use const JSON_PARTIAL_OUTPUT_ON_ERROR;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const null;
use const true;

/**
 * JSON en/decoder
 *
 * @version 0.4.0
 */
class Json {
    //#region Properties
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
    //#endregion Properties

    /**
     * Private constructor
     *
     * @return void
     */
    private function __construct() {}

    /**
     * Tests if a given string is a valid JSON string.
     *
     * This method does not update the 'lastError' property.
     *
     * error:
     *  - code        : error code `JSON_ERROR_NONE` for no error.
     *  - message    : error message, 'No error' for no error.
     *
     * @since 0.2.0
     *
     * @param string                         $json              string to test if valid json format.
     * @param bool                           $ignoreInvalidUTF8 Sets whether to ignore invalid UTF-8 characters.
     * @param null|array<string, string|int> $error             an optional array to store the error should one occur.
     *
     * @return bool **true** if valid json formatted string.
     */
    public static function isJsonString(string $json, bool $ignoreInvalidUTF8 = true, ?array &$error = null): bool {
        $flags = $ignoreInvalidUTF8 ? JSON_INVALID_UTF8_IGNORE : 0;
        json_validate(json: $json, flags: $flags);

        if (is_array($error)) {
            $error['code'] = json_last_error();
            $error['message'] = json_last_error_msg();
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
     * @version 0.4.0 - [options->onerror='']
     *
     * @param mixed            $data    The data to be encoded. Can be of any type.
     * @param array            $options An associative array of encoding options. Supported keys:
     *                                  - 'pretty' (bool): Whether to format the output with indents and whitespace.
     *                                  - 'numeric' (bool): Whether to convert numeric strings to numbers.
     *                                  - 'escape' (bool): Whether to use unescaped Unicode and slashes.
     *                                  - 'hex' (bool): Whether to encode JSON in a hex-safe format.
     *                                  - 'onerror' (string): How to handle errors: '', 'ignore', 'substitute', 'throw'. Empty string means default behavior.
     *                                  - 'flags' (int): Additional flags for JSON encoding.
     * @param null|string|File $file    A file path or File object where the encoded JSON string should be written.
     *                                  If null, the JSON is not written to a file.
     * @param ?array &         $error   If provided, will be populated with an array containing error code and message on failure.
     *
     * @return string|false Returns the JSON-encoded string on success, or false on failure.
     *
     * @throws JsonException
     */
    public static function encode(mixed $data, array $options = [], null|string|File $file = null, ?array &$error = null): string|false {
        self::$lastException = null;

        if (is_string($file)) $file = new File($file);

        $options += [
            'pretty'  => false,
            'numeric' => true,
            'escape'  => true,
            'hex'     => false,
            'onerror' => '',
            'flags'   => 0,
        ];

        ['pretty' => $pretty, 'numeric' => $numeric, 'escape' => $escape, 'hex' => $hex, 'onerror' => $onerror, 'flags' => $flags] = $options;

        $flags |= $hex ? JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP : 0;
        $flags |= $escape ? JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES : 0;
        $flags |= $numeric ? JSON_NUMERIC_CHECK : 0;
        $flags |= $pretty ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : 0;
        $flags |= match ($onerror) {
            'ignore' => JSON_INVALID_UTF8_IGNORE | JSON_PARTIAL_OUTPUT_ON_ERROR,
            'substitute' => JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR,
            //            'throw' => JSON_THROW_ON_ERROR,
            default => 0,
        };

        if ($data instanceof OptionsInterface || $data instanceof ArrayObject) $data = $data->toArray();

        $json = json_encode($data, $flags);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = [
                'code'    => json_last_error(),
                'message' => json_last_error_msg(),
            ];
            self::$lastException = new JsonException(...$error);

            if ($onerror === 'throw') {
                throw self::$lastException;
            }
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
     *  - (string) [onerror=''] How to handle errors: '', 'ignore', 'substitute', 'throw'. Empty string means default behavior.
     *  - (int) [flags=0] Bitmask of (behaviour described on the JSON constants page):
     *      - JSON_BIGINT_AS_STRING
     *      - JSON_INVALID_UTF8_IGNORE
     *      - JSON_INVALID_UTF8_SUBSTITUTE
     *      - JSON_OBJECT_AS_ARRAY
     *      - JSON_THROW_ON_ERROR
     *  - (bool) [asOptions=false] Return an Options object instead of an array.
     *
     * @param string  $json    json string to decode
     * @param array   $options decoding options
     * @param ?array &$error   If provided, will be populated with an array containing error code and message on failure.
     *
     * @return mixed Returns the value encoded in JSON in the appropriate PHP type. Values true, false, and null are returned as true, false, and null respectively. null is returned if the JSON cannot be decoded or if the encoded data is deeper than the nesting limit.
     *
     * @throws JsonException
     */
    public static function decode(string $json, array $options = [], ?array &$error = null): mixed {
        self::$lastException = null;

        $options += [
            'assoc'     => true,
            'depth'     => 512,
            'onerror'   => '',
            'flags'     => 0,
            'asOptions' => false,
        ];

        ['assoc' => $assoc, 'depth' => $depth, 'onerror' => $onerror, 'flags' => $flags, 'asOptions' => $asOptions] = $options;
        $flags |= match ($onerror) {
            'ignore' => JSON_INVALID_UTF8_IGNORE,
            'substitute' => JSON_INVALID_UTF8_SUBSTITUTE,
            //            'throw' => JSON_THROW_ON_ERROR,
            default => 0,
        };

        $array = json_decode($json, $assoc, $depth, $flags);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = [
                'code'    => json_last_error(),
                'message' => json_last_error_msg(),
            ];
            self::$lastException = new JsonException(...$error);

            if ($onerror === 'throw') {
                throw self::$lastException;
            }
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
     *
     * @throws JsonException Exception thrown if JSON_THROW_ON_ERROR option is set for Json::encode() or Json::decode(). code contains the error type, for possible values see json_last_error().
     */
    public static function decodeFile(string|File $file, array $options = []): mixed {
        if (is_string($file)) $file = new File($file);
        if (!$file->isReadable()) return null;

        return static::decode($file->read(), $options);
    }
}
