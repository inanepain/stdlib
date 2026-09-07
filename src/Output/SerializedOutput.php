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

use function serialize;

/**
 * SerializedOutput
 *
 * Provides functionality to serialise input data into a string format.
 */
class SerializedOutput extends AbstractOutput {
    /**
     * Serialise the input data.
     *
     * @param mixed $inputData The data to serialise.
     *
     * @return string The serialised data.
     *
     * @throws \RuntimeException
     */
    public function output(mixed $inputData = null): string {
        $this->setInputData($inputData);

        if (!isset($this->outputData)) $this->outputData = serialize($this->inputData);

        return $this->outputData;
    }
}
