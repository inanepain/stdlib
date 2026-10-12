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

use Inane\Stdlib\Converters\ArrayToXML;
use SimpleXMLElement;

use function is_string;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function simplexml_load_string;
use function trim;

/**
 * XmlOutput
 *
 * Provides functionality to convert input data to an XML format.
 */
class XmlOutput extends AbstractOutput {
    use ArrayToXML;

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
     * @inheritDoc
     *
     * @param mixed $inputData The data to convert.
     *
     * @return SimpleXMLElement
     */
    public function output(mixed $inputData = null): SimpleXMLElement {
        $this->setInputData($inputData);

        if (!isset($this->outputData)) {
            if (is_string($this->inputData) && static::isXmlString($this->inputData)) {
                $this->outputData = simplexml_load_string($this->inputData);
            } else {
                $this->outputData = static::arrayToXML(new ArrayOutput($this->inputData)->output());
            }
        }

        return $this->outputData;
    }

    /**
     * Return object as an XML string
     *
     * @example
     * return static::arrayToXML($this->data)->asXML();
     *
     * @return string
     */
    public function toXML(): string {
        return $this->output()
            ->asXML()
        ;
    }
}
