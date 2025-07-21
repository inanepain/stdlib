<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package Inane\Stdlib
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib;

use ArrayAccess;
use Countable;
use Iterator;
use Psr\Container\ContainerInterface;
use Inane\Stdlib\Converters\{
	Arrayable,
	JSONable,
	XMLable
};
use Inane\Stdlib\Exception\{
	InvalidArgumentException,
	RuntimeException
};

use function array_key_exists;
use function array_keys;
use function array_pop;
use function array_reduce;
use function array_unique;
use function array_values;
use function count;
use function current;
use function in_array;
use function is_array;
use function is_int;
use function is_null;
use function is_string;
use function key;
use function next;
use function prev;
use function reset;

use const null;

/**
 * Options: recursive key, value store
 *
 * Provides a property based interface to an array.
 * The data can be made read-only by setting $allowModifications to false with the `lock` method.
 *
 * @package Inane\Stdlib
 *
 * @version 0.16.0
 */
class Options implements ArrayAccess, Iterator, Countable, ContainerInterface, Arrayable, JSONable, XMLable {
	#region PROPERTIES
	use Converters\ArrayToXML;

	/**
	 * Value store
	 */
	private array $data = [];
	#endregion PROPERTIES

	#region CREATE
	/**
	 * Options
	 *
	 * Create a new options object. Any invalid initial values are ignore resulting in a clean Options object being created.
	 *
	 * @since 0.10.2
	 *  - takes \ArrayObject
	 * @since 0.13.0
	 *  - takes string - json encoded string
	 * @since 0.15.0
	 *  - now also excepts an instance of itself and a null
	 *
	 * @param null|array|string|\ArrayObject|\Inane\Stdlib\ArrayObject|\Inane\Stdlib\Options $data initial data in a variety of formates
	 * @param bool $allowModifications
	 *
	 * @return void
	 */
	public function __construct(
		/**
		 * Initial value store
		 */
		null|array|string|\ArrayObject|ArrayObject|Options $data = [],
		/**
		 * Whether modifications to the data are allowed
		 */
		private bool $allowModifications = true
	) {
		if (is_string($data)) $data = Json::decode($data);
		if ($data instanceof \ArrayObject || $data instanceof ArrayObject) $data = $data->getArrayCopy();

		if ((!is_array($data) && !($data instanceof static)) || $data === null) $data = [];

		foreach ($data as $key => $value)
			if (is_array($value) || $value instanceof \ArrayObject || $value instanceof ArrayObject) $this->data[$key] = new static($value, $this->allowModifications);
			else $this->data[$key] = $value;
	}

	/**
	 * Deep clone of instance ensuring that nested <strong>Inane\Stdlib\Options</strong> are cloned.
	 *
	 * @return void
	 */
	public function __clone() {
		$array = [];

		foreach ($this->data as $key => $value)
			if ($value instanceof self) $array[$key] = clone $value;
			else $array[$key] = $value;

		$this->data = $array;
	}

	/**
	 * Recreate Options from `var_export` code
	 *
	 * @since 0.13.0
	 *
	 * @param array $properties result from `var_export`
	 *
	 * @return static Options
	 */
	public static function __set_state(array $properties): static {
		$obj = new static();
		$obj->data = $properties['data'];
		$obj->allowModifications = $properties['allowModifications'];

		return $obj;
	}
	#endregion CREATE

	#region IS_SET
	/**
	 * Test if empty
	 *
	 * @since 0.10.6
	 *
	 * @return bool
	 */
	public function empty(): bool {
		return empty($this->data);
	}

