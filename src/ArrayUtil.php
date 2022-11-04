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
 * @category array
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);
/*
$data = [
    'people' => [
        'philip' => [
            'age' => 16,
            'firstName' => 'Philip',
            'lastName' => 'Raab',
        ],
    ],
];

echo ArrayUtil::readWithPath($data, 'people/philip/firstName') . PHP_EOL;
var_export(ArrayUtil::writeWithPath($data, 'people/philip/middleName=Michael'));

ArrayUtil::$pathAssignor=':';
ArrayUtil::$pathSeparator='.';

ArrayUtil::writeWithPath($data, 'people.philip.colour:Purple');

ArrayUtil::$pathSeparator='->';
echo ArrayUtil::readWithPath($data, 'people->philip->colour') . PHP_EOL;
*/

namespace Inane\Stdlib;

use function array_filter;
use function array_key_exists;
use function array_pop;
use function array_shift;
use function count;
use function explode;
use function in_array;
use function is_array;
use function str_contains;
use const false;
use const null;

/**
 * Array Utility
 *
 * @package Inane\Stdlib
 *
 * @version 0.3.3
 */
class ArrayUtil {
    /**
     * Path Separator
     */
    public static string $pathSeparator = '/';

    /**
     * Path Assignor
     */
    public static string $pathAssignor = '=';

    /**
     * Creates a new array by merging missing keys and values
     *
     * The middle ground between `array_merge` and `array_merge_recursive`
     *
     * Merges arrays **without** replacing existing values only **adding** keys.<br/>
     * > Priority decreases from first (highest) to last (lowest)
     *
     * @since 0.3.0
     *
     * @param array ...$arrays to merge with decreeing priority left to right
     *
     * @return array completed array
     */
    public static function complete(array ...$arrays): array {
        $arrays = array_filter($arrays, fn ($a) => count($a) > 0) ?: [[]];
        $m = array_shift($arrays);

        while ($a = array_shift($arrays))
            foreach ($a as $k => $v)
                if (is_array($v) && isset($m[$k]) && is_array($m[$k])) $m[$k] = static::complete($m[$k], $v);
                else if (!array_key_exists($k, $m) || in_array($m[$k], [
                    '',
                    null,
                    false
                ])) $m[$k] = $v;
        return $m;
    }

    /**
     * Returns array merged by only updating existing keys
     *
     *  - keys updated only
     *
     * @since 0.3.2
     *
     * @param array ...$arrays to merge with increasing priority left to right
     *
     * @return array updated array
     */
    public static function modify(array ...$arrays): array {
        $arrays = array_filter($arrays, fn ($a) => count($a) > 0) ?: [[]];
        $m = array_shift($arrays);

        while ($a = array_shift($arrays))
            foreach ($a as $k => $v)
                if (array_key_exists($k, $m)) {
                    if (is_array($v) && isset($m[$k]) && is_array($m[$k])) $m[$k] = static::modify($v, $m[$k]);
                    else $m[$k] = $v;
                }
        return $m;
    }

    /**
     * Creates a new array by merging $arrays by updating existing values and adding missing keys
     *
     * The middle ground between `array_merge` and `array_merge_recursive`
     *
     * Merges arrays **replacing** existing values and **adding** missing keys.<br/>
     * > Priority increasing from first (lowest) to last (highest)
     *
     * @since 0.3.0
     *
     * @param array ...$arrays to merge with increasing priority left to right
     *
     * @return array updated array
     */
    public static function update(array ...$arrays): array {
        $arrays = array_filter($arrays, fn ($a) => count($a) > 0) ?: [[]];
        $m = array_pop($arrays);

        while ($a = array_pop($arrays))
            foreach ($a as $k => $v)
                if (is_array($v) && isset($m[$k]) && is_array($m[$k])) $m[$k] = static::update($v, $m[$k]);
                else if (!array_key_exists($k, $m) || in_array($m[$k], [
                    '',
                    null,
                    false
                ])) $m[$k] = $v;
        return $m;
    }

    /**
     * get object path value
     *
     * ```php
     * $data = ['people' => ['bob' => [ 'age' => 7, 'firstName' => 'Bob']]];
     * echo ArrayUtil::readWithPath($data, 'people/bob/firstName') . PHP_EOL;
     * # => Bob
     * ```
     *
     * @param array $array array to query
     * @param string $path path to get
     * @param null|string $separator path separator char (default: /)
     *
     * @return mixed path value
     */
    public static function readWithPath(array $array, string $path, ?string $separator = null): mixed {
        $explodedPath = explode($separator ?? static::$pathSeparator, $path);

        $temp = &$array;
        foreach ($explodedPath as $key) {
            if (array_key_exists($key, $temp)) $temp = &$temp[$key];
            else return null;
        }

        return $temp;
    }

    /**
     * Set value using path assignment
     *
     * input: contacts/personal/bob/age=16
     *
     * ```php
     * $data = ['people' => ['bob' => [ 'age' => 7, 'firstName' => 'Bob']]];
     * var_export(ArrayUtil::writeWithPath($data, 'people/bob/lastName=Tail'));
     * # => ['people' => ['bob' => [ 'age' => 7, 'firstName' => 'Bob', 'lastName' => 'Tail']]];
     * ```
     *
     * @param array $array array to update
     * @param string $arrayPath assignment string
     * @param null|string $separator path separator character (default: /)
     * @param null|string $assignor assignment character (default: =)
     *
     * @return array updated array
     */
    public static function writeWithPath(array &$array, string $arrayPath, ?string $separator = null, ?string $assignor = null): array {
        list($path, $value) = explode($assignor ?? static::$pathAssignor, $arrayPath);

        $explodedPath = explode($separator ?? static::$pathSeparator, $path);

        $temp = &$array;
        foreach ($explodedPath as $key) $temp = &$temp[$key];
        $temp = $value;
        unset($temp);

        return $array;
    }

    /**
     * Set value using path but separate $value
     *
     * This allows for $value to be anything you can normally add to an array
     *
     * @param array $array array to update
     * @param string $arrayPath assignment string
     * @param mixed $value assignment value
     * @param null|string $separator path separator character (default: /)
     * @param null|string $assignor assignment character (default: =)
     *
     * @return array updated array
     */
    public static function writeToPath(array &$array, string $arrayPath, mixed $value, ?string $separator = null, ?string $assignor = null): array {
        $explodedPath = explode($separator ?? static::$pathSeparator, $arrayPath);

        $temp = &$array;
        foreach ($explodedPath as $key) $temp = &$temp[$key];
        $temp = $value;
        unset($temp);

        return $array;
    }

    /**
     * Read or Write path value based on $pathAction
     *
     * Separator: `static::$pathSeparator`
     * Assignor : `static::$pathAssignor`
     *
     * @since 0.3.3
     *
     * @param array       $array array to update
     * @param string      $pathAction string array path to read or assign value to
     *
     * @return mixed read value|updated array
     */
    public static function stringPath(array &$array, string $pathAction): mixed {
        if (str_contains($pathAction, static::$pathAssignor))
            return static::writeWithPath($array, $pathAction);

        return static::readWithPath($array, $pathAction);
    }
}
