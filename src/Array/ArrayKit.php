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
 * @category array
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\Array;

use Inane\Stdlib\Converters\Arrayable;

use function array_push;
use function call_user_func;
use function count;
use function function_exists;
use function in_array;
use function random_int;
use const null;

/**
 * Array Function Toolkit
 *
 * Methods listed bellow have been tested, but only their simplest use case.
 *
 * @method array     column(int|string|null $key, int|string|null $index = null))   Return the values from a single column in the input array
 * @method ArrayKit  fill(int $start_index, int $count, mixed $value)               Fill an array with values
 * @method array     filter(callable $func)                                         Filters elements this array using a callback function
 * @method ArrayKit  flip()                                                         Exchanges all keys with their associated values in an array
 * @method bool      keyExists(string|int $key)                                     Checks if the given key or index exists in the array
 * @method array     map(callable $func)                                            Applies the callback to the elements of this array
 * @method ArrayKit  merge(array $array)                                            Merges an array into this array
 * @method mixed     pop()                                                          Pop the element off the end of array
 * @method int       push(mixed $values)                                            Push one or more elements onto the end of array
 * @method mixed     shift()                                                        Shift an element off the beginning of array
 * @method int|float sum()                                                          Calculate the sum of values in an array
 * @method int       unshift(mixed $values)                                         Prepend one or more elements to the beginning of an array
 * @method array     walk(callable $func)                                           Apply a user supplied function to every member of this array
 *
 * @package Inane\Stdlib\Array
 *
 * @version 0.2.0
 */
class ArrayKit implements Arrayable {
    /**
     * List of array functions that have the array before the callback
     *
     * @var array
     */
    private static array $before = [
        'array_column',
        'array_filter',
        'array_push',
        'array_unshift',
        'array_walk',
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
     * List of array functions that have been tested but don't fall into any of the other groups
     *
     * @var array
     */
    private static array $tested = [
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
        'array_sum',
        'array_unshift',
        'array_walk',
    ];

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(private array $data = []) {
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
    public function __call(string $name, array $arguments): static|array|int|bool|null {
        $func = 'array_' . \Inane\Stdlib\String\Inflector::underscore($name);

        if (function_exists($func)) {
            if (!in_array($func, static::$tested))
                trigger_error("Untested function: `$func`");

            if (count($arguments) == 0 && in_array($func, static::$self))
                $result = $func($this->data);
            else if (in_array($func, static::$before))
                $result = $func($this->data, ...$arguments);
            else if (in_array($func, static::$other))
                $result = $func(...$arguments);
            else {
                array_push($arguments, $this->data);
                // array_unshift($arguments, $this->data);
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
