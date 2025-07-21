<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package Inane\Stdlib
 * @category converter
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
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
 *
 * @package Inane\Stdlib
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
