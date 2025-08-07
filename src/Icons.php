<?php

/**
 * Inane: Stdlib
 *
 * Common classes, tools and utilities used throughout the inanepain libraries.
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
 * @version $version
 */

declare(strict_types=1);

namespace Inane\Stdlib;

/**
 * Icons
 *
 * Icons for easy reference
 *
 * @package Inane\Stdlib
 *
 * @version 1.0.0
 */
enum Icons: string {
    case Cross = '✗';
    case Dash = '-';
    case Tick = '✓';
}
