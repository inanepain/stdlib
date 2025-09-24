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

use ArrayAccess;

use function array_shift;
use function explode;
use function is_array;
use function is_null;
use function property_exists;
use function str_contains;
use const null;

/**
 * ObjectPathTrait
 *
 * Add methods to access (read/write) data using strings
 *
 * Set `pathArrayProperty` to property to use as pathObject
 * else
 * pathObject is set to `data`
 *
 * @version 0.1.0
 */
trait ArrayPathAccessTrait {
    // protected string $pathArrayProperty = 'data';
    // protected string $arrayPathDivider = '/';

    /**
     * Walks multidimensional $array using $path and sets final segment to $value
     *
     * @param array|\ArrayAccess &$array The array to manipulate
     * @param array $path An array containing keys for each dimension
     * @param mixed $value The value that is assigned to the element
     *
     * @return void
     */
    private static function walkPath(array|ArrayAccess &$array, array $path, $value): void {
        $key = array_shift($path);

        if (empty($path))
            $array[$key] = $value;
        else {
            if (!isset($array[$key]) || !(is_array($array[$key]) || $array[$key] instanceof ArrayAccess)) $array[$key] = [];
            static::walkPath($array[$key], $path, $value);
        }
    }

    /**
     * Get Path Divider
     *
     * If none, `/`
     *
     * @return string path divider
     */
    private function getPathDivider(): string {
        return property_exists($this, 'arrayPathDivider') ? $this->arrayPathDivider : '/';
    }

    /**
     * Get the array to use with path
     *
     * If none, null returned
     *
     * @return null|array|\ArrayAccess path array
     */
    private function getPathArray(): null|array|ArrayAccess {
        $property = property_exists($this, 'pathArrayProperty') ? $this->pathArrayProperty : 'data';
        return $this->{$property};
    }

    /**
     * Set array used with path
     *
     * @param array|\ArrayAccess $pathArray
     *
     * @return self
     */
    private function setPathArray(array|ArrayAccess $pathArray): self {
        $property = property_exists($this, 'pathArrayProperty') ? $this->pathArrayProperty : 'data';
        $this->{$property} = $pathArray;
        return $this;
    }

    /**
     * Get or Set the value at path
     *
     * @note: writing
     * This short-cut method only works with string values, though numbers should be ok too
     *
     * @example read $action = 'config/env/module_path'
     * @example write $action = 'config/env/module_path=src/module'
     *
     * @param string $path path to set
     * @param null|string $divider path divider if other than default required or property not set
     *
     * @return mixed
     */
    public function stringPath(string $pathAndValue, ?string $divider = null): mixed {
        if (str_contains($pathAndValue, '=')) {
            [$path, $value] = explode('=', $pathAndValue);
            return $this->writePath($path, $value, $divider);
        }

        return $this->readPath($pathAndValue, $divider);
    }

    /**
     * get value at $path
     *
     * @param string $path path to get
     *
     * @return mixed path value
     */
    public function readPath(string $path, ?string $divider = null): mixed {
        if (is_null($divider)) $divider = $this->getPathDivider();

        $explodedPath = explode($divider, $path);
        $temp = $this->getPathArray();
        if (is_null($temp)) return null;

        foreach ($explodedPath as $key) $temp = &$temp[$key];

        return $temp;
    }

    /**
     * set $path to $value
     *
     * @param string $path path to set
     * @param mixed $value value of path
     * @param null|string $divider path divider if other than default required or property not set
     *
     * @return null|self
     */
    public function writePath(string $path, mixed $value, ?string $divider = null): ?self {
        $temp = $this->getPathArray();
        if (is_null($temp)) return null;

        if (is_null($divider)) $divider = $this->getPathDivider();
        $data = $this->getPathArray();
        static::walkPath($data, explode($divider, $path), $value);
        $this->setPathArray($data);

        return $this;
    }
}
