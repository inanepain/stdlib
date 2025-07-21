<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package Inane\Stdlib
 * @category converter
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\Converters;

/**
 * XML Export Interface
 *
 * @package Inane\Stdlib
 *
 * @version 0.1.0
 */
interface XMLable {
    /**
     * Return XML representation of data
     *
     * @return array as XML
     */
    public function toXML(): string;
}
