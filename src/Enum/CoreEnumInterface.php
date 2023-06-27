<?php

/**
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

/**
 * Core Enum Interface
 * 
 * @since 0.4.4
 *
 * @version 0.1.1
 *
 * @package Inane\Stdlib
 */
interface CoreEnumInterface {
	/**
	 * Try parse enum from name
	 * 
	 * @param string $name			enum name
	 * @param bool   $ignoreCase	case insensitive option
	 * 
	 * @return null|static enum
	 */
	public static function tryFromName(string $name, bool $ignoreCase = false): ?static;
}
