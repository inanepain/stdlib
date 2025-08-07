<?php

/**
 * Inane: Stdlib
 *
 * Common classes, tools and utilities used throughout the inanepain libraries.
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
 * @version $version
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
