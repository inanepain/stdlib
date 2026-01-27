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

use Inane\Datetime\Timestamp;
use function round;

/**
 * Trait FuzzyTime
 *
 * @version 0.5.0
 */
trait FuzzyTimeTrait {
    /**
     * @var array minutes to words mappings.
     */
    private const array words = [
        0  => "o'clock",
        5  => 'five past',
        10 => 'ten past',
        15 => 'quarter past',
        20 => 'twenty past',
        25 => 'twenty-five past',
        30 => 'half past',
        35 => 'twenty-five to',
        40 => 'twenty to',
        45 => 'quarter to',
        50 => 'ten to',
        55 => 'five to'
    ];

    /**
     * @var array number to words mappings.
     */
    private const array map = [
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve'
    ];

    /**
     * Converts a DateTime into words.
     *
     * @param null|\DateTime|Timestamp $time time to convert.
     *
     * @return string time words.
     */
    public static function fuzzyClock(null|\DateTime|Timestamp $time = null): string {
        $time = $time ?? new Timestamp()->getDateTime();
        if ($time instanceof Timestamp) $time = $time->getDateTime();
        $hour = (int) $time->format('G');
        $minute = (int) $time->format('i');

        // Round minutes to the nearest 5
        $minute = (int)(5 * round($minute / 5));

        if ($minute >= 60) {
            $minute = 0;
            $hour++;
        }

        return match (true) {
            $minute === 0 => self::numToWords($hour % 12 ?: 12) . " o'clock",
            $minute <= 30 => self::words[$minute] . ' ' . self::numToWords($hour % 12 ?: 12),
            default => self::words[$minute] . ' ' . self::numToWords(($hour + 1) % 12 ?: 12),
        };
    }

    /**
     * converts an integer into words.
     *
     * @param int $num number to convert.
     *
     * @return string words number
     */
    private static function numToWords(int $num): string {
        return self::map[$num];
    }
}
