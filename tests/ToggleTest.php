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

use Inane\Stdlib\Control\Toggle;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests for `Inane\Stdlib\Control\Toggle`.
 */
final class ToggleTest extends TestCase {
    /**
     * Verifies the state is stored inverted so the first toggle yields the initial state.
     *
     * @return void
     */
    public function testConstructorStoresInitialStateInverted(): void {
        $on = new Toggle(true);
        $off = new Toggle(false);

        $this->assertFalse($on->state);
        $this->assertTrue($off->state);

        $this->assertTrue($on->nextState);
        $this->assertFalse($off->nextState);
    }

    /**
     * Verifies the state alternates on every toggle.
     *
     * @return void
     */
    public function testNextStateAlternates(): void {
        $toggle = new Toggle();

        $this->assertTrue($toggle->nextState);
        $this->assertFalse($toggle->nextState);
        $this->assertTrue($toggle->nextState);
    }

    /**
     * Verifies reading the current state does not toggle it.
     *
     * @return void
     */
    public function testStateReadDoesNotToggle(): void {
        $toggle = new Toggle();

        $this->assertTrue($toggle->nextState);

        $this->assertTrue($toggle->state);
        $this->assertTrue($toggle->state);
    }

    /**
     * Verifies the current value maps to the on/off values without toggling.
     *
     * @return void
     */
    public function testValueMapsToCurrentStateWithoutToggling(): void {
        $toggle = new Toggle(true, false, 'on', 'off');

        $this->assertSame('off', $toggle->value);
        $this->assertSame('off', $toggle->value);
    }

    /**
     * Verifies toggling returns the value matching the new state.
     *
     * @return void
     */
    public function testNextValueTogglesAndReturnsMatchingValue(): void {
        $toggle = new Toggle(true, true, 'on', 'off');

        $this->assertSame('on', $toggle->nextValue);
        $this->assertSame('off', $toggle->nextValue);
        $this->assertSame('on', $toggle->nextValue);
    }

    /**
     * Verifies `$next` returns the associated values when value mode is enabled.
     *
     * @return void
     */
    public function testNextReturnsValueWhenUseValueIsTrue(): void {
        $toggle = new Toggle(true, true, 'odd', 'even');

        $this->assertSame('odd', $toggle->next);
        $this->assertSame('even', $toggle->next);
    }

    /**
     * Verifies `$next` returns the boolean state when value mode is disabled.
     *
     * @return void
     */
    public function testNextReturnsStateWhenUseValueIsFalse(): void {
        $toggle = new Toggle(true, false, 'odd', 'even');

        $this->assertTrue($toggle->next);
        $this->assertFalse($toggle->next);
    }

    /**
     * Verifies invoking without a mode selector follows the value mode flag.
     *
     * @return void
     */
    public function testInvokeWithoutArgumentFollowsUseValueFlag(): void {
        $state = new Toggle(true, false, 'on', 'off');
        $value = new Toggle(true, true, 'on', 'off');

        $this->assertTrue($state());
        $this->assertSame('on', $value());
    }

    /**
     * Verifies the mode selector overrides the value mode flag.
     *
     * @return void
     */
    public function testInvokeModeSelectorOverridesUseValueFlag(): void {
        $toggle = new Toggle(true, false, 'on', 'off');

        $this->assertSame('on', $toggle(true));
        $this->assertFalse($toggle(false));
        $this->assertSame('on', $toggle(true));
    }

    /**
     * Verifies non-string values, including objects and null, are supported.
     *
     * @return void
     */
    public function testSupportsObjectAndNullValues(): void {
        $on = new stdClass();
        $toggle = new Toggle(true, true, $on, null);

        $this->assertSame($on, $toggle->nextValue);
        $this->assertNull($toggle->nextValue);
    }

    /**
     * Verifies numeric values are returned unchanged.
     *
     * @return void
     */
    public function testSupportsNumericValues(): void {
        $toggle = new Toggle(true, true, 1, 0.5);

        $this->assertSame(1, $toggle->nextValue);
        $this->assertSame(0.5, $toggle->nextValue);
    }

    /**
     * Verifies a toggle drives alternating output over a collection.
     *
     * @return void
     */
    public function testAlternatesAcrossIteration(): void {
        $toggle = new Toggle(true, true, 'odd', 'even');
        $rows = [];

        for ($i = 0; $i < 4; $i++) {
            $rows[] = $toggle->next;
        }

        $this->assertSame(['odd', 'even', 'odd', 'even'], $rows);
    }
}
