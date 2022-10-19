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
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\String;

use function array_values;
use function count;
use function strlen;
use function strpos;
use function substr;

/**
 * String Utility
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.0
 */
class StringUtility {
    /**
     * Find longest common substring
     *
     * finds the stem (longest common substring) from the string array
     *
     * @param string[] $strings list of string to search
     *
     * @return string longest common substring
     */
    public static function commonSubstring(array $strings): string {
        $d = array_values($strings); // make sure array index consecutive
        $c = count($d); // size of pool

        $s = $d[0]; // get the shortest string
        for ($i = 1; $i < $c; $i++)
            if (strlen($d[$i]) < strlen($s)) $s = $d[$i];
        $l = strlen($s); // it's length is the limit

        $r = '';
        for ($i = 0; $i < $l; $i++) for ($j = $i + 1; $j <= $l; $j++) { // generating all possible substrings within limits
            $m = substr($s, $i, $j - $i);

            for ($k = 1; $k < $c; $k++) if (strpos($d[$k], $m) === false) // Check if common to all
                break 2;

            if (strlen($r) >= $l) return $r;
            if ($k <= $c && strlen($r) < strlen($m)) // If common to all & longer then current
                $r = $m;
        }

        return $r;
    }
}
