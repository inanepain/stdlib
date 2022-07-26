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

namespace Inane\Stdlib\String;

/**
 * String Capitalisation
 *
 * @package Inane\Stdlib
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
