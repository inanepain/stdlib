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

namespace Inane\Stdlib\Converters;

use SimpleXMLElement;

use function is_array;
use function is_null;
use function is_numeric;
use const false;
use const null;

/**
 * Array To XML
 *
 * @version 0.1.0
 *
 * @package Inane\Stdlib
 */
trait ArrayToXML {
    /**
     * Return object as an XML string
     *
     * @example
     * return static::arrayToXML($this->data)->asXML();
     *
     * @return string
     */
    abstract public function toXML(): string;

    /**
     * Convert $array to an XML string
     *
     * @param array $array source data
     * @param null|\SimpleXMLElement $xmlObj XML object to use as root
     * @param bool $unique appends the array index to tag names for plain arrays
     * @param null|string $tagName to use when converting plain arrays, mainly for internally use
     *
     * @return \SimpleXMLElement XML string of $array
     */
    protected static function arrayToXML(array $array, ?SimpleXMLElement $xmlObj = null, bool $unique = false, ?string $tagName = null): SimpleXMLElement {
        if (is_null($xmlObj)) $xmlObj = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

        foreach ($array as $key => $value) {
            if (is_numeric($key))
                $key = ($tagName ?? 'item') . ($unique ? $key : '');
            if (is_array($value))
                static::arrayToXML($value, $xmlObj->addChild($key), $unique, \Inane\Stdlib\String\Inflector::singularise($key));
            else
                $xmlObj->addChild("$key", htmlspecialchars("$value"));
        }

        return $xmlObj;
    }
}
