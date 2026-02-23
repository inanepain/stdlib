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

namespace Inane\Stdlib\String;

use function array_merge;
use function count;
use function implode;
use function in_array;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function str_replace;
use function strtolower;
use function strtoupper;
use function ucfirst;
use function ucwords;

/**
 * Inflector
 *
 * @version 1.2.0
 */
class Inflector {
    /**
     * Singular and Plural Rules
     *
     * @var array
     */
    protected static array $rules = [
        'pluralise'   => [
            '/(oxen|octopi|viri|aliases|quizzes)$/'         => '$1',
            '/(people|men|children|sexes|moves|stadiums)$/' => '$1',
            '/(quiz)$/'                                     => '$1zes',
            '/^(ox)$/'                                      => '$1en',
            '/([m|l])ice$/'                                 => '$1ice',
            '/([m|l])ouse$/'                                => '$1ice',
            '/(matr|vert|ind)ix|ex$/'                       => '$1ices',
            '/(x|ch|ss|sh)$/'                               => '$1es',
            '/([^aeiouy]|qu)y$/'                            => '$1ies',
            '/(hive)$/'                                     => '$1s',
            '/(?:([^f])fe|([lr])f)$/'                       => '$1$2ves',
            '/sis$/'                                        => 'ses',
            '/([ti])a$/'                                    => '$1a',
            '/([ti])um$/'                                   => '$1a',
            '/(buffal|tomat)o$/'                            => '$1oes',
            '/(bu)s$/'                                      => '$1ses',
            '/(alias|status)$/'                             => '$1es',
            '/(octop|vir)i$/'                               => '$1i',
            '/(octop|vir)us$/'                              => '$1i',
            '/(ax|test)is$/'                                => '$1es',
            '/s$/'                                          => 's',
            '/$/'                                           => 's',
        ],
        'singularise' => [
            '/(quiz)zes$/'                                                    => '$1',
            '/(matr)ices$/'                                                   => '$1ix',
            '/(vert|ind)ices$/'                                               => '$1ex',
            '/^(ox)en/'                                                       => '$1',
            '/(alias|status)$/'                                               => '$1',
            '/(alias|status)es$/'                                             => '$1',
            '/(octop|vir)us$/'                                                => '$1us',
            '/(octop|vir)i$/'                                                 => '$1us',
            '/(cris|ax|test)es$/'                                             => '$1is',
            '/(cris|ax|test)is$/'                                             => '$1is',
            '/(shoe)s$/'                                                      => '$1',
            '/(o)es$/'                                                        => '$1',
            '/(bus)es$/'                                                      => '$1',
            '/([m|l])ice$/'                                                   => '$1ouse',
            '/(x|ch|ss|sh)es$/'                                               => '$1',
            '/(m)ovies$/'                                                     => '$1ovie',
            '/(s)eries$/'                                                     => '$1eries',
            '/([^aeiouy]|qu)ies$/'                                            => '$1y',
            '/([lr])ves$/'                                                    => '$1f',
            '/(tive)s$/'                                                      => '$1',
            '/(hive)s$/'                                                      => '$1',
            '/([^f])ves$/'                                                    => '$1fe',
            '/(^analy)sis$/'                                                  => '$1sis',
            '/(^analy)ses$/'                                                  => '$1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '$1$2sis',
            '/([ti])a$/'                                                      => '$1um',
            '/(n)ews$/'                                                       => '$1ews',
            '/(s|si|u)s$/'                                                    => '$1s',
            '/s$/'                                                            => '',
        ],
        'irregular'   => [
            'child'   => 'children',
            'man'     => 'men',
            'move'    => 'moves',
            'person'  => 'people',
            'sex'     => 'sexes',
            'stadium' => 'stadiums',
        ],
        'uncountable' => ['equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'],
        'data'        => [
            'singularise' => [],
            'pluralise'   => [],
        ],
    ];

    /**
     * Convert to plural
     *
     * Examples:
     *  - post => posts
     *
     * @param string $word singular word
     *
     * @return string plural word
     */
    protected static function swapPluralSingular(string $word, string $action): string {
        if (count(static::$rules['data'][$action]) === 0) {
            $data = [];
            if ($action === 'pluralise') foreach(static::$rules['irregular'] as $singular => $plural) $data["/{$singular}/"] = $plural; else foreach(static::$rules['irregular'] as $singular => $plural) $data["/{$plural}/"] = $singular;
            static::$rules['data'][$action] = array_merge($data, static::$rules[$action]);
        }

        if (static::isCountable($word)) foreach(static::$rules['data'][$action] as $pattern => $replace) {
            if (preg_match($pattern, $word, $match)) return preg_replace($pattern, $replace, $word);
        }

        return $word;
    }

