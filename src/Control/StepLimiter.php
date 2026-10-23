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

use function max;

/**
 * Step class for tracking progress towards a limit.
 *
 * Call the object returns a boolean indicating whether the step can continue.
 *
 * @version 0.1.0
 */
class StepLimiter {
    /**
     * Current count.
     *
     * @var int
     */
    private(set) int $current = 0;

    /**
     * Calculates the remaining count based on limit and current values.
     *
     * @var int The remaining count, ensuring it is never less than 0.
     */
    public int $remaining {
        get => max($this->limit - $this->current, 0);
    }

    /**
     * Target limit.
     *
     * @var int
     */
    private(set) int $limit = 1;

    /**
     * Checks if a step can continue.
     *
     * @var bool false if count exceeds limit.
     */
    public bool $canContinue {
        get => !(++$this->current > $this->limit);
    }

    /**
     * Constructs a Step instance.
     *
     * @param int $limit Target limit (min 0).
     */
    public function __construct(int $limit = 1) {
        $this->limit = max($limit, 0);
    }

    /**
     * Creates a new StepLimiter instance with the specified limit.
     *
     * @param int $limit The limit to be applied (minimum value is 0).
     *
     * @return self A new instance of StepLimiter configured with the given limit.
     */
    public static function withLimit(int $limit = 1): self {
        return new StepLimiter($limit);
    }

    /**
     * Invokes continue check.
     *
     * @return bool Continue status.
     */
    public function __invoke(): bool {
        return $this->canContinue;
    }
}
