<?php

/**
 * Inane: Stdlib
 *
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
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

declare(strict_types = 1);

namespace Inane\Stdlib;

use Exception;
use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Exception\{
    InvalidArgumentException,
    RuntimeException};
use Inane\Stdlib\String\Capitalisation;
use Inane\Stdlib\String\StringCaseConverter;
use function array_key_exists;
use function array_keys;
use function array_pop;
use function array_reduce;
use function array_unique;
use function array_values;
use function asort;
use function count;
use function current;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use function key;
use function next;
use function prev;
use function reset;
use function sort;
use const null;

/**
 * Options: recursive key, value store
 *
 * Provides a property-based interface to an array.
 * The data can be made read-only by setting $allowModifications to false with the `lock` method.
 *
 * @todo    : version bump
 *
 * @version 0.17.0
 * @property \Inane\Stdlib\Array\OptionsInterface|Options|mixed|null $category
 */
class Options implements OptionsInterface {
    #region TRAITS
    use Converters\ArrayToXML;
    use Converters\TraversableToArray;

    #endregion TRAITS

    #region PROPERTIES
//#region Properties
    /**
     * Stores the option values as key-value pairs.
     *
     * @var array<string, mixed> $data
     */
    private array $data = [];
    /**
     * Used when unsetting values during iteration to ensure we do not skip
     * the next element.
     *
     * @var bool
     */
    protected bool $skipNextIteration;
//#endregion Properties
    #endregion PROPERTIES

    #region CREATE
    /**
     * Options
     *
     * Create a new options object. Any invalid initial values are ignored, resulting in a clean Options object being created.
     *
     * @since 0.10.2
     *  - takes \ArrayObject
     * @since 0.13.0
     *  - takes string - JSON encoded string
     * @since 0.15.0
     *  - now also excepts an instance of itself and a null
     *
     * @param bool                                                                $allowModifications
     *
     * @param null|array|string|\ArrayObject|ArrayObject|Options|OptionsInterface $data initial data in a variety of formates
     *
     * @return void
     */
    public function __construct(
        /**
         * Initial value store
         */ null|array|string|\ArrayObject|ArrayObject|Options|OptionsInterface $data = [], /**
     * Whether modifications to the data are allowed
     */ private bool                                                            $allowModifications = true,
    ) {
        if (is_string($data)) $data = Json::decode($data);
        if ($data instanceof \ArrayObject || $data instanceof ArrayObject) $data = $data->getArrayCopy();

        // if ((!is_array($data) && !($data instanceof static)) || $data === null) $data = [];
        if ((!is_array($data) && !($data instanceof OptionsInterface)) || $data === null) $data = [];

        foreach($data as $key => $value) if (is_array($value) || $value instanceof \ArrayObject || $value instanceof ArrayObject) $this->data[$key] = new static($value, $this->allowModifications); else $this->data[$key] = $value;
    }

//#region Magic Methods
    /**
     * get value
     *
     * public function &__get($key) {
     *
     * @param mixed $key key
     *
     * @return mixed|Options|OptionsInterface value
     */
    public function __get(mixed $key) {
        return $this->get($key);
    }

    /**
     * Assigns a value to the specified key
     *
     * @param mixed $key   The key to which the value will be assigned
     * @param mixed $value The value to assign
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function __set(mixed $key, mixed $value) {
        if ($this->allowModifications) {
            if (!$this->offsetExists($key) && is_string($key)) {
                $case = StringCaseConverter::caseFromString($key);

                $kebab = false;
                if ($case === Capitalisation::camelCase) $kebab = StringCaseConverter::camelToKebab($key); elseif ($case === Capitalisation::PascalCase) $kebab = StringCaseConverter::pascalToKebab($key);

                if (is_string($kebab) && $this->offsetExists($kebab)) $key = $kebab;
            }

            if (is_array($value)) $value = new static($value);

            if ($key === null) $this->data[] = $value; else $this->data[$key] = $value;
        } else throw new RuntimeException("Option is read only, key: $key");
    }
    #endregion CREATE

    #region IS_SET

    /**
     * Whether data exists by key
     *
     * @param mixed $key A data key to check for
     *
     * @return boolean
     */
    public function __isset(mixed $key): bool {
        // if (!array_key_exists($key, $this->data) && is_string($key)) {
        // 	$case = StringCaseConverter::caseFromString($key);

        // 	$kebab = false;
        // 	if ($case == Capitalisation::camelCase) {
        // 		$kebab = StringCaseConverter::camelToKebab($key);
        // 	} elseif ($case == Capitalisation::PascalCase) {
        // 		$kebab = StringCaseConverter::pascalToKebab($key);
        // 	}
        // 	if (is_string($kebab) && array_key_exists($kebab, $this->data)) $key = $kebab;
        // }

        return array_key_exists($key ?? '', $this->data);
    }
//#endregion Magic Methods

