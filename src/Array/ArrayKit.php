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

namespace Inane\Stdlib\Array;

use ArrayAccess;

use function array_key_exists;
use function array_push;
use function call_user_func;
use function count;
use function function_exists;
use function in_array;
use function is_null;
use function random_int;
use const null;

use Inane\Stdlib\{
	Converters\Arrayable,
	Options
};

/**
 * Array Function Toolkit: Array object that handles an assortment of php array functions in an OO manner.
 *
 * AFT attempts to create a OO array with the `array_*` functions as methods.
 * The various functions differ and AFT uses rule groups to handle these differences.
 *
 * This is more for shits and giggles, like many of my classes, than any real use case.
 * But it does allow for easy chaining of array functions an can neaten code in various situations.
 * Of course those or more side effects then planned features but it does not make them any less nifty.
 *
 * @todo convert individual rule properties to a single rules array property
 * @todo enable adding custom rules by setting allowModifications to true
 *
 * plain method listed bellow have been tested, but only their simplest use case.
 * @method int       count()																Counts all elements in the array
 * @method string    implode(string $separator, array $array)								Join array elements with a string
 *
 * `array_` methods listed bellow have been tested, but only their simplest use case.
 * @method array     column(int|string|null $key, int|string|null $index = null))   		Return the values from a single column in the input array
 * @method ArrayKit  fill(int $start_index, int $count, mixed $value)               		Fill an array with values
 * @method array     filter(callable $func)                                         		Filters elements this array using a callback function
 * @method ArrayKit  flip()                                                         		Exchanges all keys with their associated values in an array
 * @method bool      keyExists(string|int $key)                                     		Checks if the given key or index exists in the array
 * @method array     map(callable $func)                                            		Applies the callback to the elements of this array
 * @method ArrayKit  merge(array $array)                                            		Merges an array into this array
 * @method mixed     pop()                                                          		Pop the element off the end of array
 * @method int       push(mixed $values)                                            		Push one or more elements onto the end of array
 * @method mixed     shift()                                                        		Shift an element off the beginning of array
 * @method array     slice(int $offset, ?int $length = null, bool $preserve_keys = false)	Extract a slice of the array
 * @method array     splice(int $offset, ?int $length = null, mixed $replacement = [])		Remove a portion of the array and replace it with something else
 * @method int|float sum()                                                          		Calculate the sum of values in an array
 * @method int       unshift(mixed $values)                                         		Prepend one or more elements to the beginning of an array
 * @method array     walk(callable $func)                                           		Apply a user supplied function to every member of this array
 *
 * @version 0.2.1
 */
class ArrayKit implements Arrayable, ArrayAccess {
	/**
	 * Allow Modifications
	 *
	 * Setting this to true allows adding custom array methods to the object.
	 *
	 * Use this to enable `ArrayKit` to handle more functions without throwing an error.
	 *
	 * NOTE: consider emailing me the details of the function so I can add it to the defaults.
	 *
	 * @var bool
	 */
	public static bool $allowModifications = false;

	/**
	 * List of array functions that have the array before the arguments
	 *
	 * @var array
	 */
	private static array $before = [
		'array_column',
		'array_filter',
		'array_push',
		'array_slice',
		'array_splice',
		'array_unshift',
		'array_walk',
		'count',
	];

	/**
	 * List of array functions that only have the array as argument
	 *
	 * @var array
	 */
	private static array $self = [
		'array_flip',
		'array_pop',
		'array_shift',
		'array_sum',
	];

	/**
	 * List of array functions that arguments other than the array
	 *
	 * @var array
	 */
	private static array $other = [
		'array_fill',
	];

	/**
	 * List of array functions that are called without `array_` in their name
	 *
	 * @var array
	 */
	private static array $plain = [
		'count',
		'implode',
	];

	/**
	 * List of array functions that update self
	 *
	 * @var array
	 */
	private static array $store = [
		'array_fill',
	];

	/**
	 * List of array functions that return a ArrayKit instance
	 *
	 * @var array
	 */
	private static array $returnInstance = [
		'array_flip',
		'array_merge',
	];

	/**
	 * List of array functions to allow calling on the array.
	 *
	 * These functions have generally gone through some rudimentary testing.
	 * And should work at least when using the simplest options.
	 *
	 * @var array
	 */
	private static array $enable = [
		'array_column',
		'array_fill',
		'array_filter',
		'array_flip',
		'array_key_exists',
		'array_map',
		'array_merge',
		'array_pop',
		'array_push',
		'array_shift',
		'array_slice',
		'array_splice',
		'array_sum',
		'array_unshift',
		'array_walk',
		'count',
		'implode',
	];

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(private array $data = []) {
	}

	/**
	 * Whether an offset exists
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	public function offsetExists(mixed $offset): bool {
		return isset($this->data[$offset]) || array_key_exists($offset, $this->data);
	}

	/**
	 * Offset to retrieve
	 *
	 * @param mixed $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet(mixed $offset): mixed {
		return $this->offsetExists($offset) ? $this->data[$offset] : null;
	}

	/**
	 * Assign a value to the specified offset
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 *
	 * @return void No value is returned.
	 */
	public function offsetSet(mixed $offset, mixed $value): void {
		if (is_null($offset)) $this->data[] = $value;
        else $this->data[$offset] = $value;
	}

	/**
	 * Unset an offset
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return void No value is returned.
	 */
	public function offsetUnset(mixed $offset): void {
		if ($this->offsetExists($offset)) unset($this->data[$offset]);
	}

	/**
	 * to array
	 *
	 * @return array
	 */
	public function toArray(): array {
		return $this->data;
	}

	/**
	 * __invoke
	 *
	 * @param string $name of array method
	 * @param array $arguments method arguments
	 *
	 * @return \Inane\Stdlib\Array\ArrayKit|array|int|bool|null
	 */
	public function __call(string $name, array $arguments): static|array|bool|int|string|null {
		if (in_array($name, static::$plain)) {
			$func = $name;
		} else $func = 'array_' . \Inane\Stdlib\String\Inflector::underscore($name);

		if (function_exists($func)) {
			if (!in_array($func, static::$enable))
				trigger_error("Untested function: `$func`");

			if (count($arguments) == 0 && in_array($func, static::$self))
				$result = $func($this->data);
			else if (in_array($func, static::$before))
				$result = $func($this->data, ...$arguments);
			else if (in_array($func, static::$other))
				$result = $func(...$arguments);
			else {
				array_push($arguments, $this->data);
				$result = @call_user_func($func, ...$arguments);
			}

			if (in_array($func, static::$returnInstance)) return new static($result);

			if (in_array($func, static::$store)) {
				$this->data = $result;
				return $this;
			}
			return $result;
		}

		return null;
	}

	/**
	 * Random Item
	 *
	 * @return mixed
	 */
	public function randomItem(): mixed {
		return $this->data[random_int(0, count($this->data) - 1)];
	}
}
