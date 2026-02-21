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

namespace Inane\Stdlib;

use ArrayIterator;
use ArrayObject as SystemArrayObject;
use Inane\Stdlib\Converters\{
    Arrayable,
    JSONable};
use Inane\Stdlib\Exception\JsonException;
use Random\RandomException;
use function count;
use function current;
use function is_array;
use function key;
use function next;
use function random_int;

/**
 * ArrayObject
 *
 * differences from parent:
 *  - ARRAY_AS_PROPS set
 *  - Arrays converted to ArrayObject
 *
 * @version 0.2.2
 */
class ArrayObject extends SystemArrayObject implements Arrayable, JSONable {
    /**
     * ArrayObject constructor
     *
     * @param array|object $array $array
     */
    public function __construct(array|object $array = []) {
        $this->setFlags(static::ARRAY_AS_PROPS);

        foreach ($array as $key => $value) if (is_array($value)) $array[$key] = new static($value);
        parent::__construct(
            $array,
            static::ARRAY_AS_PROPS,
            ArrayIterator::class
        );
    }

    /**
     * Get as an array
     *
     * @return array
     */
    public function getArrayCopy(): array {
        $array = parent::getArrayCopy();
        foreach ($array as $key => $value) if ($value instanceof static) $array[$key] = $value->getArrayCopy();
        return $array;
    }

    /**
     * Replace current values with $array
     *
     * Old values are returned
     *
     * @param object|array $array new values
     *
     * @return array old values
     */
    public function exchangeArray(object|array $array): array {
        $old = $this->getArrayCopy();
        foreach ($array as $key => $value) if (is_array($value)) $array[$key] = new static($value);
        parent::exchangeArray($array);

        return $old;
    }

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return void
     */
    public function set(mixed $key, mixed $value): void {
        $this->offsetSet($key, $value);
    }

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value): void {
        if (is_array($value)) $value = new static($value);

        parent::offsetSet($key, $value);
    }

    /**
     * Random Item
     *
     * @return array
     *
     * @throws RandomException
     */
    public function randomItem(): array {
        $data = $this->getArrayCopy();
        $r = random_int(0, count($data) - 1);
        for ($i = 0; $i < $r; $i++)
            next($data);

        return [key($data) => current($data)];
    }

    /**
     * Get copy as array
     *
     * @since 0.2.2
     *
     * @return array
     */
    public function toArray(): array {
        return $this->getArrayCopy();
    }

    /**
     * Get as JSON string
     *
     * @since 0.2.2
     *
     * @return string
     *
     * @throws JsonException
     */
    public function toJSON(): string {
        return Json::encode($this->toArray());
    }
}
