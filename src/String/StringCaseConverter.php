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

namespace Inane\Stdlib\String;

use Inane\Http\Exception\InvalidArgumentException;

use function lcfirst;
use function preg_replace;
use function preg_replace_callback;
use function strtolower;
use function strtoupper;
use function trim;
use function ucfirst;

/**
 * Class StringCaseConverter
 *
 * Provides utility methods for converting strings between different case formats
 * such as camelCase, snake_case, PascalCase, and others.
 * 
 * - script-dir ⇄ scriptDir
 * - kebab-case ⇄ camelCase.
 *
 * @package inanepain\stdlib\String
 */
class StringCaseConverter {
    /**
     * Converts a kebab-case string to camelCase.
     *
     * @param string $string The input string in kebab-case format.
     * 
     * @return string The converted string in camelCase format.
     */
    public static function kebabToCamel(string $string): string {
        $string = strtolower(trim($string, '-'));
        return preg_replace_callback('/-([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $string);
    }

    /**
     * Converts a camelCase string to kebab-case.
     *
     * @param string $string The input string in camelCase format.
     * 
     * @return string The converted string in kebab-case format.
     */
    public static function camelToKebab(string $string): string {
        $string = lcfirst($string);
        return strtolower(preg_replace('/([A-Z])/', '-$1', $string));
    }

    /**
     * Converts a PascalCase string to kebab-case.
     *
     * @param string $string The input string in PascalCase format.
     * 
     * @return string The converted string in kebab-case format.
     */
    public static function pascalToKebab(string $string): string {
        return strtolower(preg_replace('/([A-Z])/', '-$1', lcfirst($string)));
    }

    /**
     * Converts a kebab-case string to PascalCase.
     *
     * @param string $string The input string in kebab-case format (e.g., "my-string-example").
     * 
     * @return string The converted string in PascalCase format (e.g., "MyStringExample").
     */
    public static function kebabToPascal(string $string): string {
        return ucfirst(self::kebabToCamel($string));
    }

    /**
     * Converts a snake_case string to camelCase or PascalCase.
     *
     * @param string $string The input string in snake_case format.
     * @param bool $pascal If true, converts to PascalCase; otherwise, converts to camelCase. Default is false.
     * 
     * @return string The converted string in camelCase or PascalCase.
     */
    public static function snakeToCamel(string $string, bool $pascal = false): string {
        $string = strtolower(trim($string, '_'));
        $camel = preg_replace_callback('/_([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $string);
        return $pascal ? ucfirst($camel) : $camel;
    }

    /**
     * Converts a camelCase string to snake_case.
     *
     * @param string $string The input string in camelCase format.
     * 
     * @return string The converted string in snake_case format.
     */
    public static function camelToSnake(string $string): string {
        $string = lcfirst($string);
        return strtolower(preg_replace('/([A-Z])/', '_$1', $string));
    }

    /**
     * Converts a PascalCase string to snake_case.
     *
     * @param string $string The input string in PascalCase format.
     * 
     * @return string The converted string in snake_case format.
     */
    public static function pascalToSnake(string $string): string {
        return strtolower(preg_replace('/([A-Z])/', '_$1', lcfirst($string)));
    }

    /**
     * Converts a snake_case string to PascalCase.
     *
     * @param string $string The input string in snake_case format.
     * 
     * @return string The converted string in PascalCase format.
     */
    public static function snakeToPascal(string $string): string {
        return ucfirst(self::snakeToCamel($string));
    }

    /**
     * Converts a string from one capitalisation style to another.
     *
     * @param string $string The input string to be converted.
     * @param Capitalisation $from The current capitalisation style of the input string.
     * @param Capitalisation $to The desired capitalisation style for the output string.
     * 
     * @return string The converted string in the desired capitalisation style.
     */
    public static function convert(string $string, Capitalisation $from, Capitalisation $to): string {
        if ($from === $to) {
            return $string;
        }

        switch ($from) {
            case Capitalisation::KebabCase:
                if ($to === Capitalisation::camelCase) return self::kebabToCamel($string);
                if ($to === Capitalisation::PascalCase) return self::kebabToPascal($string);
                if ($to === Capitalisation::snake_case) return self::camelToSnake(self::kebabToCamel($string));
                break;
            case Capitalisation::snake_case:
                if ($to === Capitalisation::camelCase) return self::snakeToCamel($string);
                if ($to === Capitalisation::PascalCase) return self::snakeToPascal($string);
                if ($to === Capitalisation::KebabCase) return self::camelToKebab(self::snakeToCamel($string));
                break;
            case Capitalisation::camelCase:
                if ($to === Capitalisation::KebabCase) return self::camelToKebab($string);
                if ($to === Capitalisation::snake_case) return self::camelToSnake($string);
                if ($to === Capitalisation::PascalCase) return ucfirst($string);
                break;
            case Capitalisation::PascalCase:
                if ($to === Capitalisation::KebabCase) return self::pascalToKebab($string);
                if ($to === Capitalisation::snake_case) return self::pascalToSnake($string);
                if ($to === Capitalisation::camelCase) return lcfirst($string);
                break;
        }

        throw new InvalidArgumentException("Unsupported conversion: {$from} -> {$to}");
    }
}