    /**
     * Recreate Options from `var_export` code
     *
     * @since 0.13.0
     *
     * @param array $properties result from `var_export`
     *
     * @return static Options|OptionsInterface
     */
    public static function __set_state(array $properties): static {
        $obj = new static();
        $obj->data = $properties['data'];
        $obj->allowModifications = $properties['allowModifications'];

        return $obj;
    }

    /**
     * Deep clone of an instance ensuring that nested <strong>Inane\Stdlib\Options</strong> are cloned.
     *
     * @return void
     */
    public function __clone() {
        $array = [];

        foreach($this->data as $key => $value) if ($value instanceof OptionsInterface) $array[$key] = clone $value; else $array[$key] = $value;

        $this->data = $array;
    }

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
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does, however, mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
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
     *
     * @return bool exists
     */
    public function offsetExists(mixed $offset): bool {
        return $this->__isset($offset);
    }
    #endregion IS_SET

    #region GETTER

    /**
     * valid
     *
     * Checks if the current element is valid
     *
     * @return bool valid
     */
    public function valid(): bool {
        return $this->key() !== null;
    }

    /**
     * get key
     *
     * @param string $id      key
     * @param mixed  $default value
     *
     * @return mixed|Options|OptionsInterface value
     */
    public function get(mixed $id, mixed $default = null): mixed {
        if ($this->offsetExists($id)) return $this->data[$id];

        if (is_string($id)) {
            $case = StringCaseConverter::caseFromString($id);

            $kebab = false;
            if ($case === Capitalisation::camelCase) {
                $kebab = StringCaseConverter::camelToKebab($id);
            } elseif ($case === Capitalisation::PascalCase) {
                $kebab = StringCaseConverter::pascalToKebab($id);
            }
            if (is_string($kebab) && $this->offsetExists($kebab)) return $this->data[$kebab];
        }

        return $default;
    }

    /**
     * get offset
     *
     * @param mixed $offset offset
     *
     * @return mixed|Options|OptionsInterface value
     */
    public function offsetGet(mixed $offset): mixed {
        return $this->get($offset);
    }

    /**
     * key
     *
     * Fetch the key for the current element
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
     * @return mixed|OptionsInterface
     */
    public function current(): mixed {
        $this->skipNextIteration = false;

        return current($this->data);
    }
    #endregion GETTER

    #region SETTER

    /**
     * set offset
     *
     * @param mixed $offset offset
     * @param mixed $value  value
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        $this->__set($offset, $value);
    }

    /**
     * Gets the previous value of the key being assigned a new value
     *
     * @since 0.16.0
     *
     * @param mixed $value The value to assign
     *
     * @param mixed $key   The key to which the value will be assigned and whose previous value is returned
     *
     * @return mixed the key's previous value
     *
     * @throws RuntimeException
     */
    public function getSet(mixed $key, mixed $value): mixed {
        $previous = $this->get($key);
        $this->set($key, $value);

        return $previous;
    }

    /**
     * Gets the previous value of the key being assigned a new value
     *
     * @since 0.16.0
     *
     * @param mixed $value The value to assign
     *
     * @param mixed $key   The key to which the value will be assigned and whose previous value is returned
     *
     * @return mixed the key's previous value
     *
     * @throws RuntimeException
     */
    public function offsetGetSet(mixed $key, mixed $value): mixed {
        return $this->getSet($key, $value);
    }
    #endregion SETTER

    #region GETTER_SETTER

    /**
     * delete key
     *
     * @param string $offset key
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function offsetUnset(mixed $offset): void {
        $this->__unset($offset);
    }

    /**
     * Prevent any more modifications being made to this instance.
     *
     * Useful after merge() has been used to merge multiple OptionsInterface objects
     * into one object which should then not be modified again.
     *
     * @return self
     */
    public function lock(): self {
        $this->allowModifications = false;

        /** @var OptionsInterface|Options|Config $value */
        foreach($this->data as $value) if ($value instanceof OptionsInterface) $value->lock();

        return $this;
    }
    #endregion GETTER_SETTER

    #region UNSETTER

    /**
     * Returns whether this Options object is locked or not.
     *
     * @return bool
     */
    public function isLocked(): bool {
        return !$this->allowModifications;
    }

    /**
     * next
     *
     * Advance the internal pointer
     *
     * @return void
     */
    public function next(): void {
        if ($this->skipNextIteration) {
            $this->skipNextIteration = false;

            return;
        }

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
        $this->skipNextIteration = false;
        reset($this->data);
    }

