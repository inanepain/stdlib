<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @package Inane\Stdlib
 * @author Philip Michael Raab<peep@inane.co.za>
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 */

declare(strict_types=1);

namespace Inane\Stdlib;

use ArrayAccess;
use Countable;
use Iterator;
use Psr\Container\ContainerInterface;

use function array_pop;
use function count;
use function current;
use function in_array;
use function is_array;
use function is_int;
use function is_null;
use function key;
use function next;
use function reset;

use Inane\Stdlib\Exception\{
    InvalidArgumentException,
    RuntimeException
};

/**
 * Options
 *
 * Provides a property based interface to an array.
 * The data are read-only unless $allowModifications is set to true
 * on construction.
 *
 * Implements Countable, Iterator and ArrayAccess
 * to facilitate easy access to the data.
 *
 * @package Inane\Stdlib
 * @version 0.10.1
 */
class Options implements ArrayAccess, Iterator, Countable, ContainerInterface {

    /**
     * Variables
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
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Assigns a value to the specified data
     *
     * @param string The data key to assign the value to
     * @param mixed  The value to set
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function __set($key, $value) {
        if ($this->allowModifications) {
            if (is_array($value)) $value = new static($value);

            if (is_null($key)) $this->data[] = $value;
            else $this->data[$key] = $value;
        } else throw new RuntimeException('Option is read only');
    }

    /**
     * Whether or not an data exists by key
     *
     * @param string An data key to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function __isset($key) {
        return isset($this->data[$key]);
    }

    /**
     * Unset data by key
     *
     * @param string The key to unset
     * @access public
     */
    public function __unset($key) {
        if (!$this->allowModifications) throw new InvalidArgumentException('Option is read only');
        elseif ($this->__isset($key)) unset($this->data[$key]);
    }

    /**
     * Options
     *
     * @param array $data values
     * @param bool $allowModifications
     *
     * @return void
     */
    public function __construct(
        /**
         * Variables
         */
        array $data = [],
        /**
         * Whether modifications to configuration data are allowed
         */
        private bool $allowModifications = true
    ) {
        foreach ($data as $key => $value) if (is_array($value)) $this->data[$key] = new static($value, $this->allowModifications);
        else $this->data[$key] = $value;
    }

    /**
     * Deep clone of instance ensuring that nested Inane\Config\Options are cloned.
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
     * Current
     *
     * @return mixed|Options
     */
    public function current(): mixed {
        return current($this->data);
    }

    /**
     * next
     *
     * @return void
     */
    public function next(): void {
        next($this->data);
    }

    /**
     * key
     *
     * @return string|float|int|bool|null key
     */
    public function key(): string|int|null {
        return key($this->data);
    }

    /**
     * valid
     *
     * @return bool valid
     */
    public function valid(): bool {
        return (!is_null($this->key()));
    }

    /**
     * rewind to first item
     *
     * @return void
     */
    public function rewind(): void {
        reset($this->data);
    }

    /**
     * count
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
    public function offsetExists($offset): bool {
        return $this->__isset($offset);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool {
        return $this->__isset($id);
    }

    /**
     * get offset
     * @param string $offset offset
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
    public function get($key, $default = null) {
        return $this->offsetExists($key) ? $this->data[$key] : $default;
    }

    /**
     * set offset
     *
     * @param string $offset offset
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
    public function set($key, $value): Options {
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
     * @param string $offset key
     * @return Options
     */
    public function unset($key): Options {
        $this->offsetUnset($key);
        return $this;
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
     * Merge another Config with this one.
     *
     * For duplicate keys, the following will be performed:
     * - Nested Configs will be recursively merged.
     * - Items in $merge with INTEGER keys will be appended.
     * - Items in $merge with STRING keys will overwrite current values.
     *
     * @param Options $merge
     * @return self
     */
    public function merge(Options $merge): self {
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
     * Useful after merge() has been used to merge multiple Config objects
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
     * Returns whether this Config object is locked or not.
     *
     * @return bool
     */
    public function isLocked(): bool {
        return !$this->allowModifications;
    }
}
