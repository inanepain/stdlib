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

namespace Inane\Stdlib;

use ArrayAccess;
use Countable;
use Iterator;
use Psr\Container\ContainerInterface;

use function array_keys;
use function array_pop;
use function array_values;
use function count;
use function current;
use function in_array;
use function is_array;
use function is_int;
use function is_null;
use function json_encode;
use function key;
use function next;
use function reset;

use Inane\Stdlib\Converters\{
    Arrayable,
    JSONable,
    XMLable
};
use Inane\Stdlib\Exception\{
    InvalidArgumentException,
    RuntimeException
};

/**
 * Options: key, value store
 *
 * Provides a property based interface to an array.
 * The data can be made read-only by setting $allowModifications to false with the `lock` method,
 *
 * @package Inane\Stdlib
 *
 * @version 0.10.5
 */
class Options implements ArrayAccess, Iterator, Countable, ContainerInterface, Arrayable, JSONable, XMLable {
    use Converters\ArrayToXML;

    /**
     * Value store
     */
    private array $data = [];

    /**
     * get value
     *
     * public function &__get($key) {
     *
     * @param mixed $key key
     * @return mixed|Options value
     */
    public function __get(mixed $key) {
        return $this->get($key);
    }

    /**
     * Assigns a value to the specified data
     *
     * @param mixed The data key to assign the value to
     * @param mixed  The value to set
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
     * Whether or not an data exists by key
     *
     * @param mixed $key An data key to check for
     *
     * @return boolean
     */
    public function __isset(mixed $key): bool {
        return isset($this->data[$key]);
    }

    /**
     * Unset data by key
     *
     * @param string The key to unset
     */
    public function __unset($key) {
        if (!$this->allowModifications) throw new InvalidArgumentException('Option is read only');
        elseif ($this->__isset($key)) unset($this->data[$key]);
    }

    /**
     * Options
     *
     * @since 0.10.2
     *  - takes \ArrayObject
     *
     * @param array|\ArrayObject $data values
     * @param bool $allowModifications
     *
     * @return void
     */
    public function __construct(
        /**
         * Initial value store
         */
        array|\ArrayObject $data = [],
        /**
         * Whether modifications to the data are allowed
         */
        private bool $allowModifications = true
    ) {
        if ($data instanceof \ArrayObject) $data = $data->getArrayCopy();

        foreach ($data as $key => $value)
            if (is_array($value) || $value instanceof \ArrayObject) $this->data[$key] = new static($value, $this->allowModifications);
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
     * Make Options play nicely with var_dump
     *
     * @return array
     */
    public function __debugInfo(): array {
        return $this->toArray();
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
     * key
     *
     * Fetch the key for current element
     *
     * @return string|float|int|bool|null key
     */
    public function key(): string|int|null {
        return key($this->data);
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
     * rewind
     *
     * Rewind the internal pointer to the first element
     *
     * @return void
     */
    public function rewind(): void {
        reset($this->data);
    }

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
     * Key exists
     *
     * @param string $offset key
     * @return bool exists
     */
    public function offsetExists(mixed $offset): bool {
        return $this->__isset($offset);
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
     * get offset
     * @param mixed $offset offset
     *
     * @return mixed|Options value
     */
    public function offsetGet(mixed $offset): mixed {
        return $this->get($offset);
    }

    /**
     * get key
     * @param string $key key
     * @param mixed $default value
     *
     * @return mixed|Options value
     */
    public function get(mixed $key, mixed $default = null) {
        return $this->offsetExists($key) ? $this->data[$key] : $default;
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
     * delete key
     *
     * @param string $offset key
     * @return void
     */
    public function offsetUnset(mixed $offset): void {
        $this->__unset($offset);
    }

    /**
     * delete key
     *
     * @param mixed $offset key
     * @return Options
     */
    public function unset(mixed $key): Options {
        $this->offsetUnset($key);
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
     * @param Options ...$models
     * @return Options
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
     * @param array|\ArrayObject|\Inane\Stdlib\Options $merge
     *
     * @return \Inane\Stdlib\Options
     */
    public function merge(array|\ArrayObject|Options $merge): self {
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

    /**
     * Returns keys
     *
     * @since 0.10.3
     *
     * @return iterable keys
     */
    public function keys(): iterable {
        return array_keys($this->toArray());
    }

    /**
     * Returns values
     *
     * @since 0.10.3
     *
     * @return iterable values
     */
    public function values(): iterable {
        $values = array_values($this->toArray());
        return new static($values, $this->allowModifications);
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
     * Supports flags from <strong>json_encode</strong>.
     *
     * Flags set by default:<br />
     * - JSON_NUMERIC_CHECK
     * - JSON_UNESCAPED_SLASHES
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
    public function toJSON(int $flags = 96, int $depth = 512): string {
        return json_encode($this->toArray(), $flags, $depth);
    }
}