    /**
     * Sorts the options.
     *
     * @since version
     *
     * @param bool $preserveIndex Whether to preserve the array keys during sorting. Defaults to true.
     * @param bool $createCopy    If true, returns a sorted copy of the options; if false, sorts in place.
     *
     * @return static Returns the sorted options instance.
     */
    public function sort(bool $preserveIndex = true, bool $createCopy = false): static {
        $sorted = $this->toArray();

        if ($preserveIndex) asort($sorted); else sort($sorted);

        $sorted = new static($sorted);
        if ($createCopy) return $sorted;

        $this->data = [];
        $this->merge($sorted);

        return $this;
    }
    #endregion UNSETTER

    #region MERGING

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
     * @since version $createCopy param added
     *
     * @param bool $createCopy If true, returns a new instance with unique values; if false, modifies the current instance.
     *
     * @return Options|OptionsInterface unique items
     */
    public function unique(bool $createCopy = false): static {
        $unique = new static(array_unique($this->toArray()));
        if ($createCopy) return $unique;

        $this->data = [];
        $this->merge($unique);

        return $this;
    }

    /**
     * Return keys
     *
     * @since 0.10.3
     *
     * @return array keys
     */
    public function keys(): array {
        return array_keys($this->toArray());
    }

    /**
     * Return values
     *
     * If this object is locked,
     *  the values are locked too.
     *
     * @todo  : should values inherit lock status?
     *
     * @since 0.10.3
     *
     * @return iterable|static values
     */
    public function values(): iterable|static {
        $values = array_values($this->toArray());

        return new static($values, $this->allowModifications);
    }
    #endregion MERGING

    #region LOCKING

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray(): array {
        return static::iteratorToArrayDeep($this);
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
     * @see   http://www.php.net/manual/en/json.constants.php JSON Constants
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
    #endregion LOCKING

    #region NAVIGATION

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
        return new static(array_reduce($this->toArray(), function(array $accumulator, array $element) use ($group) {
            $accumulator[$element[$group]][] = $element;

            return $accumulator;
        }, []));
    }

    /**
     * String representation of an object.
     *
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string|null The string representation of the object or null
     * @throws Exception Returning another type than string or null
     */
    public function serialize(): ?string {
        return serialize($this->data);
    }

    /**
     * Constructs the object.
     *
     * @link https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $data The string representation of the object.
     *
     * @return void
     */
    public function unserialize(string $data): void {
        $this->data = unserialize($data, ['allowed_classes' => [OptionsInterface::class]]);
    }
    #endregion NAVIGATION

    #region OTHER

    /**
     * Checks if a $value exists in an Option's values
     *
     * @since 0.10.3
     *
     * @see   in_array
     *
     * @param mixed $value  The searched value
     * @param bool  $strict If set to true, then the type of the value is also checked
     *
     * @return bool Returns true if value is found, false otherwise
     */
    public function contains(mixed $value, bool $strict = false): bool {
        return in_array($value, $this->data, $strict);
    }

