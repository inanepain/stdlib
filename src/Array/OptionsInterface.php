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
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\Stdlib\Array;

use ArrayAccess;
use Countable;
use Iterator;
use Psr\Container\ContainerInterface;
use Inane\Stdlib\Converters\{
    Arrayable,
    JSONable,
    XMLable
};

/**
 * Interface: Options
 */
interface OptionsInterface extends ArrayAccess, Iterator, Countable, ContainerInterface, Arrayable, JSONable, XMLable {
}
