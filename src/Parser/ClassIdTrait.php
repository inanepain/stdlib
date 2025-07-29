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

namespace Inane\Stdlib\Parser;

use function array_slice;
use function explode;
use function implode;
use function strtolower;
use const true;

/**
 * Class Id Trait
 *
 * @version 0.1.0
 */
trait ClassIdTrait {
	/**
	 * Build a class id based on class name.
	 *
	 * Some customisation is available.
	 *
	 * @param int $size number of parts used, namespace and class
	 * @param string $separator used when combining parts
	 * @param bool $lower convert to lowercase
	 *
	 * @return string class id
	 */
	public static function classId(int $size = 1, string $separator = '/', bool $lower = true): string {
		$ids = explode('\\', static::class);
		$cids =  array_slice($ids, $size * -1);
		$id = implode($separator, $cids);

		return $lower ? strtolower($id) : $id;
	}
}
