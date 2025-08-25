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

namespace Inane\Stdlib\Hash;

use Inane\Stdlib\Enum\CoreEnumInterface;

use function boolval;
use function ctype_xdigit;
use function is_int;
use function preg_match;
use function strcasecmp;
use function strlen;
use function strtolower;
use const false;
use const HASH_TEST_METHOD_CTYPE;
use const HASH_TEST_METHOD_REGEX;
use const null;

/**
 * Enum: Hash
 *
 * This enum contains only a few common hash types, since many create similar strings.
 *
 * Testing Method:
 * By default regex (`preg_match`) is used to test the hash key but this can be changed to use ctype (`ctype_xdigit`) by defining the global constant **HASH_TEST_METHOD**.
 *
 * 0 - preg_match (or use const HASH_TEST_METHOD_REGEX)
 * 1 - ctype_xdigit (or use const HASH_TEST_METHOD_CTYPE)
 *
 * Example:
 * define('HASH_TEST_METHOD', HASH_TEST_METHOD_CTYPE);
 *
 * @version 0.1.0
 */
enum HashType: int implements CoreEnumInterface {
	/**
	 * Hash Type: CRC32
	 */
	case CRC32	= 8;

	/**
	 * Hash Type: MD5
	 */
	case MD5	= 32;

	/**
	 * Hash Type: SHA1
	 */
	case SHA1	= 40;

	/**
	 * Hash Type: SHA224
	 */
	case SHA224	= 56;

	/**
	 * Hash Type: SHA256
	 */
	case SHA256	= 64;

	/**
	 * Hash Type: SHA384
	 */
	case SHA384	= 96;

	/**
	 * Hash Type: SHA512
	 */
	case SHA512	= 128;

	/**
	 * Get method used for hash testing
	 *
	 * @return int hash test method id
	 */
	public static function method(): int {
		return defined('HASH_TEST_METHOD') && is_int(HASH_TEST_METHOD) && HASH_TEST_METHOD <= 1  ? HASH_TEST_METHOD : HASH_TEST_METHOD_REGEX;
	}

	/**
	 * Get method used for hash testing
	 *
	 * @return string hash test method name
	 */
	public static function methodName(): string {
		/**
		 * Valid test methods
		 */
		$methods = [
			'preg_match', // HASH_TEST_METHOD_REGEX
			'ctype_xdigit' // HASH_TEST_METHOD_CTYPE
		];

		return $methods[static::method()];
	}

	/**
	 * Test if $hash is valid for Hash::case
	 *
	 * @param string $hash string to test
	 * @param int $method specify alternative test method (HASH_TEST_METHOD_REGEX, HASH_TEST_METHOD_CTYPE)
	 *
	 * @return bool is valid hash of type Hash
	 */
	public function isHash(string $hash, ?int $method = null): bool {
		return match($method ?? static::method()) {
			HASH_TEST_METHOD_CTYPE => strlen($hash) === $this->value && ctype_xdigit($hash),
			default  => boolval(preg_match('/^[a-f0-9]{' . $this->value . '}$/i', $hash)),
		};
	}

	/**
	 * Hashes $value
	 *
	 * @param string $hash string to test
	 *
	 * @return string hash of $data
	 */
	public function hash(string $data): string {
		return hash(strtolower($this->name), $data, false);
	}

	/**
	 * Try get enum by name
	 *
	 * @param string $name enum name
	 *
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
	 * Get Hash from hash string
	 *
	 * @param string $hash hash string
	 *
	 * @return null|static
	 */
	public static function tryFromHash(string $hash): ?static {
		foreach (static::cases() as $case)
			if ($case->isHash(strtolower($hash)))
				return $case;

		return null;
	}
}
