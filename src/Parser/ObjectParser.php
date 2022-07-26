<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @package Inane\Stdlib
 * @author Philip Michael Raab<peep@inane.co.za>
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\Parser;

use function array_keys;
use function array_push;
use function array_search;
use function get_class;
use function str_repeat;
use function strtr;
use function trim;
use const PHP_EOL;

/**
 * RecursiveParser
 *
 * Recursive variable parser
 *
 * @package Inane\Stdlib
 *
 * @version 1.0.0
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
     * Object Parser
     */
    private function __construct() {
    }

    /**
     * Create the dump string for an array
     *
     * @param array $array the array
     * @param int $level depth of array
     *
     * @return string array as string
     */
    private static function parseArray(array $array, int $level): string {
        $output = '';

        if (static::$depth <= $level) $output .= '[...]';
        else if (empty($array)) $output .= '[]';
        else {
            $keys = array_keys($array);
            $spaces = str_repeat(' ', $level * 4);
            $output .= '[';
            foreach ($keys as $key) $output .= PHP_EOL . "{$spaces}    [$key] => " . self::parseVariable($array[$key], $level + 1);
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
    private static function parseObject(mixed $object, int $level, array &$cache): string {
        $output = '';
        $className = get_class($object);

        if (($id = array_search($object, $cache, true)) !== false) $output .= "{$className}#" . (++$id) . '(...)';
        else if (static::$depth <= $level) $output .= "{$className}(...)";
        else {
            $id = array_push($cache, $object);
            $members = (array)$object;
            $keys = array_keys($members);
            $spaces = str_repeat(' ', $level * 4);
            $output .= "$className#$id {";

            foreach ($keys as $key) {
                $keyDisplay = strtr(trim("$key"), ["\0" => ':']);
                $output .= PHP_EOL . "{$spaces}    [$keyDisplay] => " . self::parseVariable($members[$key], $level + 1, $cache);
            }
            $output .= PHP_EOL . "{$spaces}}";
        }
        return $output;
    }

    /**
     * Creates the dump string for a variable
     *
     * @param mixed $var the variable
     * @param int $level current depth
     * @param array $cache parsed objects
     *
     * @return string dump string
     */
    private static function parseVariable(mixed $var, int $level = 0, array &$cache = []): string {
        return match (gettype($var)) {
            'boolean' => $var ? 'true' : 'false',
            'integer', 'double', 'string' => "$var",
            'resource' => '{resource}',
            'NULL' => 'null',
            'unknown type' => '{unknown}',
            'array' => static::parseArray($var, $level),
            'object' => static::parseObject($var, $level, $cache),
            default => '{unhandled}',
        };
    }

    /**
     * Parse Object
     *
     * @param mixed $object to parse
     *
     * @return string $object as string
     */
    public static function parse(mixed $object): string {
        return static::parseVariable($object);
    }
}
