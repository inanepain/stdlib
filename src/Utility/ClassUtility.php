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

declare(strict_types=1);

namespace Inane\Stdlib\Utility;

use Inane\File\File;
use Inane\Stdlib\Exception\Exception;
use function array_slice;
use function count;
use function explode;
use function implode;
use function is_string;
use function strtolower;
use function token_get_all;
use const T_CLASS;
use const T_NAMESPACE;

class ClassUtility {
    /**
     * Build a class id based on a class name.
     *
     * Some customisation is available.
     *
     * @param string $className fully qualified class name
     * @param int $size number of parts used, namespace and class
     * @param string $separator used when combining parts
     * @param bool $lower convert to lowercase
     *
     * @return string class id
     */
    public static function classId(string $className, int $size = 1, string $separator = '/', bool $lower = true): string {
        $ids = explode('\\', $className);
        $cids =  array_slice($ids, $size * -1);
        $id = implode($separator, $cids);

        return $lower ? strtolower($id) : $id;
    }

    /**
     * Extracts the fully qualified class name from the given file.
     *
     * @param string|File $file The file object containing the PHP code to search for a class.
     *
     * @return string|null The fully qualified class name if found, or null if no class is found.
     *
     * @throws Exception If the file is not valid or cannot be found.
     */
    public static function getClassFromFile(string|File $file): ?string {
        if (is_string($file)) $file = new File($file);

        if (!$file->isValid()) throw new Exception("File not found: $file");

        $src = $file->read();
        $tokens = token_get_all($src);

        $namespace = '';
        $class = '';
        $i = 0;

        while($i < count($tokens)) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                $i += 2; // skip namespace keyword and whitespace
                $namespace .= $tokens[$i][1];
                $i++;
            }

            if ($tokens[$i][0] === T_CLASS) {
                $i += 2; // skip class keyword and whitespace
                $class = $tokens[$i][1];
                break;
            }
            $i++;
        }

        if (!$class) {
            return null; // no class found
        }

        return $namespace ? $namespace . "\\" . $class : $class;
    }
}
