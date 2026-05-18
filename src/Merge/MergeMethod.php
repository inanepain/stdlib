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

namespace Inane\Stdlib\Merge;

use Inane\Stdlib\Enum\CoreEnumInterface;

/**
 * Enum MergeMethod defines various merge behaviors as string values.
 *
 * This enum implements CoreEnumInterface and offers functionality to work with
 * predefined merge operation types. It allows for retrieving instances based on
 * their case names with optional case-insensitivity.
 */
enum MergeMethod: string implements CoreEnumInterface {
    /**
     * Represents an operation to add only.
     */
    case AddOnly = 'Add';
    /**
     * Represents an operation to update only.
     */
    case UpdateOnly = 'Update';
    /**
     * Represents an operation to add and update.
     */
    case AddAndUpdate = 'AddUpdate';

    /**
     * Attempts to create an instance of the enum from the given name.
     *
     * @param string $name       The name of the enum case to match.
     * @param bool   $ignoreCase Whether to perform a case-insensitive match. Defaults to false.
     *
     * @return static|null Returns an instance of the enum if a match is found, or null otherwise.
     */
    public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
        foreach(static::cases() as $case)
            if (($ignoreCase && strcasecmp($case->name, $name) === 0) || $case->name === $name)
                return $case;

        return null;
    }
}
