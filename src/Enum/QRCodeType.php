<?php

/**
 * Inane: Stdlib
 * Common classes, tools and utilities used throughout the inanepain libraries.
 * $Id$
 * $Date$
 * PHP version 8.4
 *
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\stdlib
 * @category stdlib
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Enum;

use Inane\Stdlib\Exception\ParseException;
use function strcasecmp;
use Uri\Rfc3986\Uri;

use const false;
use const null;

/**
 * QRCodeType Enum
 * - URL: 'https://example.com'
 * - Text: 'Hello world'
 * - vCard: "BEGIN:VCARD\nVERSION:3.0\nN:Doe;John\nFN:John Doe\nTEL:123456789\nEMAIL:john@example.com\nEND:VCARD"
 * - Email: 'mailto:info@example.com?subject=Hi'
 * - SMS: 'SMSTO:123456789:Message here'
 * - WiFi: 'WIFI:T:WPA;S:mynetwork;P:mypassword;;'
 * - Geo: 'geo:37.7749,-122.4194'
 * - Phone: 'tel:+123456789'
 */
enum QRCodeType: string {
	case WiFi  = 'WIFI';
	case Email = 'mailto';
	case SMS   = 'SMSTO';
	case Text  = ''; // NONE OF THE OTHERS
	case URL   = '*protocal*:';
	case Geo   = 'geo';
	case Phone = 'tel';
	case vCard = 'BEGIN';
//	case vCard = 'BEGIN:VCARD';

	/**
	 * Attempts to create an instance of the enum from the given name.
	 *
	 * @param string $name       The name of the enum case to match.
	 * @param bool   $ignoreCase Whether to perform a case-insensitive match. Defaults to false.
	 *
	 * @return static|null Returns an instance of the enum if a match is found, or null otherwise.
	 */
	public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
		foreach(static::cases() as $case) if (($ignoreCase && strcasecmp($case->name, $name) == 0) || $case->name === $name) return $case;

		return null;
	}

	/**
	 * Identifies the QR Type of the given string based on its format.
	 *
	 * @param string $text The input string to be analyzed.
	 *
	 * @return static Returns the identified type as a static value.
	 */
	public static function identifyType(string $text): static {
		$pos = strpos($text, ':');
		if ($pos === false) return static::Text;

		$key = substr($text, 0, $pos);
		if ($type = static::tryFrom($key)) return $type;

		try {
			new Uri($text);
		} catch (\Exception) {
			return static::Text;
		}
		return static::URL;
	}
}
