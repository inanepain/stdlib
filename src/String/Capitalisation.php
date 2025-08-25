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

/**
 * String Capitalisation
 * 
 * @version 0.3.0
 */
enum Capitalisation: string {
	case Ignore     = 'Ignore';
	case UPPERCASE  = 'UPPERCASE';
	case lowercase  = 'lowercase';
	case StudlyCaps = 'StudlyCaps';
	case camelCase  = 'camelCase';
	case RaNDom     = 'RaNDom';

	/**
	 * Case Description
	 *
	 * @return string
	 */
	public function description(): string {
		return match ($this) {
			static::Ignore => 'Don\'t change case of string.',
			static::UPPERCASE => 'CHANGE STRING TO UPPERCASE',
			static::lowercase => 'change string to lowercase',
			static::StudlyCaps => 'Change String To Studlycaps',
			static::camelCase => 'change String To Camelcase',
			static::RaNDom => 'chANGe StRInG to rAnDOm CApITaliSAtIOn',
		};
	}
}
