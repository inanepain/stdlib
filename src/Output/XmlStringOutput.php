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

/**
 * XmlStringOutput
 */
class XmlStringOutput extends AbstractOutput {
    protected static function isXmlString(string $string): bool {
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
     */
    public function output(): string {
        if (!isset($this->outputData)) {
            $xo = new \Inane\Stdlib\Output\XmlOutput($this->inputData);
            $this->outputData = $xo->output()->asXML();
        }

        return $this->outputData;
    }
}
