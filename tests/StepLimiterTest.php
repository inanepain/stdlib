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

use Inane\Stdlib\Control\StepLimiter;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Control\StepLimiter`.
 */
final class StepLimiterTest extends TestCase {
    /**
     * Verifies the constructor stores the given limit and starts unconsumed.
     *
     * @return void
     */
    public function testConstructorSetsLimit(): void {
        $limiter = new StepLimiter(3);

        $this->assertSame(3, $limiter->limit);
        $this->assertSame(0, $limiter->current);
        $this->assertSame(3, $limiter->remaining);
    }

    /**
     * Verifies the default limit is one.
     *
     * @return void
     */
    public function testConstructorDefaultsToLimitOfOne(): void {
        $limiter = new StepLimiter();

        $this->assertSame(1, $limiter->limit);
    }

    /**
     * Verifies negative limits are clamped to zero and never continue.
     *
     * @return void
     */
    public function testConstructorClampsNegativeLimitToZero(): void {
        $limiter = new StepLimiter(-5);

        $this->assertSame(0, $limiter->limit);
        $this->assertSame(0, $limiter->remaining);
        $this->assertFalse($limiter->canContinue);
    }

    /**
     * Verifies the static factory matches constructor behaviour.
     *
     * @return void
     */
    public function testWithLimitCreatesConfiguredInstance(): void {
        $limiter = StepLimiter::withLimit(3);

        $this->assertInstanceOf(StepLimiter::class, $limiter);
        $this->assertSame(3, $limiter->limit);
        $this->assertSame(3, $limiter->remaining);
    }

    /**
     * Verifies the static factory default limit is one.
     *
     * @return void
     */
    public function testWithLimitDefaultsToLimitOfOne(): void {
        $this->assertSame(1, StepLimiter::withLimit()->limit);
    }

    /**
     * Verifies the static factory clamps negative limits to zero.
     *
     * @return void
     */
    public function testWithLimitClampsNegativeLimitToZero(): void {
        $limiter = StepLimiter::withLimit(-5);

        $this->assertSame(0, $limiter->limit);
        $this->assertFalse($limiter->canContinue);
    }

    /**
     * Verifies reading the continue check consumes a step.
     *
     * @return void
     */
    public function testCanContinueConsumesStepsUntilLimitExceeded(): void {
        $limiter = new StepLimiter(2);

        $this->assertTrue($limiter->canContinue);
        $this->assertTrue($limiter->canContinue);
        $this->assertFalse($limiter->canContinue);
    }

    /**
     * Verifies progress properties track consumed steps.
     *
     * @return void
     */
    public function testCurrentAndRemainingTrackProgress(): void {
        $limiter = new StepLimiter(3);

        $step = $limiter->canContinue;

        $this->assertTrue($step);
        $this->assertSame(1, $limiter->current);
        $this->assertSame(2, $limiter->remaining);

        $step = $limiter->canContinue;

        $this->assertTrue($step);

        $this->assertSame(2, $limiter->current);
        $this->assertSame(1, $limiter->remaining);
    }

    /**
     * Verifies the remaining count never drops below zero.
     *
     * @return void
     */
    public function testRemainingNeverDropsBelowZero(): void {
        $limiter = new StepLimiter(1);

        $steps = [$limiter->canContinue, $limiter->canContinue, $limiter->canContinue];

        $this->assertSame([true, false, false], $steps);

        $this->assertSame(3, $limiter->current);
        $this->assertSame(0, $limiter->remaining);
    }

    /**
     * Verifies invoking the object performs the continue check.
     *
     * @return void
     */
    public function testInvokeReturnsContinueStatus(): void {
        $limiter = new StepLimiter(2);

        $this->assertTrue($limiter());
        $this->assertTrue($limiter());
        $this->assertFalse($limiter());
    }

    /**
     * Verifies a loop driven by the limiter runs exactly the limit times.
     *
     * @return void
     */
    public function testLoopRunsExactlyLimitTimes(): void {
        $limiter = new StepLimiter(3);
        $count = 0;

        while ($limiter->canContinue) {
            $count++;
        }

        $this->assertSame(3, $count);
    }
}
