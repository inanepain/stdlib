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
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib;

use function is_null;
use function preg_match;

/**
 * Enum: Operating Systems
 * 
 * @version 0.1.0
 * 
 * @package Inane\Stdlib
 */
enum Os {
	case MacOS;
	case Linux;
	case Windows;
	case UNKNOWN;

	/**
	 * Identify Operating System
	 * 
	 * @param null|string $uname result of `php_uname`
	 * 
	 * @return static operating system
	 */
	public static function Identify(?string $uname = null): static {
		if (is_null($uname)) $uname = php_uname();

		if (preg_match('/darwin/i', $uname)) return Os::MacOS;
		else if (preg_match('/windows/i', $uname)) return Os::Windows;
		else return Os::Linux;
	}
}
