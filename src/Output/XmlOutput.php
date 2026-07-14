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

namespace Inane\Stdlib\Output;

use Inane\Stdlib\String\Inflector;
use SimpleXMLElement;

use function htmlspecialchars;
use function is_array;
use function is_null;
use function is_numeric;

/**
 * XmlOutput
 *
 * Provides functionality to convert input data to an XML format.
 */
class XmlOutput extends AbstractOutput {
    /**
     * Determine if a string is a valid XML string.
     *
     * @param string $string The source string.
     *
     * @return bool True if valid XML, false otherwise.
     */
    public static function isXmlString(string $string): bool {
        $string = trim($string);

        if ($string === '') {
            return false;
        }

        libxml_use_internal_errors(true);
        $result = simplexml_load_string($string) !== false;
        libxml_clear_errors();

        return $result;
    }

    /**
     * Convert $array to an XML string
     *
     * @param array                 $array   source data
     * @param null|SimpleXMLElement $xmlObj  XML object to use as root
     * @param bool                  $unique  appends the array index to tag names for plain arrays
     * @param null|string           $tagName to use when converting plain arrays, mainly for internally use
     *
     * @return SimpleXMLElement XML string of $array
     *
     * @throws \Exception
     */
    protected static function arrayToXML(array $array, ?SimpleXMLElement $xmlObj = null, bool $unique = false, ?string $tagName = null): SimpleXMLElement {
        if (is_null($xmlObj)) $xmlObj = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

        foreach($array as $key => $value) {
            if (is_numeric($key))
                $key = ($tagName ?? 'item') . ($unique ? $key : '');
            if (is_array($value))
                static::arrayToXML($value, $xmlObj->addChild($key), $unique, Inflector::singularise($key));
            else
                $xmlObj->addChild("$key", htmlspecialchars((string)$value));
        }

        return $xmlObj;
    }

    /**
     * @inheritDoc
     *
     * @return SimpleXMLElement
     *
     * @throws \RuntimeException
     */
    public function output(): SimpleXMLElement {
        if (!isset($this->outputData)) {
            if (is_string($this->inputData) && static::isXmlString($this->inputData)) {
                $this->outputData = simplexml_load_string($this->inputData);
            } else {
                $this->outputData = static::arrayToXML(new ArrayOutput($this->inputData)->output());
            }
        }

        return $this->outputData;
    }
}
