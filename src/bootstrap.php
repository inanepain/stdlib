<?php

/**
 * Inane: Stdlib
 *
 * Inane Stdlib constants
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab <peep@inane.co.za>
 * @package Inane\Stdlib
 * @category constants
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

/**
 * HASH_TEST_METHOD_REGEX
 *
 * Tests hash using `preg_match`
 *
 * usage:
 * define('HASH_TEST_METHOD', HASH_TEST_METHOD_REGEX);
 */
if (!defined('HASH_TEST_METHOD_REGEX')) define('HASH_TEST_METHOD_REGEX', 0);

/**
 * HASH_TEST_METHOD_CTYPE
 *
 * Tests hash using `ctype_xdigit`
 *
 * usage:
 * define('HASH_TEST_METHOD', HASH_TEST_METHOD_CTYPE);
 */
if (!defined('HASH_TEST_METHOD_CTYPE')) define('HASH_TEST_METHOD_CTYPE', 1);
