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
 * _version_ $version
 */

declare(strict_types=1);


define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);

// Levels of error correction.

define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// Supported output formats

define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG', 1);
define('QR_CACHEABLE', false);       // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR', false);       // used when QR_CACHEABLE === true
define('QR_LOG_DIR', false);         // default error logs dir

define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremely slow; set to false to significant performance boost but (probably) worst quality code
define('QR_FIND_FROM_RANDOM', 2);                                                           // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false

define('QR_PNG_MAXIMUM_SIZE', 1024);                                                       // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images
define('QRSPEC_VERSION_MAX', 40);
define('QRSPEC_WIDTH_MAX', 177);

define('QRCAP_WIDTH', 0);
define('QRCAP_WORDS', 1);
define('QRCAP_REMINDER', 2);
define('QRCAP_EC', 3);
define('QR_IMAGE', true);
define('STRUCTURE_HEADER_BITS', 20);
define('MAX_STRUCTURED_SYMBOLS', 16);
define('N1', 3);
define('N2', 3);
define('N3', 40);
define('N4', 10);

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