    /**
     * set key
     *
     * @param mixed $key   key
     * @param mixed $value value
     *
     * @return OptionsInterface
     *
     * @throws RuntimeException
     */
    public function set(mixed $key, mixed $value): OptionsInterface {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Unset data by key
     *
     * @param mixed $key The key to unset
     *
     * @throws InvalidArgumentException
     */
    public function __unset($key) {
        if (!$this->allowModifications) throw new InvalidArgumentException('Option is read only'); elseif ($this->__isset($key)) {
            unset($this->data[$key]);
            $this->skipNextIteration = true;
        }
    }
    #endregion OTHER

    #region EXPORTING

    /**
     * delete key
     *
     * @param mixed $key key
     *
     * @return OptionsInterface
     *
     * @throws InvalidArgumentException
     */
    public function unset(mixed $key): OptionsInterface {
        $this->offsetUnset($key);

        return $this;
    }

    /**
     * Get value and delete key
     *
     * @since 0.15.0
     *
     * @param mixed  $default value
     *
     * @param string $id      key
     *
     * @return mixed|OptionsInterface value
     * @throws InvalidArgumentException
     */
    public function pull(mixed $id, mixed $default = null): mixed {
        $result = $this->get($id, $default);
        $this->unset($id);

        return $result;
    }

    /**
     * Merge another Options object with this one.
     *
     * @since 0.10.2
     *  - takes an array and \ArrayObject
     *
     * For duplicate keys, the following will be performed:
     * - Nested Options will be recursively merged.
     * - Items in $merge with INTEGER keys will be appended.
     * - Items in $merge with STRING keys will overwrite current values.
     *
     * @param array|\ArrayObject|ArrayObject|OptionsInterface|Options $merge
     *
     * @return OptionsInterface|Options
     */
    public function merge(array|\ArrayObject|ArrayObject|OptionsInterface|Options $merge): OptionsInterface {
        if (!$merge instanceof OptionsInterface) $merge = new static($merge);

        /** @var OptionsInterface $value */
        foreach($merge as $key => $value) if ($this->offsetExists($key)) {
            if (is_int($key)) $this->data[] = $value; elseif ($value instanceof OptionsInterface && $this->data[$key] instanceof OptionsInterface) $this->data[$key]->merge($value);
            elseif ($value instanceof OptionsInterface) $this->data[$key] = new static($value->toArray(), $this->allowModifications); else $this->data[$key] = $value;
        } elseif ($value instanceof OptionsInterface) $this->data[$key] = new static($value->toArray(), $this->allowModifications); else $this->data[$key] = $value;

        return $this;
    }

    /**
     * updates properties 2+ into the first array with decreasing importance
     * so only unset keys are assigned values
     *
     * 1 array in = the same array out
     * 0 array in = empty array out
     *
     * Apply defaults to $args:
     * $args->defaults($defaults);
     *
     * @todo  : check for allowModifications
     *
     * @since version bump accepts array values
     *
     * @param array|Options|OptionsInterface ...$models
     *
     * @return OptionsInterface
     */
    public function defaults(array|Options|OptionsInterface ...$models): self {
        $replaceable = ['', null];

        while($model = array_pop($models)) foreach($model as $key => $value) {
            if (is_array($model)) $model = new static($model);
            if ($value instanceof OptionsInterface && $this->offsetExists($key) && $this[$key] instanceof OptionsInterface) $this[$key]->defaults($value); elseif ((!$this->offsetExists($key) || in_array($this[$key], $replaceable)) && $this[$key] !== false) $this[$key] = $value;
        }

        return $this;
    }

    /**
     * Merge an array but only updates existing keys, ignoring unmatched keys
     *
     * @since 0.11.0
     *
     * @param array|\ArrayObject|ArrayObject|Options|OptionsInterface $merge
     *
     * @return OptionsInterface
     */
    public function modify(array|\ArrayObject|ArrayObject|Options|OptionsInterface $merge): self {
        if (!$merge instanceof OptionsInterface) $merge = new static($merge);

        /** @var Options $value */
        foreach($merge as $key => $value) if ($this->offsetExists($key)) {
            if (is_int($key)) $this->data[] = $value; elseif ($value instanceof OptionsInterface && $this->data[$key] instanceof OptionsInterface) $this->data[$key]->modify($value);
            elseif ($value instanceof OptionsInterface) $this->data[$key] = new static($value->toArray(), $this->allowModifications); else $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Merge an array but only adds missing keys, leaving existing keys unmodified
     *
     * @since 2025-07-31 array $exclude A list of keys to ignore.
     *
     * @since 0.11.0
     *
     * @param array|\ArrayObject|ArrayObject|Options|OptionsInterface $merge
     * @param array                                                   $exclude A list of keys to ignore.
     *
     * @return OptionsInterface
     */
    public function complete(array|\ArrayObject|ArrayObject|Options|OptionsInterface $merge, array $exclude = []): self {
        if (!$merge instanceof OptionsInterface) $merge = new static($merge);

        /** @var OptionsInterface $value */
        foreach($merge as $key => $value) if (!in_array($key, $exclude) && $this->offsetExists($key)) {
            if ($value instanceof OptionsInterface && $this->data[$key] instanceof OptionsInterface) $this->data[$key]->complete($value, $exclude);
        } elseif ($value instanceof OptionsInterface) $this->data[$key] = new static($value->toArray(), $this->allowModifications); else $this->data[$key] = $value;

        return $this;
    }

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
     * Make Options play nicely with var_dump
     *
     * @return array
     */
    public function __debugInfo(): array {
        return $this->toArray();
    }
    #endregion EXPORTING

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function __toArray(): array {
        // return static::iteratorToArrayDeep($this);
        return $this->toArray();
    }

    /**
     * Return data as an XML string
     *
     * @since 0.10.3
     *
     * @return string XML string
     */
    public function toXML(): string {
        return static::arrayToXML($this->toArray())
            ->asXML()
        ;
    }

    /**
     * Returns array containing all the necessary state of the object.
     *
     * @since 7.4
     * @link  https://wiki.php.net/rfc/custom_object_serialization
     */
    public function __serialize(): array {
        return $this->toArray();
    }

    /**
     * Restores the object state from the given data array.
     *
     * @since 7.4
     * @link  https://wiki.php.net/rfc/custom_object_serialization
     *
     * @param array $data
     */
    public function __unserialize(array $data): void {
        $this->data = $data;
    }
}
