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
 * Tests for `Inane\Stdlib\VerifyValue`.
 */
final class VerifyValueTest extends TestCase {
    /**
     * Verifies that boolean-like strings are converted correctly.
     *
     * @return void
     */
    public function testBoolVerifyConvertsBooleanLikeStrings(): void {
        $this->assertTrue(VerifyValue::boolVerify('yes'));
        $this->assertFalse(VerifyValue::boolVerify('no'));
    }

    /**
     * Verifies that invalid boolean input can return null when requested.
     *
     * @return void
     */
    public function testBoolVerifyReturnsNullWhenNullOnFailureIsEnabled(): void {
        $this->assertNull(VerifyValue::boolVerify('not-a-bool', true));
    }

    /**
     * Verifies single email validation outcomes.
     *
     * @return void
     */
    public function testEmailVerifyHandlesSingleValues(): void {
        $this->assertSame('user@example.com', VerifyValue::emailVerify('user@example.com'));
        $this->assertFalse(VerifyValue::emailVerify('not-an-email'));
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
        $this->assertArrayHasKey('good@example.com', $result);
        $this->assertArrayHasKey('bad-email', $result);
        $this->assertSame('good@example.com', $result['good@example.com']);
        $this->assertFalse($result['bad-email']);
    }

    /**
     * Verifies MAC validation and separator normalisation.
     *
     * @return void
     */
    public function testMacVerifyNormalisesValidAddresses(): void {
        $this->assertSame('aa:bb:cc:dd:ee:ff', VerifyValue::macVerify('AA-BB-CC-DD-EE-FF', true));
        $this->assertSame('aa.bb.cc.dd.ee.ff', VerifyValue::macVerify('AA:BB:CC:DD:EE:FF', true, '.'));
        $this->assertNull(VerifyValue::macVerify('invalid-mac'));
    }
}
