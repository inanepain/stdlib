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

/**
 * Exceptions:
 *
 * BadFunctionCallException
 * 	Exception thrown if a callback refers to an undefined function or if some arguments are missing.
 *
 * BadMethodCallException
 * 	Exception thrown if a callback refers to an undefined method or if some arguments are missing.
 *
 * DomainException
 * 	Exception thrown if a value does not adhere to a defined valid data domain.
 *
 * InvalidArgumentException
 * 	Exception thrown if an argument is not of the expected type.
 *
 * LengthException
 * 	Exception thrown if a length is invalid.
 *
 * LogicException
 * 	Exception that represents error in the program logic. This kind of exception should lead directly to a fix in your code.
 *
 * OutOfBoundsException
 * 	Exception thrown if a value is not a valid key. This represents errors that cannot be detected at compile time.
 *
 * OutOfRangeException
 * 	Exception thrown when an illegal index was requested. This represents errors that should be detected at compile time.
 *
 * OverflowException
 * 	Exception thrown when adding an element to a full container.
 *
 * RangeException
 * 	Exception thrown to indicate range errors during program execution. Normally this means there was an arithmetic error other than under/overflow. This is the runtime version of DomainException.
 *
 * RuntimeException
 * 	Exception thrown if an error which can only be found on runtime occurs.
 *
 * UnderflowException
 * 	Exception thrown when performing an invalid operation on an empty container, such as removing an element.
 *
 * UnexpectedValueException
 * 	Exception thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors.
 */
declare(strict_types=1);

namespace Inane\Stdlib\Exception;

/**
 * ExceptionInterface
 *
 *  - 0  : Exception
 *  - 100: ExampleException
 *  - 200: RuntimeException
 *  - 300: ParseException
 *  - 350: ParseMethodException
 *  - 355: InvalidArgumentException
 *  - 360: InvalidPropertyException
 *  - 780: FileException
 *
 * @version 1.0.0
 */
interface ExceptionInterface {
}
