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
use function lcfirst;
use function property_exists;
use function str_replace;
use function ucwords;

use Inane\Stdlib\Exception\{
	InvalidPropertyException,
	ParseMethodException
};

/**
 * MagicPropertyTrait
 *
 * Adds Getters / Setters via magic get / get methods
 *
 * @package Inane\Stdlib\Property
 * 
 * @version 0.1.0
 */
trait MagicPropertyTrait {
	/**
	 * Getter method identifier
	 *
	 * @var string
	 */
	protected static $MAGIC_PROPERTY_GET = 'get';

	//     protected static string $MAGIC_PROPERTY_GET = 'get';

	/**
	 * Setter method identifier
	 *
	 * @var string
	 */
	protected static $MAGIC_PROPERTY_SET = 'set';

	//     protected static string $MAGIC_PROPERTY_SET = 'set';

	/**
	 * If property does not exist an exception is thrown
	 *
	 * @var bool
	 */
	protected static $verify = true;

	//     protected static bool $verify = true;

	/**
	 * Gets the method name based on the property name
	 *
	 * @param string $property - property name
	 * @param string $prepend - string identifying method (get/set/store/fetch/put/...)
	 *
	 * @return string - the method name
	 *
	 * @throws MethodException
	 */
	protected function parseMethodName(string $property, string $prepend = ''): string {
		$methodName = $prepend . str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
		if (!$prepend) $methodName = lcfirst($methodName);

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
	 * @throws PropertyException
	 * @throws MethodException
	 */
	public function __get(string $property) {
		if (static::$verify && property_exists(__CLASS__, 'magic_property_properties')) {
			if (!in_array($property, $this->magic_property_properties)) throw new InvalidPropertyException("Property not in array: {$property}", 13);
		} else if (static::$verify && !property_exists(__CLASS__, $property)) throw new InvalidPropertyException($property, 11);

		$method = $this->parseMethodName($property, static::$MAGIC_PROPERTY_GET);
		return $this->$method();
	}

	/**
	 * magic method: __set
	 *
	 * @param string $property - property name
	 * @param mixed $value - new property value
	 *
	 * @return mixed usually $this to support chaining
	 *
	 * @throws PropertyException
	 * @throws MethodException
	 */
	public function __set(string $property, $value) {
		if (static::$verify && property_exists(__CLASS__, 'magic_property_properties')) {
			if (!in_array($property, $this->magic_property_properties)) throw new InvalidPropertyException("Property not in array: {$property}", 14, new PropertyException());
		} else if (static::$verify && !property_exists(__CLASS__, $property)) throw new InvalidPropertyException($property, 12);

		$method = $this->parseMethodName($property, static::$MAGIC_PROPERTY_SET);
		return $this->$method($value);
	}
}
