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
use function array_unshift;
use function call_user_func;
use function count;
use function function_exists;
use function in_array;
use const null;

/**
 * Array Function Toolkit
 *
 * Methods listed bellow have been tested, but only their simplest use case.
 *
 * @method array filter(callable $func) Filters elements this array using a callback function
 * @method array map(callable $func)    Applies the callback to the elements of this array
 * @method array merge(array $array)    Merges an array into this array
 * @method mixed pop()                  Pop the element off the end of array
 * @method mixed shift()                Shift an element off the beginning of array
 * @method int   unshift(mixed $values) Prepend one or more elements to the beginning of an array
 * @method int   push(mixed $values)    Push one or more elements onto the end of array
 * @method array walk(callable $func)   Apply a user supplied function to every member of this array
 *
 * @package Inane\Stdlib\Array
 *
 * @version 0.1.0
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
        'array_walk',
    ];

    /**
     * List of array functions that only have the array as argument
     *
     * @var array
     */
    private static array $self = [
        'array_pop',
        'array_shift',
        'array_sum',
    ];

    /**
     * List of array functions that update self
     *
     * @var array
     */
    private static array $store = [
        'array_merge',
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
     * @return array|int|bool|null
     */
    public function __call(string $name, array $arguments): array|int|bool|null {
        $func = "array_$name";

        if (function_exists($func)) {
            if (count($arguments) == 0 && in_array($func, static::$self))
                $result = $func($this->data);
            else if (in_array($func, static::$before))
                $result = $func($this->data, ...$arguments);
            else {
                // array_push($arguments, $this->data);
                array_unshift($arguments, $this->data);
                $result = @call_user_func($func, ...$arguments);
            }

            if (in_array($func, static::$store)) $this->data = $result;
            return $result;
        }

        return null;
    }
}
