<?php

/**
 * This file is part of the InaneTools package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Philip Michael Raab <philip@inane.co.za>
 * @package Inane\Stdlib
 *
 * @license MIT
 * @license https://inane.co.za/license/MIT
 *
 * @copyright 2015-2019 Philip Michael Raab <philip@inane.co.za>
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

/**
 * Array Utility
 *
 * @package Inane\Stdlib
 * @version 0.3.1
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
     * Complete missing keys
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
     * Update missing keys and existing values
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
     * @param string $input assignment string
     * @param null|string $separator path separator character (default: /)
     * @param null|string $assignor assignment character (default: =)
     *
     * @return array updated array
     */
    public static  function writeWithPath(array &$array, string $input, ?string $separator = null, ?string $assignor = null): array {
        list($path, $value) = explode($assignor ?? static::$pathAssignor, $input);

        $explodedPath = explode($separator ?? static::$pathSeparator, $path);

        $temp = &$array;
        foreach ($explodedPath as $key) $temp = &$temp[$key];
        $temp = $value;
        unset($temp);

        return $array;
    }
}
