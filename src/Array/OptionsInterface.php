<?php

/**
 * Inane: Stdlib
 *
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
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
use Countable;
use Inane\Stdlib\Converters\{
    Arrayable,
    JSONable,
    XMLable};
use Iterator;
use JsonSerializable;
use Psr\Container\ContainerInterface;
use Serializable;

/**
 * Interface: Options
 *
 * @version 0.1.0
 */
interface OptionsInterface extends ArrayAccess, Iterator, Countable, ContainerInterface, JsonSerializable, Arrayable, JSONable, XMLable, Serializable {
    /**
     * Checks if the specified offset exists.
     *
     * @param mixed $offset The offset to check for existence.
     *
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists(mixed $offset): bool;
    /**
     * Retrieves the value at the specified offset.
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed The value at the specified offset, or null if not set.
     */
    public function offsetGet(mixed $offset): mixed;
    /**
     * Sets the value at the specified offset.
     *
     * @param mixed $offset The offset to set.
     * @param mixed $value The value to assign at the specified offset.
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void;
    /**
     * Unset the value at the specified offset.
     *
     * @param mixed $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void;
    /**
     * Returns the current element in the array or collection.
     *
     * @return mixed The current element.
     */
    public function current(): mixed;
    /**
     * Returns the current key in the options array.
     *
     * @return mixed The key of the current element.
     */
    public function key(): mixed;
    /**
     * Advances the internal pointer to the next element.
     *
     * @return void
     */
    public function next(): void;
    /**
     * Rewinds the iterator to the first element.
     *
     * This method is typically called at the beginning of an iteration to reset the internal pointer.
     *
     * @return void
     */
    public function rewind(): void;
    /**
     * Determines if the current option is valid.
     *
     * @return bool Returns true if the option is valid, false otherwise.
     */
    public function valid(): bool;
    /**
     * Prevent any more modifications being made to this instance.
     *
     * Useful after merge() has been used to merge multiple OptionsInterface objects
     * into one object which should then not be modified again.
     *
     * @return self
     */
    public function lock(): self;
    /**
     * Returns whether this Options object is locked or not.
     *
     * @return bool
     */
    public function isLocked(): bool;
    /**
     * Sorts the options.
     *
     * @since version
     *
     * @param bool $preserveIndex Whether to preserve the array keys during sorting. Defaults to true.
     * @param bool $createCopy If true, returns a sorted copy of the options; if false, sorts in place.
     *
     * @return static Returns the sorted options instance.
     */
    public function sort(bool $preserveIndex = true, bool $createCopy = false): static;
    /**
     * count
     *
     * Counts all elements
     *
     * @return int item count
     */
    public function count(): int;
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
    public function unique(bool $createCopy = false): static;
    /**
     * Returns keys
     *
     * @return array keys
     */
    public function keys(): array;
    /**
     * Returns values
     *
     * If this object is locked,
     *  the values are locked too.
     *
     * @return iterable|static values
     */
    public function values(): iterable|static;
    /**
     * Return new Options group by property $group
     *
     * @param string $group property to group entries by
     *
     * @return static grouped options
     */
    public function groupBy(string $group): static;
    /**
     * Gets the previous value of the key being assigned a new value
     *
     * @param mixed $key The key to which the value will be assigned and who's previous value is returned
     * @param mixed $value The value to assign
     *
     * @return mixed the key's previous value
     */
    public function getSet(mixed $key, mixed $value): mixed;
    /**
     * Gets the previous value of the key being assigned a new value
     *
     * @param mixed $key The key to which the value will be assigned and who's previous value is returned
     * @param mixed $value The value to assign
     *
     * @return mixed the key's previous value
     */
    public function offsetGetSet(mixed $key, mixed $value): mixed;

    /**
     * Merge an array but only adds missing keys, leaving existing keys unmodified
     *
     * @since 2025-07-31 array $exclude A list of keys to ignore.
     *
     * @since 0.11.0
     *
     * @param array|\ArrayObject|ArrayObject|OptionsInterface $merge
     * @param array                                                   $exclude A list of keys to ignore.
     *
     * @return OptionsInterface
     */
    public function complete(array|\ArrayObject|ArrayObject|OptionsInterface $merge, array $exclude = []): self;
}
