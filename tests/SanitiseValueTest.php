<?php

/**
 * inane-fw
 *
 * Inane Framework
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author   Philip Michael Raab <philip@cathedral.co.za>
 * @package  inanepain\inane-fw
 * @category inane-fw
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $Version$
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Tests;

use Inane\Stdlib\Value\SanitiseValue;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Value\SanitiseValue`.
 */
final class SanitiseValueTest extends TestCase {
    /**
     * Verifies tags are stripped from a single string.
     *
     * @return void
     */
    public function testStripTagsHandlesSingleValues(): void {
        $this->assertSame('hi', SanitiseValue::stripTags('<b>hi</b>'));
        $this->assertSame('hi there', SanitiseValue::stripTags('<p>hi <em>there</em></p>'));
        $this->assertSame('plain', SanitiseValue::stripTags('plain'));
    }

    /**
     * Verifies tags are stripped from every element of an array.
     *
     * @return void
     */
    public function testStripTagsHandlesArrayValues(): void {
        $result = SanitiseValue::stripTags([
            '<i>a</i>',
            '<b>b</b>',
        ]);

        $this->assertSame(['a', 'b'], $result);
    }

    /**
     * Verifies permitted tags are retained.
     *
     * @return void
     */
    public function testStripTagsRetainsAllowedTags(): void {
        $this->assertSame('<b>hi</b>x', SanitiseValue::stripTags('<b>hi</b><i>x</i>', '<b>'));
        $this->assertSame('<b>hi</b><i>x</i>', SanitiseValue::stripTags('<b>hi</b><i>x</i>', ['b', 'i']));
    }

    /**
     * Verifies email sanitisation of single values.
     *
     * @return void
     */
    public function testEmailSanitiseHandlesSingleValues(): void {
        $this->assertSame('user@example.com', SanitiseValue::emailSanitise('us(er)@exa mple.com'));
        $this->assertSame('user@example.com', SanitiseValue::emailSanitise('user@example.com'));
    }

    /**
     * Verifies email sanitisation of array values.
     *
     * @return void
     */
    public function testEmailSanitiseHandlesArrayValues(): void {
        $result = SanitiseValue::emailSanitise([
            'a b@c.com',
            'good@example.com',
        ]);

        $this->assertSame(['ab@c.com', 'good@example.com'], $result);
    }

    /**
     * Verifies tags are removed before email sanitisation when requested.
     *
     * @return void
     */
    public function testEmailSanitiseCanStripTagsFirst(): void {
        $this->assertSame('a@b.com', SanitiseValue::emailSanitise('<b>a@b.com</b>', true));
        $this->assertSame(['a@b.com'], SanitiseValue::emailSanitise(['<b>a@b.com</b>'], true));
    }

    /**
     * Verifies URL sanitisation of single and array values.
     *
     * @return void
     */
    public function testUrlSanitiseHandlesSingleAndArrayValues(): void {
        $this->assertSame('http://example.com/ab', SanitiseValue::urlSanitise('http://exa mple.com/a b'));
        $this->assertSame(['http://ab.com'], SanitiseValue::urlSanitise(['http://a b.com']));
        $this->assertSame('http://example.com', SanitiseValue::urlSanitise('<b>http://example.com</b>', true));
    }

    /**
     * Verifies integer sanitisation of single and array values.
     *
     * @return void
     */
    public function testIntSanitiseStripsNonIntegerCharacters(): void {
        $this->assertSame('-125', SanitiseValue::intSanitise('abc-12.5'));
        $this->assertSame('42', SanitiseValue::intSanitise('4a2'));
        $this->assertSame('7', SanitiseValue::intSanitise(7));
        $this->assertSame(['1', '2'], SanitiseValue::intSanitise(['a1', '2b']));
    }

    /**
     * Verifies float sanitisation honours the fraction flag.
     *
     * @return void
     */
    public function testFloatSanitiseHonoursFractionFlag(): void {
        $this->assertSame('-125', SanitiseValue::floatSanitise('abc-12.5'));
        $this->assertSame('-12.5', SanitiseValue::floatSanitise('abc-12.5', true));
        $this->assertSame(['1.5'], SanitiseValue::floatSanitise(['1.5x'], true));
    }

    /**
     * Verifies float sanitisation honours the thousand and scientific flags.
     *
     * @return void
     */
    public function testFloatSanitiseHonoursThousandAndScientificFlags(): void {
        $this->assertSame('1234.5', SanitiseValue::floatSanitise('1,234.5', true));
        $this->assertSame('1,234.5', SanitiseValue::floatSanitise('1,234.5', true, true));
        $this->assertSame('1,234.5e3', SanitiseValue::floatSanitise('1,234.5e3', true, true, true));
        $this->assertSame('1,234.53', SanitiseValue::floatSanitise('1,234.5e3', true, true));
    }
}
