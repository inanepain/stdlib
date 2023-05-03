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

use BackedEnum;

/**
 * Core Enum Interface
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.0
 */
interface CoreEnumInterface extends BackedEnum {
	/**
	 * Try parse enum from name
	 * 
	 * @param string $name			enum name
	 * @param bool   $ignoreCase	case insensitive option
	 * 
	 * @return null|static enum
	 */
    public static function tryFromName(string $name, bool $ignoreCase = false): ?static;

	/**
     * Example implementation: Try get enum from name
     *
     * @param string $name
     * @param bool   $ignoreCase case insensitive option
     *
     * @return null|static
     */
    // public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
    //     foreach (static::cases() as $case)
    //         if (($ignoreCase && strcasecmp($case->name, $name) == 0) || $case->name === $name)
    //             return $case;

    //     return null;
    // }
}
