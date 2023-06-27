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

namespace Inane\Stdlib\Object;

use function get_class_methods;
use function in_array;
use function is_null;
use function lcfirst;
use function property_exists;
use function str_replace;
use function ucwords;
use const null;

use Inane\Stdlib\Exception\{
	InvalidPropertyException,
	ParseMethodException
};

/**
 * MagicPropertyTrait
 *
 * Adds Getters / Setters via magic get / get methods
 * 
 * @since 0.4.5
 *
 * @version 0.1.0
 * 
 * @package Inane\Stdlib\Object
 */
trait MagicPropertyTrait {
	/**
	 * Getter method identifier
	 *
	 * @var string
	 */
	protected static string $MAGIC_PROPERTY_GET = 'get';

	/**
	 * Setter method identifier
	 *
	 * @var string
	 */
	protected static string $MAGIC_PROPERTY_SET = 'set';

	/**
	 * Only allow properties found in `magic_property_properties` else throw exception
	 *
	 * @var bool
	 */
	protected static bool $verify = true;

	/**
	 * Gets the method name based on the property name
	 *
	 * @param string $property - property name
	 * @param null|string $prepend - string identifying method (get/set/store/fetch/put/...)
	 *
	 * @return string - the method name
	 *
	 * @throws \Inane\Stdlib\Exception\ParseMethodException 
	 */
	protected function parseMethodName(string $property, ?string $prepend = null): string {
		$methodName = $prepend . str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
		if (is_null($prepend)) $methodName = lcfirst($methodName);

		if (!in_array($methodName, get_class_methods(__CLASS__))) throw new ParseMethodException($methodName);

		return $methodName;
	}

	/**
	 * magic method: __get
	 *
	 * @param string $property - property name
	 *
	 * @return mixed the value of $property
	 *
	 * @throws \Inane\Stdlib\Exception\InvalidPropertyException 
	 * @throws \Inane\Stdlib\Exception\ParseMethodException 
	 */
	public function __get(string $property): mixed {
		if (static::$verify)
			if (property_exists(__CLASS__, 'magic_property_properties')) {
				if (!in_array($property, $this->magic_property_properties)) throw new InvalidPropertyException("$property: not allowed in `magic_property_properties`", 13);
			} else if (!property_exists(__CLASS__, $property))
				throw new InvalidPropertyException("Property Invalid: $property", 13);

		$method = $this->parseMethodName($property, static::$MAGIC_PROPERTY_GET);
		return $this->$method();
	}

	/**
	 * magic method: __set
	 *
	 * @param string $property - property name
	 * @param mixed $value - new property value
	 *
	 * @return void
	 *
	 * @throws \Inane\Stdlib\Exception\InvalidPropertyException 
	 * @throws \Inane\Stdlib\Exception\ParseMethodException 
	 */
	public function __set(string $property, mixed $value): void {
		if (static::$verify)
			if (property_exists(__CLASS__, 'magic_property_properties')) {
				if (!in_array($property, $this->magic_property_properties)) throw new InvalidPropertyException("$property: not allowed in `magic_property_properties`", 13);
			} else if (!property_exists(__CLASS__, $property))
				throw new InvalidPropertyException("Property Invalid: $property", 13);

		$method = $this->parseMethodName($property, static::$MAGIC_PROPERTY_SET);
		$this->$method($value);
	}
}
