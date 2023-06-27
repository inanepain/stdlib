<?php

/** Inane: Stdlib
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
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
	 * Try return enum from supplied $name value with optional case insensitivity
	 *
	 * @param string $name			value to match as enum name
	 * @param bool   $ignoreCase	case insensitive option
	 *
	 * @return null|static enum if matched or `null` on failure.
	 */
	public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
		foreach (static::cases() as $case)
			if (($ignoreCase && strcasecmp($case->name, $name) == 0) || $case->name === $name)
				return $case;

		return null;
	}
}
