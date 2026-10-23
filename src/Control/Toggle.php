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

namespace Inane\Stdlib\Control;

use InvalidArgumentException;

use const false;
use const true;

/**
 * Represents a toggleable state with associated values.
 *
 * @version 0.1.0
 */
class Toggle {
    /**
     * Current state.
     *
     * @var bool
     */
    private(set) bool $state;

    /**
     * Retrieve the value based on the current state.
     *
     * @var null|mixed The true value if the state is truthy, otherwise the false value.
     */
    public null|bool|string|int|float|object $value {
        get => $this->state ? $this->on : $this->off;
    }

    /**
     * Toggles state.
     *
     * @return bool New state.
     */
    public bool $nextState {
        get => $this->state = !$this->state;
    }

    /**
     * Value based on toggle.
     *
     * @return null|bool|string|int|float|object
     */
    public null|bool|string|int|float|object $nextValue {
        get => $this->nextState ? $this->on : $this->off;
    }

    /**
     * Use values assigned rather than boolean toggle.
     *
     * @return null|bool|string|int|float|object
     */
    public null|bool|string|int|float|object $next {
        get => $this->useValue ? $this->nextValue : $this->nextState;
    }

    /**
     * Constructor for initialising the object with initial state and associated values.
     *
     * @param bool                              $initialState The initial state of the object.
     * @param bool                              $useValue     A flag to indicate the usage of a value insted of boolean state.
     * @param null|bool|string|int|float|object $on           The value to use when the state is "on".
     * @param null|bool|string|int|float|object $off          The value to use when the state is "off".
     *
     * @return void
     *
     * @throws InvalidArgumentException If invalid types are passed for $on or $off.
     */
    public function __construct(
        bool                                               $initialState = true, private readonly bool $useValue = false,
        private readonly null|bool|string|int|float|object $on = true, private readonly null|bool|string|int|float|object $off = false,
    ) {
        $this->state = !$initialState;
    }

    /**
     * Invokes toggle logic.
     *
     * If $getValue is null (default), returns the next value based on the $useValue flag when initialising the object.
     * - true will return the values stored in $on and $off.
     * - false will return the boolean state.
     *
     * @param ?bool $getValue Mode selector.
     *
     * @return null|bool|string|int|float|object Result.
     */
    public function __invoke(?bool $getValue = null): null|bool|string|int|float|object {
        return match ($getValue) {
            true => $this->nextValue,
            false => $this->nextState,
            null => $this->next,
        };
    }
}
