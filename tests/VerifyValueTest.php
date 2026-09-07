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

use Inane\Stdlib\Value\VerifyValue;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Value\VerifyValue`.
 */
final class VerifyValueTest extends TestCase {
    /**
     * Verifies that boolean-like strings are converted correctly.
     *
     * @return void
     */
    public function testBoolVerifyConvertsBooleanLikeStrings(): void {
        $this->assertTrue(VerifyValue::boolVerify('yes'));
        $this->assertTrue(VerifyValue::boolVerify('on'));
        $this->assertTrue(VerifyValue::boolVerify('1'));
        $this->assertTrue(VerifyValue::boolVerify(true));
        $this->assertFalse(VerifyValue::boolVerify('no'));
        $this->assertFalse(VerifyValue::boolVerify('off'));
        $this->assertFalse(VerifyValue::boolVerify('0'));
        $this->assertFalse(VerifyValue::boolVerify(false));
    }

    /**
     * Verifies that invalid boolean input can return null when requested.
     *
     * @return void
     */
    public function testBoolVerifyReturnsNullWhenNullOnFailureIsEnabled(): void {
        $this->assertNull(VerifyValue::boolVerify('not-a-bool', true));
        $this->assertFalse(VerifyValue::boolVerify('not-a-bool'));
        $this->assertTrue(VerifyValue::boolVerify('true', true));
        $this->assertFalse(VerifyValue::boolVerify('false', true));
    }

    /**
     * Verifies alphabetic string validation.
     *
     * @return void
     */
    public function testAlphaVerifyAcceptsOnlyLetters(): void {
        $this->assertSame('abcDEF', VerifyValue::alphaVerify('abcDEF'));
        $this->assertFalse(VerifyValue::alphaVerify('abc1'));
        $this->assertFalse(VerifyValue::alphaVerify('abc def'));
        $this->assertFalse(VerifyValue::alphaVerify(''));
    }

    /**
     * Verifies decimal digit validation.
     *
     * @return void
     */
    public function testDigitVerifyAcceptsOnlyDecimalDigits(): void {
        $this->assertSame('12345', VerifyValue::digitVerify('12345'));
        $this->assertFalse(VerifyValue::digitVerify('12a'));
        $this->assertFalse(VerifyValue::digitVerify('12.5'));
        $this->assertFalse(VerifyValue::digitVerify(''));
    }

    /**
     * Verifies hexadecimal digit validation.
     *
     * @return void
     */
    public function testXdigitVerifyAcceptsOnlyHexadecimalDigits(): void {
        $this->assertSame('1aF9', VerifyValue::xdigitVerify('1aF9'));
        $this->assertSame('deadbeef', VerifyValue::xdigitVerify('deadbeef'));
        $this->assertFalse(VerifyValue::xdigitVerify('0x1A'));
        $this->assertFalse(VerifyValue::xdigitVerify('g1'));
    }

    /**
     * Verifies alphanumeric string validation.
     *
     * @return void
     */
    public function testAlphaNumericVerifyAcceptsLettersAndDigits(): void {
        $this->assertSame('abc123', VerifyValue::alphaNumericVerify('abc123'));
        $this->assertFalse(VerifyValue::alphaNumericVerify('abc-123'));
        $this->assertFalse(VerifyValue::alphaNumericVerify('abc 123'));
    }

    /**
     * Verifies single email validation outcomes.
     *
     * @return void
     */
    public function testEmailVerifyHandlesSingleValues(): void {
        $this->assertSame('user@example.com', VerifyValue::emailVerify('user@example.com'));
        $this->assertSame('first.last+tag@sub.example.co.za', VerifyValue::emailVerify('first.last+tag@sub.example.co.za'));
        $this->assertFalse(VerifyValue::emailVerify('not-an-email'));
        $this->assertFalse(VerifyValue::emailVerify('user@'));
    }

    /**
     * Verifies array email validation keeps original values as keys.
     *
     * @return void
     */
    public function testEmailVerifyHandlesArrayValues(): void {
        $emails = [
            'good@example.com',
            'bad-email',
        ];

        $result = VerifyValue::emailVerify($emails);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('good@example.com', $result);
        $this->assertArrayHasKey('bad-email', $result);
        $this->assertSame('good@example.com', $result['good@example.com']);
        $this->assertFalse($result['bad-email']);
    }

    /**
     * Verifies integer validation, including ranges and fallbacks.
     *
     * @return void
     */
    public function testIntegerVerifyValidatesValuesRangesAndDefaults(): void {
        $this->assertSame(42, VerifyValue::integerVerify('42'));
        $this->assertSame(-7, VerifyValue::integerVerify('-7'));
        $this->assertFalse(VerifyValue::integerVerify('abc'));
        $this->assertFalse(VerifyValue::integerVerify('1.5'));

        // Out of range without a default
        $this->assertFalse(VerifyValue::integerVerify(5, false, 10, 20));
        // In range
        $this->assertSame(15, VerifyValue::integerVerify(15, false, 10, 20));
        // Out of range with a default fallback
        $this->assertSame(-1, VerifyValue::integerVerify(5, -1, 10, 20));
        $this->assertSame('fallback', VerifyValue::integerVerify('abc', 'fallback'));
    }

    /**
     * Verifies integer validation with alternative numeric bases.
     *
     * @return void
     */
    public function testIntegerVerifyHandlesAlternativeBases(): void {
        $this->assertSame(26, VerifyValue::integerVerify('0x1A', false, null, null, false, true));
        $this->assertFalse(VerifyValue::integerVerify('0x1A'));

        $this->assertSame(10, VerifyValue::integerVerify('012', false, null, null, true));
        $this->assertFalse(VerifyValue::integerVerify('012'));
    }

    /**
     * Verifies the option-array integer wrapper forwards recognised keys.
     *
     * @return void
     */
    public function testIntVerifyForwardsOptionsAndIgnoresUnknownKeys(): void {
        $this->assertSame(42, VerifyValue::intVerify('42'));
        $this->assertSame(-1, VerifyValue::intVerify('50', [
            'min'     => 1,
            'max'     => 10,
            'default' => -1,
        ]));
        $this->assertSame(5, VerifyValue::intVerify('5', [
            'min'       => 1,
            'max'       => 10,
            'unknown'   => 'ignored',
            'somethingElse' => true,
        ]));
        $this->assertSame(26, VerifyValue::intVerify('0x1A', ['allowHex' => true]));
    }

    /**
     * Verifies float validation, including ranges and thousand separators.
     *
     * @return void
     */
    public function testFloatVerifyValidatesValuesAndSeparators(): void {
        $this->assertSame(1.5, VerifyValue::floatVerify('1.5'));
        $this->assertSame(1234.5, VerifyValue::floatVerify('1,234.5', false, null, null, true));
        $this->assertFalse(VerifyValue::floatVerify('1,234.5'));
        $this->assertFalse(VerifyValue::floatVerify('abc'));

        $this->assertFalse(VerifyValue::floatVerify('0.5', false, 1, 10));
        $this->assertSame(2.5, VerifyValue::floatVerify('2.5', false, 1, 10));
        $this->assertSame(0.0, VerifyValue::floatVerify('0.5', 0.0, 1, 10));
    }

    /**
     * Verifies regular expression validation and fallbacks.
     *
     * @return void
     */
    public function testRegexVerifyMatchesPatternsAndAppliesDefaults(): void {
        $this->assertSame('abc', VerifyValue::regexVerify('abc', '/^[a-c]+$/'));
        $this->assertNull(VerifyValue::regexVerify('xyz', '/^[a-c]+$/'));
        $this->assertSame('def', VerifyValue::regexVerify('xyz', '/^[a-c]+$/', 'def'));
    }

    /**
     * Verifies domain validation with and without strict hostname rules.
     *
     * @return void
     */
    public function testDomainVerifyValidatesDomainsAndHostnames(): void {
        $this->assertSame('example.com', VerifyValue::domainVerify('example.com'));
        $this->assertSame('sub.example.co.za', VerifyValue::domainVerify('sub.example.co.za', true));
        $this->assertNull(VerifyValue::domainVerify('-bad-', true));
        $this->assertNull(VerifyValue::domainVerify('exa mple.com', true));
    }

    /**
     * Verifies IP validation across versions and range policies.
     *
     * @return void
     */
    public function testIpVerifyHonoursVersionAndRangePolicies(): void {
        // Both versions accepted by default
        $this->assertSame('127.0.0.1', VerifyValue::ipVerify('127.0.0.1'));
        $this->assertSame('::1', VerifyValue::ipVerify('::1'));
        $this->assertNull(VerifyValue::ipVerify('not-an-ip'));

        // Version gating
        $this->assertSame('8.8.8.8', VerifyValue::ipVerify('8.8.8.8', true, false));
        $this->assertNull(VerifyValue::ipVerify('::1', true, false));
        $this->assertSame('2001:db8::1', VerifyValue::ipVerify('2001:db8::1', false));
        $this->assertNull(VerifyValue::ipVerify('8.8.8.8', false));

        // Range policies
        $this->assertNull(VerifyValue::ipVerify('192.168.1.1', true, true, true));
        $this->assertSame('8.8.8.8', VerifyValue::ipVerify('8.8.8.8', true, true, true));
        $this->assertNull(VerifyValue::ipVerify('0.0.0.0', true, true, false, true));
        $this->assertNull(VerifyValue::ipVerify('192.168.1.1', true, true, false, false, true));
    }

    /**
     * Verifies MAC validation and separator normalisation.
     *
     * @return void
     */
    public function testMacVerifyNormalisesValidAddresses(): void {
        $this->assertSame('aa:bb:cc:dd:ee:ff', VerifyValue::macVerify('AA-BB-CC-DD-EE-FF', true));
        $this->assertSame('aa.bb.cc.dd.ee.ff', VerifyValue::macVerify('AA:BB:CC:DD:EE:FF', true, '.'));
        $this->assertSame('aa-bb-cc-dd-ee-ff', VerifyValue::macVerify('aa:bb:cc:dd:ee:ff', true, '-'));
        $this->assertNull(VerifyValue::macVerify('invalid-mac'));
    }

    /**
     * Verifies MAC addresses are returned untouched when normalisation is off.
     *
     * @return void
     */
    public function testMacVerifyReturnsRawValueWithoutNormalisation(): void {
        $this->assertSame('AA-BB-CC-DD-EE-FF', VerifyValue::macVerify('AA-BB-CC-DD-EE-FF'));
        $this->assertSame('aa:bb:cc:dd:ee:ff', VerifyValue::macVerify('aa:bb:cc:dd:ee:ff'));
        $this->assertNull(VerifyValue::macVerify('AA:BB:CC:DD:EE'));
    }
}