    /**
     * Convert to plural
     *
     * Examples:
     *  - post => posts
     *
     * @param string $word singular word
     *
     * @return string plural word
     */
    public static function pluralise(string $word): string {
        return static::swapPluralSingular($word, __FUNCTION__);
    }

    /**
     * Convert to single
     *
     * Examples:
     *  - posts => post
     *
     * @param string $word plural word
     *
     * @return string singular word
     */
    public static function singularise(string $word): string {
        return static::swapPluralSingular($word, __FUNCTION__);
    }

    /**
     * Countable
     *
     * Examples:
     *  - advice => false
     *  - cat => true
     *
     * @param string $word
     *
     * @return bool word
     */
    public static function isCountable(string $word): bool {
        return !in_array($word, static::$rules['uncountable']);
    }

    /**
     * Convert to camel case
     *
     * Examples:
     *  - active_model => activeModel
     *  - active_model/errors => activeModel\Errors
     *
     * @param string $word       word
     * @param bool   $upperFirst initial char uppercase
     *
     * @return string camel case word
     */
    public static function camelise(string $word, bool $upperFirst = false): string {
        $word = preg_replace_callback('/([_ \-\/])+(.?)/', fn($m) => ($m[1] === '/' ? '\\' : '') . strtoupper($m[2]), $word);

        return $upperFirst ? ucfirst($word) : $word;
    }

    /**
     * Underscore word
     *
     * Examples:
     *  - ActiveModel => active_model
     *  - ActiveModel\Errors => active_model/errors
     *
     * @param string $word word
     *
     * @return string word
     */
    public static function underscore(string $word): string {
        $word = strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', $word));

        return str_replace('\_', '/', $word);
    }

    /**
     * Hyphenate word
     *
     * Examples:
     *  - ActiveModel => active-model
     *  - ActiveModel\Errors => active-model/errors
     *
     * @since 1.1.0
     *
     * @param string $word word
     *
     * @return string word
     */
    public static function hyphenate(string $word): string {
        $word = strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1-$2', $word));

        return str_replace('\-', '/', $word);
    }

    /**
     * Capitalise
     *
     * Returns a copy of the input with the first character converted to uppercase and the remainder to lowercase.
     *
     * Examples:
     *  - active model => Active model
     *  - ACTIVE => Active
     *
     * @param string $word word
     *
     * @return string word
     */
    public static function capitalise(string $word): string {
        $word = strtolower($word);

        return ucfirst($word);
    }

    /**
     * Humanise
     *
     * Examples:
     *  - active_model => Active model
     *  - author_id => Author
     *
     * @param string $word word
     *
     * @return string word
     */
    public static function humanise(string $word): string {
        $word = preg_replace('/_?id$|^id_?/', '', $word);
        $word = preg_replace('/[ _\-]+/', ' ', $word);

        return ucfirst($word);
    }

    /**
     * Titleise
     *
     * Examples:
     *  - man from the boondocks => Man From The Boondocks
     *  - raiders_of_the_lost_ark => Raiders Of The Lost Ark
     *
     * @param string $word word
     *
     * @return string word
     */
    public static function titleise(string $word): string {
        $word = strtolower($word);
        $word = str_replace('_', ' ', $word);

        return ucwords($word);
    }

    /**
     * Break on uppercase letters
     *
     * Examples:
     *  - helloWorld => hello World
     *  - firstName => firstName
     *
     * upperFirst:
     *  - helloWorld => Hello World
     *  - firstName => First Name
     *
     * @since 1.2.0
     *
     * @param string $word       word
     * @param bool   $upperFirst make first character of first word uppercase else unchanged
     *
     * @return string words
     */
    public static function breakOnUppercase(string $word, bool $upperFirst = false): string {
        $words = implode(' ', preg_split('/(?=[A-Z])/', $word));

        return $upperFirst ? ucfirst($words) : $words;
    }

    /**
     * Ordinal
     *
     * Examples:
     *  - 1 => st
     *  - 2 => nd
     *
     * @param int $number
     *
     * @return string word
     */
    public static function ordinal(int $number): string {
        $r = $number % 100;
        if (11 <= $r && $r <= 13) return 'th';

        return match ($number % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th'
        };
    }

    /**
     * Ordinalise
     *
     * Examples:
     *  - 1 => 1st
     *  - 2 => 2nd
     *
     * @param int $number
     *
     * @return string word
     */
    public static function ordinalise(int $number): string {
        return $number . static::ordinal($number);
    }
}
