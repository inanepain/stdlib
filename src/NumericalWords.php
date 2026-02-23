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

use SplStack;
use function array_flip;
use function array_map;
use function count;
use function implode;
use function in_array;
use function ltrim;
use function preg_split;
use function str_split;
use function strlen;
use function strrev;
use function strtolower;
use function strtr;
use function substr;
use function trim;
use const false;
use const PHP_INT_MAX;

/**
 * Number and word converter
 *
 * Converts spelt numbers to digits and digits to spelt words.
 *
 * @version 0.1.0
 */
class NumericalWords {
    /**
     * A Scale for testing
     *
     * @var Options
     */
    protected static Options $scale;

    /**
     * Word value lookup
     *
     * @var array
     */
    private static array $lookup = [
        'zero' => 0,
        'a' => 1,
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
        'ten' => 10,
        'eleven' => 11,
        'twelve' => 12,
        'thirteen' => 13,
        'fourteen' => 14,
        'fifteen' => 15,
        'sixteen' => 16,
        'seventeen' => 17,
        'eighteen' => 18,
        'nineteen' => 19,
        'twenty' => 20,
        'thirty' => 30,
        'forty' => 40,
        'fifty' => 50,
        'sixty' => 60,
        'seventy' => 70,
        'eighty' => 80,
        'ninety' => 90,
        'hundred' => 100, // +1*0
        'thousand' => 1_000,
        'million' => 1_000_000,
        'billion' => 1_000_000_000,
        'trillion' => 1_000_000_000_000,
        'quadrillion' => 1_000_000_000_000_000,
        'quintillion' => 1_000_000_000_000_000_000, //10^18
        'and' => '',
    ];

    /**
     * NumericalWords Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->getScale();
    }

    /**
     * Scale to use for testing
     *
     * @return Options
     */
    public function getScale(): Options {
        if (!isset(static::$scale) || !static::$scale->isLocked()) {
            static::$scale = new Options([
                'one'         => 10 ** 0,
                'five'        => 5,
                'ten'         => 10 ** 1,
                'hundred'     => 10 ** 2,
                'thousand'    => 10 ** 3,
                'million'     => 10 ** 6,
                'billion'     => 10 ** 9,
                'trillion'    => 10 ** 12,
                'quadrillion' => 10 ** 15,
                'quintillion' => 10 ** 18,
                'seven hundred thirty six  two hundred twelve thousand six hundred eighty four million nineteen billion eight hundred twenty trillion eight hundred seventy five quadrillion three quintillion' => 3875820019684212736,
                // 'Sextillion'  => pow(10, 21),
            ], false);
        }

        return static::$scale;
    }

    /**
     * Returns the maximum integer value supported by PHP, either as a number or in words.
     *
     * @param bool $inWords Determines whether the maximum integer value should be returned in words (true) or as a number (false).
     *
     * @return string|int The maximum integer value as a number or its equivalent in words.
     */
    public function phpIntMax(bool $inWords = false): string|int {
        if ($inWords) return $this->toWords(PHP_INT_MAX);

        return PHP_INT_MAX;
    }

    /**
     * Convert spelt numbers to digits
     *
     * @param string $word spelt numbers
     *
     * @return float digits
     */
    public function toNumber(string $word): float {
        $data = strtolower(trim($word));

        // Replace all number words with an equivalent numeric value
        $data = strtr(
            $data,
            static::$lookup
        );

        // Coerce all tokens to numbers
        $parts = array_map(
            fn ($val) => (float)$val,
            preg_split('/[\s-]+/', $data)
        );

        $stack = new SplStack; // Current work stack
        $sum = 0; // Running total
        $last = null;

        foreach ($parts as $part) {
            if (!$stack->isEmpty()) {
                // We're part-way through a phrase
                if ($stack->top() > $part) {
                    // Decreasing step, e.g. from hundreds to ones
                    if ($last >= 1000) {
                        $sum += $stack->pop(); // If we drop from more than 1000, then we've finished the phrase
                        $stack->push($part); // This is the first element of a new phrase
                    } else
                        $stack->push($stack->pop() + $part); // Drop down from less than 1000, just addition // e.g. "seventy-one" -> "70 1" -> "70 + 1"
                } else
                    $stack->push($stack->pop() * $part); // Increasing step, e.g. ones to hundreds
            } else
                $stack->push($part); // This is the first element of a new phrase

            $last = $part; // Store the last processed part
        }

        return $sum + $stack->pop();
    }

    /**
     * Convert digits to spelt words
     *
     * @param float $number digits
     *
     * @return string spelt words
     */
    public function toWords(float $number): string {
        $w = '';

        $n = (string)(int)$number;
        $r = strrev($n);
        $g = str_split($r, 3);
        $p = array_map(static fn ($i) => strrev($i), $g);

        $us = ['', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion',];
        $upLook = array_flip(static::$lookup);

        $tmp = [];
        $c = count($p) - 1;
        foreach ($p as $i => $v) {
            $t = '';
            $u = $us[$i];
            $v = ltrim($v, '0');

            if (strlen($v) === 1)
                $tmp[] = $upLook[(string)$v];
            else if (strlen($v) === 2) {
                $z = (int)$v;
                if ($z === 0) {
                } else if (in_array($z, static::$lookup, true))
                    $tmp[] = $upLook[$z];
                else {
                    $z1 = substr((string)$z, 0, 1) . '0';
                    $tmp[] = $upLook[$z1];
                    $tmp[] = $upLook[substr((string)$z, 1)];
                }
            } else if (strlen($v) === 3) {
                $tmp[] = $upLook[$v[0]] . ' hundred';
                $z = (int) substr($v, 1);

                if ($z === 0) {
                } else if (in_array($z, static::$lookup, true))
                    $tmp[] = $upLook[$z];
                else {
                    $z1 = substr((string)$z, 0, 1) . '0';
                    $tmp[] = $upLook[$z1];
                    $tmp[] = $upLook[substr((string)$z, 1)];
                }
            }
            if ($v !== '') $tmp[] = $us[$i];
        }

        $w = implode(' ', $tmp);
        return trim($w);
    }
}