	/**
	 * Whether a data exists by key
	 *
	 * @param mixed $key A data key to check for
	 *
	 * @return boolean
	 */
	public function __isset(mixed $key): bool {
		return isset($this->data[$key]) || array_key_exists($key, $this->data);
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param mixed $id Identifier of the entry to look for.
	 *
	 * @return bool
	 */
	public function has(mixed $id): bool {
		return $this->__isset($id);
	}

	/**
	 * Key exists
	 *
	 * @param string $offset key
	 * @return bool exists
	 */
	public function offsetExists(mixed $offset): bool {
		return $this->__isset($offset);
	}

	/**
	 * valid
	 *
	 * Checks if the current element is valid
	 *
	 * @return bool valid
	 */
	public function valid(): bool {
		return (!is_null($this->key()));
	}

	/**
	 * Checks if a $value exists in an Option's values
	 *
	 * @since 0.10.3
	 *
	 * @see in_array
	 *
	 * @param mixed $value The searched value
	 * @param bool $strict If set to true then the type of the value is also checked
	 *
	 * @return bool Returns true if value is found, false otherwise
	 */
	public function contains(mixed $value, bool $strict = false): bool {
		return in_array($value, $this->toArray(), $strict);
	}
	#endregion IS_SET

	#region GETTER
	/**
	 * get value
	 *
	 * public function &__get($key) {
	 *
	 * @param mixed $key key
	 * 
	 * @return mixed|Options value
	 */
	public function __get(mixed $key) {
		return $this->get($key);
	}

	/**
	 * get key
	 *
	 * @param string $id      key
	 * @param mixed  $default value
	 *
	 * @return mixed|Options value
	 */
	public function get(mixed $id, mixed $default = null): mixed {
		return $this->offsetExists($id) ? $this->data[$id] : $default;
	}

	/**
	 * get offset
	 * @param mixed $offset offset
	 *
	 * @return mixed|Options value
	 */
	public function offsetGet(mixed $offset): mixed {
		return $this->get($offset);
	}

	/**
	 * key
	 *
	 * Fetch the key for current element
	 *
	 * @return string|float|int|bool|null key
	 */
	public function key(): string|float|int|bool|null {
		return key($this->data);
	}

	/**
	 * current
	 *
	 * Return the current element
	 *
	 * @return mixed|Options
	 */
	public function current(): mixed {
		return current($this->data);
	}
	#endregion GETTER
	
	#region SETTER
	/**
	 * Assigns a value to the specified key
	 *
	 * @param mixed $key The key to which the value will be assigned
	 * @param mixed $value The value to assign
	 *
	 * @return void
	 *
	 * @throws RuntimeException
	 */
	public function __set(mixed $key, mixed $value) {
		if ($this->allowModifications) {
			if (is_array($value)) $value = new static($value);

			if (is_null($key)) $this->data[] = $value;
			else $this->data[$key] = $value;
		} else throw new RuntimeException('Option is read only');
	}

	/**
	 * set key
	 *
	 * @param mixed $key key
	 * @param mixed $value value
	 *
	 * @return Options
	 *
	 * @throws RuntimeException
	 */
	public function set(mixed $key, mixed $value): Options {
		$this->offsetSet($key, $value);
		return $this;
	}

	/**
	 * set offset
	 *
	 * @param mixed $offset offset
	 * @param mixed $value value
	 *
	 * @return void
	 *
	 * @throws RuntimeException
	 */
	public function offsetSet(mixed $offset, mixed $value): void {
		$this->__set($offset, $value);
	}
	#endregion SETTER
	
	#region GETTER_SETTER
	/**
	 * Gets the previous value of the key being assigned a new value
	 * 
	 * @since 0.16.0
	 * 
	 * @param mixed $key The key to which the value will be assigned and who's previous value is returned
	 * @param mixed $value The value to assign
	 * 
	 * @return mixed the key's previous value
	 * 
	 * @throws \Inane\Stdlib\Exception\RuntimeException 
	 */
	public function getSet(mixed $key, mixed $value): mixed {
		$previous = $this->get($key);
		$this->set($key, $value);

		return $previous;
	}

	/**
	 * SGets the previous value of the key being assigned a new value
	 * 
	 * @since 0.16.0
	 * 
	 * @param mixed $key The key to which the value will be assigned and who's previous value is returned
	 * @param mixed $value The value to assign
	 * 
	 * @return mixed the key's previous value
	 * 
	 * @throws \Inane\Stdlib\Exception\RuntimeException 
	 */
	public function offsetGetSet(mixed $key, mixed $value): mixed {
		return $this->getSet($key, $value);
	}
	#endregion GETTER_SETTER

	#region UNSETTER
	/**
	 * Unset data by key
	 *
	 * @param string $key The key to unset
	 *
	 * @throws \Inane\Stdlib\Exception\InvalidArgumentException
	 */
	public function __unset($key) {
		if (!$this->allowModifications) throw new InvalidArgumentException('Option is read only');
		elseif ($this->__isset($key)) unset($this->data[$key]);
	}

	/**
	 * delete key
	 *
	 * @param mixed $offset key
	 *
	 * @return Options
	 *
	 * @throws \Inane\Stdlib\Exception\InvalidArgumentException
	 */
	public function unset(mixed $key): Options {
		$this->offsetUnset($key);
		return $this;
	}

	/**
	 * delete key
	 *
	 * @param string $offset key
	 *
	 * @return void
	 *
	 * @throws \Inane\Stdlib\Exception\InvalidArgumentException
	 */
	public function offsetUnset(mixed $offset): void {
		$this->__unset($offset);
	}

	/**
	 * Get value and delete key
	 *
	 * @since 0.15.0
	 *
	 * @param string $id      key
	 * @param mixed  $default value
	 *
	 * @return mixed|Options value
	 */
	public function pull(mixed $id, mixed $default = null): mixed {
		$result = $this->get($id, $default);
		$this->unset($id);
		return $result;
	}
	#endregion UNSETTER
	
	#region MERGING
	/**
	 * Merge another Options object with this one.
	 *
	 * @since 0.10.2
	 *  - takes array and \ArrayObject
	 *
	 * For duplicate keys, the following will be performed:
	 * - Nested Options will be recursively merged.
	 * - Items in $merge with INTEGER keys will be appended.
	 * - Items in $merge with STRING keys will overwrite current values.
	 *
	 * @param array|\ArrayObject|\Inane\Stdlib\ArrayObject|\Inane\Stdlib\Options $merge
	 *
	 * @return \Inane\Stdlib\Options
	 */
	public function merge(array|\ArrayObject|ArrayObject|Options $merge): self {
		if (!$merge instanceof self) $merge = new static($merge);

		/** @var Options $value */
		foreach ($merge as $key => $value) if ($this->offsetExists($key)) {
			if (is_int($key)) $this->data[] = $value;
			elseif ($value instanceof self && $this->data[$key] instanceof self) $this->data[$key]->merge($value);
			else {
				if ($value instanceof self) $this->data[$key] = new static($value->toArray(), $this->allowModifications);
				else $this->data[$key] = $value;
			}
		} else {
			if ($value instanceof self) $this->data[$key] = new static($value->toArray(), $this->allowModifications);
			else $this->data[$key] = $value;
		}

		return $this;
	}

	/**
	 * updates properties 2+ into first array with decreasing importance
	 * so only unset keys are assigned values
	 *
	 * 1 array in = same array out
	 * 0 array in = empty array out
	 *
	 * @todo: check for allowModifications
	 *
	 * @param \Inane\Stdlib\Options ...$models
	 *
	 * @return \Inane\Stdlib\Options
	 */
	public function defaults(Options ...$models): self {
		// $replaceable = ['', null, false];
		$replaceable = ['', null];

		while ($model = array_pop($models)) foreach ($model as $key => $value) {
			if ($value instanceof self && $this->offsetExists($key) && $this[$key] instanceof self) $this[$key]->defaults($value);
			else {
				if (!$this->offsetExists($key) || in_array($this[$key], $replaceable))
					$this[$key] = $value;
			}
		}
		return $this;
	}

	/**
	 * Merge an array but only updates existing keys, ignoring unmatched keys
	 *
	 * @since 0.11.0
	 *
	 * @param array|\ArrayObject|\Inane\Stdlib\ArrayObject|\Inane\Stdlib\Options $merge
	 *
	 * @return \Inane\Stdlib\Options
	 */
	public function modify(array|\ArrayObject|ArrayObject|Options $merge): self {
		if (!$merge instanceof self) $merge = new static($merge);

		/** @var Options $value */
		foreach ($merge as $key => $value) if ($this->offsetExists($key)) {
			if (is_int($key)) $this->data[] = $value;
			elseif ($value instanceof self && $this->data[$key] instanceof self) $this->data[$key]->modify($value);
			else {
				if ($value instanceof self) $this->data[$key] = new static($value->toArray(), $this->allowModifications);
				else $this->data[$key] = $value;
			}
		}

		return $this;
	}

	/**
	 * Merge an array but only adds missing keys, leaving existing keys unmodified
	 *
	 * @since 0.11.0
	 *
	 * @param array|\ArrayObject|\Inane\Stdlib\ArrayObject|\Inane\Stdlib\Options $merge
	 *
	 * @return \Inane\Stdlib\Options
	 */
	public function complete(array|\ArrayObject|ArrayObject|Options $merge): self {
		if (!$merge instanceof self) $merge = new static($merge);

		/** @var Options $value */
		foreach ($merge as $key => $value) if ($this->offsetExists($key)) {
			// if (is_int($key)) $this->data[] = $value;
			if ($value instanceof self && $this->data[$key] instanceof self) $this->data[$key]->complete($value);
			// else {
			//     if ($value instanceof self) $this->data[$key] = new static($value->toArray(), $this->allowModifications);
			//     else $this->data[$key] = $value;
			// }
		} else {
			if ($value instanceof self) $this->data[$key] = new static($value->toArray(), $this->allowModifications);
			else $this->data[$key] = $value;
		}

		return $this;
	}
	#endregion MERGING
	
	#region LOCKING
	/**
	 * Prevent any more modifications being made to this instance.
	 *
	 * Useful after merge() has been used to merge multiple Options objects
	 * into one object which should then not be modified again.
	 *
	 * @return Options
	 */
	public function lock(): self {
		$this->allowModifications = false;

		/** @var Options $value */
		foreach ($this->data as $value) if ($value instanceof self) $value->lock();

		return $this;
	}

	/**
	 * Returns whether this Options object is locked or not.
	 *
	 * @return bool
	 */
	public function isLocked(): bool {
		return !$this->allowModifications;
	}
	#endregion LOCKING
	
	#region NAVIGATION
	/**
	 * previous
	 *
	 * Rewinds the internal pointer by 1
	 *
	 * @since 0.10.6
	 *
	 * @return void
	 */
	public function prev(): void {
		prev($this->data);
	}

	/**
	 * next
	 *
	 * Advance the internal pointer
	 *
	 * @return void
	 */
	public function next(): void {
		next($this->data);
	}

	/**
	 * rewind
	 *
	 * Rewind the internal pointer to the first element
	 *
	 * @return void
	 */
	public function rewind(): void {
		reset($this->data);
	}
	#endregion NAVIGATION
	
	#region OTHER
	/**
	 * count
	 *
	 * Counts all elements
	 *
	 * @return int item count
	 */
	public function count(): int {
		return count($this->data);
	}

	/**
	 * UNIQUE
	 *
	 * Filters unique items
	 *
	 * @since 0.14.0
	 *
	 * @return \Inane\Stdlib\Options unique items
	 */
	public function unique(): static {
		return new static(array_unique($this->toArray()));
	}
	#endregion OTHER
	
	#region EXPORTING
	/**
	 * Make Options play nicely with var_dump
	 *
	 * @return array
	 */
	public function __debugInfo(): array {
		return $this->toArray();
	}
	
	/**
	 * Returns keys
	 *
	 * @since 0.10.3
	 *
	 * @return array keys
	 */
	public function keys(): array {
		return array_keys($this->toArray());
	}

	/**
	 * Returns values
	 *
	 * If this object is locked,
	 *  the values are locked too.
	 *
	 * @todo: should values inherit lock status?
	 *
	 * @since 0.10.3
	 *
	 * @return iterable|static values
	 */
	public function values(): iterable|static {
		$values = array_values($this->toArray());
		return new static($values, $this->allowModifications);
	}

	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$array = [];
		$data = $this->data;

		/** @var self $value */
		foreach ($data as $key => $value) if ($value instanceof self) $array[$key] = $value->toArray();
		else $array[$key] = $value;

		return $array;
	}

