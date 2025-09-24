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

namespace Inane\Stdlib\Enum;

use function strcasecmp;

use const false;
use const null;

/**
 * Core Enum Trait
 *
 * @since 0.4.6
 *
 * @version 0.1.0
 */
trait CoreEnumTrait {
	/**
	 * Attempts to create an instance of the enum from the given name.
	 *
	 * @param string $name The name of the enum case to match.
	 * @param bool $ignoreCase Whether to perform a case-insensitive match. Defaults to false.
	 *
	 * @return static|null Returns an instance of the enum if a match is found, or null otherwise.
	 */
	public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
		foreach (static::cases() as $case)
			if (($ignoreCase && strcasecmp($case->name, $name) == 0) || $case->name === $name)
				return $case;

		return null;
	}
}
