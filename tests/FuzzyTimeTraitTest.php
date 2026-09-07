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
 * @author   Philip Michael Raab <philip@cathedral.co.za>
 * @package  inanepain\stdlib
 * @category stdlib
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Tests;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Inane\Stdlib\Parser\FuzzyTimeTrait;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Parser\FuzzyTimeTrait`.
 */
final class FuzzyTimeTraitTest extends TestCase {
    /**
     * Returns a helper object exposing the trait static API for tests.
     *
     * @return object
     */
    private function getFuzzyTimeHelper(): object {
        return new class {
            use FuzzyTimeTrait;
        };
    }

    /**
     * Verifies exact-hour times produce the expected "o'clock" wording.
     *
     * @throws DateMalformedStringException
     */
    public function testFuzzyClockReturnsOClockForExactHour(): void {
        $helper = $this->getFuzzyTimeHelper();
        $time = new DateTime('2026-07-04 13:00:00', new DateTimeZone('UTC'));

        $this->assertSame("one o'clock", $helper::fuzzyClock($time));
    }

    /**
     * Verifies minutes are rounded to the nearest five and phrased as "past"
     * before or at half-past.
     *
     * @throws DateMalformedStringException
     */
    public function testFuzzyClockRoundsAndUsesPastPhraseBeforeHalfHour(): void {
        $helper = $this->getFuzzyTimeHelper();
        $time = new DateTime('2026-07-04 09:13:00', new DateTimeZone('UTC'));

        $this->assertSame('quarter past nine', $helper::fuzzyClock($time));
    }

    /**
     * Verifies times past half-hour are phrased as "to" the next hour.
     *
     * @throws DateMalformedStringException
     */
    public function testFuzzyClockUsesToPhraseWithNextHourAfterHalfPast(): void {
        $helper = $this->getFuzzyTimeHelper();
        $time = new DateTime('2026-07-04 09:34:00', new DateTimeZone('UTC'));

        $this->assertSame('twenty-five to ten', $helper::fuzzyClock($time));
    }

    /**
     * Verifies rounding up to sixty minutes rolls over to the next hour.
     *
     * @throws DateMalformedStringException
     */
    public function testFuzzyClockRollsOverHourWhenRoundedMinuteIsSixty(): void {
        $helper = $this->getFuzzyTimeHelper();
        $time = new DateTime('2026-07-04 23:58:00', new DateTimeZone('UTC'));

        $this->assertSame("twelve o'clock", $helper::fuzzyClock($time));
    }
}
