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
	case Ignore     	  = 'Ignore';
	case UPPERCASE  	  = 'UPPERCASE';
	case lowercase  	  = 'lowercase';
	case PascalCase 	  = 'PascalCase';
	case camelCase  	  = 'camelCase';
	case snake_case		  = 'snake_case';
	case UPPER_SNAKE_CASE = 'UPPER_SNAKE_CASE';
	case KebabCase  	  = 'kebab-case';
	case RaNDom     	  = 'RaNDom';
	case Unknown    	  = 'unknown';

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
			static::PascalCase => 'Change String To PascalCase',
			static::camelCase => 'Change String To camelCase',
			static::snake_case => 'Change String To snake_case',
			static::UPPER_SNAKE_CASE => 'Change String To UPPER_SNAKE_CASE',
			static::KebabCase => 'Change String To kebab-case',
			static::RaNDom => 'chANGe StRInG to rAnDOm CApITaliSAtIOn',
			static::Unknown => 'Could be UPPER or lower case',
		};
	}

	/**
	 * Creates a new instance of the class from the given string.
	 *
	 * @param string $string The input string to be used for instantiation.
	 * 
	 * @return static An instance of the called class.
	 */
	public static function fromString(string $string): static {
		if (preg_match('/^[a-z]+(?:-[a-z]+)*$/', $string)) return static::KebabCase;
		elseif (preg_match('/^[a-z]+(?:[A-Z][a-z0-9]*)*$/', $string)) return static::camelCase;
		elseif (preg_match('/^[A-Z][a-z0-9]*(?:[A-Z][a-z0-9]*)*$/', $string)) return static::PascalCase;
		elseif (preg_match('/^[a-z]+(?:_[a-z0-9]+)*$/', $string)) return static::snake_case;
		elseif (preg_match('/^[A-Z]+(?:_[A-Z0-9]+)*$/', $string)) return static::UPPER_SNAKE_CASE;
		elseif (preg_match('/^[a-z]+$/', $string)) return static::lowercase;
		elseif (preg_match('/^[A-Z]+$/', $string)) return static::UPPERCASE;

		return static::Unknown;
	}
}
