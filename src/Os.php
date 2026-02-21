<?php

/**
 * Inane: Stdlib
 *
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
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

namespace Inane\Stdlib;

use Inane\Stdlib\Enum\CoreEnumInterface;

use function strcasecmp;

use const false;
use const null;
use const PHP_OS_FAMILY;
use const true;

/**
 * Enum: Operating Systems
 *
 * @version 0.1.0
 */
enum Os: string implements CoreEnumInterface {
	case BSD = 'bsd';
	case Darwin = 'darwin';
	case Linux = 'linux';
	case Solaris = 'solaris';
	case Windows = 'windows';
	case Unknown = 'unknown';

	/**
     * Example implementation: Try get enum from name
     *
     * @param string $name
     * @param bool   $ignoreCase case insensitive option
     *
     * @return null|static
     */
    public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
        foreach (static::cases() as $case)
            if (($ignoreCase && strcasecmp($case->name, $name) == 0) || $case->name === $name)
                return $case;

        return null;
    }

	/**
	 * Try to identify current Operating System
	 *
	 * @param null|string $phpOsFamily contents of `PHP_OS_FAMILY` constant
	 *
	 * @return static operating system
	 */
	public static function Identify(?string $phpOsFamily = null): static {
		return static::tryFromName($phpOsFamily ?? PHP_OS_FAMILY, true) ?? static::Unknown;
	}
}