	/**
	 * Return data as an XML string
	 *
	 * @since 0.10.3
	 *
	 * @return string XML string
	 */
	public function toXML(): string {
		return static::arrayToXML($this->toArray())->asXML();
	}

	/**
	 * Return data as an JSON string
	 *
	 * Support for flags from <strong>json_encode</strong>.
	 *
	 * Pretty for the eyes (224):<br />
	 * - JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
	 *
	 * Good for inserting SQL (46):<br />
	 * - JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
	 *
	 * @since 0.10.4
	 *
	 * @see http://www.php.net/manual/en/json.constants.php JSON Constants
	 *
	 * @param int $flags Bitmask consisting of `JSON_FORCE_OBJECT`, `JSON_HEX_QUOT`, `JSON_HEX_TAG`, `JSON_HEX_AMP`, `JSON_HEX_APOS`, `JSON_INVALID_UTF8_IGNORE`, `JSON_INVALID_UTF8_SUBSTITUTE`, `JSON_NUMERIC_CHECK`, `JSON_PARTIAL_OUTPUT_ON_ERROR`, `JSON_PRESERVE_ZERO_FRACTION`, `JSON_PRETTY_PRINT`, `JSON_UNESCAPED_LINE_TERMINATORS`, `JSON_UNESCAPED_SLASHES`, `JSON_UNESCAPED_UNICODE`, `JSON_THROW_ON_ERROR`. The behaviour of these constants is described on the `JSON constants` page.
	 * @param int $depth Set the maximum depth. Must be greater than zero.
	 *
	 * @return string JSON string
	 */
	public function toJSON(array|int $flags = 0, int $depth = 512): string {
		if (is_array($flags)) return Json::encode($this->toArray(), $flags);

		return Json::encode($this->toArray(), ['flags' => $flags, 'depth' => $depth]);
	}

	/**
	 * Return new Options group by property $group
	 *
	 * @since 0.12.0
	 *
	 * @param string $group property to group entries by
	 *
	 * @return static grouped options
	 */
	public function groupBy(string $group): static {
		return new static(array_reduce($this->toArray(), function (array $accumulator, array $element) use ($group) {
			$accumulator[$element[$group]][] = $element;
			return $accumulator;
		}, []));
	}
	#endregion EXPORTING
}
