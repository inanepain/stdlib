<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author  Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Stdlib
 * @category array
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib;

use ArrayIterator;
use ArrayObject as SystemArrayObject;

use function is_array;

/**
 * ArrayObject
 *
 * differences from parent:
 *  - ARRAY_AS_PROPS set
 *  - Arrays converted to ArrayObject
 *
 * @version 0.2.1
 * @package Inane\Stdlib
 */
class ArrayObject extends SystemArrayObject {
    /**
     * ArrayObject constructor
     *
     * @param array $array
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
    public function set($key, $value): void {
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
}
