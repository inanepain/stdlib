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

namespace Inane\Stdlib\String;

use NumberFormatter;
use Inane\Stdlib\{
	Array\OptionsInterface,
	Exception\RuntimeException,
	Options
};
use Psr\Container\{
	ContainerExceptionInterface,
	NotFoundExceptionInterface
};

/**
 * NumberFormatterTrait
 *
 * Provides number formatting functionality with locale-specific support.
 * Maintains a cached collection of NumberFormatter instances for improved performance.
 *
 * @package Inane\Stdlib\String
 * @version 0.1.0
 */
trait NumberFormatterTrait {
	/**
	 * Cache of NumberFormatter instances indexed by name.
	 * Stored as an Options container implementing OptionsInterface.
	 *
	 * @var OptionsInterface|Options
	 */
	private static OptionsInterface $formatters;

	/**
	 * Retrieves a configured NumberFormatter instance for a given name.
	 *
	 * The optional $config may be an array or an OptionsInterface. Supported option keys:
	 *  - locale   (string) e.g. 'en_ZA'
	 *  - currency (string) currency code, e.g. 'ZAR'
	 *  - pattern  (string) custom number pattern for NumberFormatter
	 *
	 * @param string                 $name   The unique name identifier for the formatter.
	 * @param array|OptionsInterface $config Optional configuration settings for the formatter.
	 *
	 * @return NumberFormatter The configured NumberFormatter instance associated with the specified name.
	 *
	 * @throws RuntimeException If the formatter cannot be created.
	 */
	public static function getNumberFormatter(string $name, array|OptionsInterface $config = []): NumberFormatter {
		if (!isset(static::$formatters)) static::$formatters = new Options();

		if (!static::$formatters->has($name)) {
			$opts = new Options([
				'locale' => 'en_ZA',
				'currency' => 'ZAR',
				// 'pattern' => '¤ ####0.00',
			])->merge($config);

			static::$formatters->set($name, new NumberFormatter($opts->locale, NumberFormatter::CURRENCY));

			if ($opts->has('pattern')) static::$formatters?->{$name}?->setPattern($opts->pattern);
		}

		return static::$formatters->{$name};
	}

	/**
	 * Formats a given number using a named formatter if available.
	 *
	 * @param string    $name   The name of the formatter to use.
	 * @param float|int $number The number to be formatted.
	 *
	 * @return false|string Returns the formatted number as a string if successful, or false if the formatter is not found.
	 *
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public static function formatNumber(string $name, float $number): false|string {
		if ($fmt = static::$formatters->get($name)) {
			return $fmt->format($number);
		}

		return false;
	}
}
