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
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\stdlib
 * @category stdlib
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Converters;

use BackedEnum;
use Inane\Stdlib\Exception\InvalidArgumentException;
use Inane\Stdlib\Exception\ValueError;
use ReflectionClass;
use UnitEnum;
use function filter_var;
use function get_debug_type;
use function is_array;
use function is_numeric;
use function is_object;
use function is_subclass_of;
use function method_exists;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOL;

/**
 * Provides utility methods for type-safe assignment and casting of variables
 * with support for basic scalar types, arrays, enums, and objects.
 */
trait CastAndAssignTrait {
    /**
     * Casts and assigns a value to a variable according to its type. Supports various data types including
     * primitive types (int, float, string, bool, array) and custom types (enums, objects). Optionally preserves
     * the null state of the variable.
     *
     * @param mixed &$var          The variable to which the value will be assigned. Its type determines how the value is cast.
     * @param mixed  $value        The value to be assigned to the variable. It will be cast to the type of the variable if possible.
     * @param bool   $preserveNull If true, the variable's null state is preserved. If false and the variable is null, it will be assigned the given value.
     *
     * @return void
     *
     * @throws InvalidArgumentException|\ReflectionException If the value cannot be cast to the required type.
     */
    public static function castAndAssign(
        mixed &$var, mixed $value, bool $preserveNull = true,
    ): void {
        $type = get_debug_type($var);   // Get the type or object name of a variable

        // Preserve null semantics
        if ($var === null) {   // Casts and assigns a value to a variable according to its type. Supports various data types including
            if ($preserveNull) {   // Casts and assigns a value to a variable according to its type. Supports various data types including
                return;
            }

            $var = $value;   // Casts and assigns a value to a variable according to its type. Supports various data types including

            return;
        }

        switch ($type) {
            case 'int':
                if (!is_numeric($value)) {                                      // Finds whether a variable is a number or a numeric string
                    throw new InvalidArgumentException('Cannot cast to int');   // Custom construct template
                }
                $var = (int)$value;   // Casts and assigns a value to a variable according to its type. Supports various data types including

                return;

            case 'float':
                if (!is_numeric($value)) {                                        // Finds whether a variable is a number or a numeric string
                    throw new InvalidArgumentException('Cannot cast to float');   // Custom construct template
                }
                $var = (float)$value;   // Casts and assigns a value to a variable according to its type. Supports various data types including

                return;

            case 'string':
                if (is_array($value) || is_object($value)) {                       // Finds whether a variable is an array | Finds whether a variable is an object
                    throw new InvalidArgumentException('Cannot cast to string');   // Custom construct template
                }
                $var = (string)$value;   // Casts and assigns a value to a variable according to its type. Supports various data types including

                return;

            case 'bool':
                $var = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);   // Casts and assigns a value to a variable according to its type. Supports various data types including | Filters a variable with a specified filter

                if ($var === null) {                                               // Casts and assigns a value to a variable according to its type. Supports various data types including
                    throw new InvalidArgumentException('Invalid boolean value');   // Custom construct template
                }

                return;

            case 'array':
                if (!is_array($value)) {                                    // Finds whether a variable is an array
                    throw new InvalidArgumentException('Expected array');   // Custom construct template
                }
                $var = $value;   // Casts and assigns a value to a variable according to its type. Supports various data types including

                return;

            default:
                // ENUMS
                if ($var instanceof UnitEnum) {       // Casts and assigns a value to a variable according to its type. Supports various data types including | @since 8.1
                    static::castEnum($var, $value);   // Updates the provided enum reference with a corresponding enum value or throws an exception

                    return;
                }

                // OBJECTS
                if (is_object($var)) {                  // Finds whether a variable is an object
                    static::castObject($var, $value);   // Casts or hydrates the provided object based on the given value.

                    return;
                }

                throw new InvalidArgumentException("Unsupported type: $type");   // Custom construct template
        }
    }

    /**
     * Updates the provided enum reference with a corresponding enum value or throws an exception
     * if the provided value is invalid for the specified enum.
     *
     * @param UnitEnum $enum  The enum instance to be updated. This is passed by reference.
     * @param mixed    $value The value to map to an enum case. Can be an instance of the same enum class,
     *                        a backing value for backed enums, or a string matching an enum case name.
     *
     * @return void
     *
     * @throws InvalidArgumentException If the provided value does not match any valid enum case.
     */
    public static function castEnum(UnitEnum &$enum, mixed $value): void {
        /**
         * @var BackedEnum $class
         */
        $class = $enum::class;

        if ($value instanceof $class) {   // Updates the provided enum reference with a corresponding enum value or throws an exception | @var BackedEnum $class
            $enum = $value;               // Updates the provided enum reference with a corresponding enum value or throws an exception

            return;
        }

        if (is_subclass_of($class, BackedEnum::class)) {   // checks if the object has this class as one of its parents or implements it
            try {
                $enum = $class::from($value);   // Updates the provided enum reference with a corresponding enum value or throws an exception | Translates a string or int into the corresponding <code>Enum</code>

                return;
            } catch (ValueError) {
                throw new InvalidArgumentException('Invalid enum backing value');   // Custom construct template
            }
        }

        foreach($class::cases() as $case) {   // @return static[]
            if ($case->name === $value) {   // Updates the provided enum reference with a corresponding enum value or throws an exception
                $enum = $case;              // Updates the provided enum reference with a corresponding enum value or throws an exception

                return;
            }
        }

        throw new InvalidArgumentException('Invalid enum value');   // Custom construct template
    }

    /**
     * Casts or hydrates the provided object based on the given value.
     * If the value matches the type of the object, it directly assigns it.
     * Otherwise, it attempts to hydrate the object using various strategies, including
     * fromArray, hydrate methods, or constructor arguments.
     *
     * @param object $object The object to be cast or hydrated. The reference is updated if the operation is successful.
     * @param mixed  $value  The value used for casting or hydration. It can be an instance of the object's class or an array.
     *
     * @return void
     *
     * @throws InvalidArgumentException|\ReflectionException If the object cannot be hydrated using the provided value.
     */
    public static function castObject(object &$object, mixed $value): void {
        $class = $object::class;

        if ($value instanceof $class) {   // Casts or hydrates the provided object based on the given value.
            $object = $value;             // Casts or hydrates the provided object based on the given value.

            return;
        }

        // 1. fromArray()
        if (is_array($value) && method_exists($class, 'fromArray')) {   // Finds whether a variable is an array | Checks if the class method exists
            $object = $class::fromArray($value);                        // Casts or hydrates the provided object based on the given value.

            return;
        }

        // 2. hydrate()
        if (is_array($value) && method_exists($object, 'hydrate')) {   // Finds whether a variable is an array | Checks if the class method exists
            $object->hydrate($value);                                  // Casts or hydrates the provided object based on the given value.

            return;
        }

        // 3. Constructor hydration
        if (is_array($value)) {                        // Finds whether a variable is an array
            $ref = new ReflectionClass($class);        // Constructs a ReflectionClass
            $object = $ref->newInstanceArgs($value);   // Casts or hydrates the provided object based on the given value. | Creates a new class instance from given arguments.

            return;
        }

        throw new InvalidArgumentException("Cannot hydrate object of type $class");   // Custom construct template
    }
}
