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

namespace Inane\Stdlib\Parser;

use function array_keys;
use function array_push;
use function array_search;
use function get_class;
use function gettype;
use function str_repeat;
use function str_replace;
use function strtr;
use function trim;
use const PHP_EOL;

/**
 * ObjectParser
 *
 * Recursive object/variable parser
 *
 * @version 1.1.0
 */
class ObjectParser {
    /**
     * Max dump depth
     *
     * N.B.: does not effect `var_dump`
     * @see \Inane\Dumper\Dumper::$useVarExport
     */
    public static int $depth = 4;

    /**
     * Max dump depth
     *
     * N.B.: does not effect `var_dump`
     * @see \Inane\Dumper\Dumper::$useVarExport
     */
    public int $parseDepth {
        get => isset($this->parseDepth) ? $this->parseDepth : static::$depth;
        set(int $value) {
            $this->parseDepth = $value;
        }
    }

    /**
     * Object Parser: private constructor
     *  So instantiation can only be done internally.
     */
    private function __construct(?int $parseDepth = null) {
        if ($parseDepth !== null)
            $this->parseDepth = $parseDepth;
    }

    /**
     * Create the dump string for an array
     *
     * @param array $array the array
     * @param int $level depth of array
     *
     * @return string array as string
     */
    private function parseArray(array $array, int $level): string {
        $output = '';

        if ($this->parseDepth <= $level) $output .= '[...]';
        else if (empty($array)) $output .= '[]';
        else {
            $keys = array_keys($array);
            $spaces = str_repeat(' ', $level * 4);
            $output .= '[';
            foreach ($keys as $key) $output .= PHP_EOL . "{$spaces}    [$key] => " . $this->parseVariable($array[$key], $level + 1). ',';
            $output .= PHP_EOL . "{$spaces}]";
        }

        return $output;
    }

    /**
     * Create the dump string for an object
     *
     * @param mixed $object the object
     * @param int $level depth of object
     * @param array $cache objects already parsed
     *
     * @return string object as string
     */
    private function parseObject(mixed $object, int $level, array &$cache): string {
        $output = '';
        $className = get_class($object);

        if (($id = array_search($object, $cache, true)) !== false) $output .= "{$className}#" . (++$id) . '(...)';
        else if ($this->parseDepth <= $level) $output .= "{$className}(...)";
        else {
            $id = array_push($cache, $object);
            $members = (array)$object;
            $keys = array_keys($members);
            $spaces = str_repeat(' ', $level * 4);
            $output .= "$className#$id {";

            foreach ($keys as $key) {
                $keyDisplay = strtr(trim("$key"), ["\0" => ':']);
                $output .= PHP_EOL . "{$spaces}    [$keyDisplay] => " . $this->parseVariable($members[$key], $level + 1, $cache);
            }
            $output .= PHP_EOL . "{$spaces}}";
        }
        return $output;
    }

    /**
     * Creates the dump string for a variable
     *  calling other parse methods if needed.
     *
     * @param mixed $var the variable
     * @param int $level current depth
     * @param array $cache parsed objects
     *
     * @return string dump string
     */
    private function parseVariable(mixed $var, int $level = 0, array &$cache = []): string {
        return match (gettype($var)) {
            'boolean' => $var ? 'true' : 'false',
            'integer', 'double' => "$var",
            'string' => "'".str_replace("'", "\'", $var)."'",
            'resource' => '{resource}',
            'NULL' => 'null',
            'unknown type' => '{unknown}',
            'array' => $this->parseArray($var, $level),
            'object' => $this->parseObject($var, $level, $cache),
            default => '{unhandled}',
        };
    }

    /**
     * Parses the given object and returns its string representation.
     *
     * @param mixed $object The object to be parsed.
     * @param int|null $parseDepth Optional. The maximum depth to parse nested objects. If null, uses default depth.
     *
     * @return string The string representation of the parsed object.
     */
    public static function parse(mixed $object, ?int $parseDepth = null): string {
        return new self($parseDepth)->dump($object);
    }

    /**
     * Parse Object
     *
     * @param mixed $object to parse
     *
     * @return string $object as string
     */
    public function dump(mixed $object): string {
        return $this->parseVariable($object);
    }
}
