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

use Inane\Stdlib\Json;

use function is_string;

/**
 * JsonStringOutput
 *
 * Provides functionality to output data as a JSON encoded string.
 */
class JsonStringOutput extends AbstractOutput {
    /**
     * Encode and return the input data as a JSON string.
     *
     * @param mixed $inputData The data to encode.
     *
     * @return false|string The encoded JSON string or false on failure.
     *
     * @throws \RuntimeException
     */
    public function output(mixed $inputData = null): false|string {
        $this->setInputData($inputData);

        if (!isset($this->outputData)) {
            if (!is_string($this->inputData)) {
                $this->outputData = Json::encode($this->inputData, [
                    'numeric' => true,
                    'escape'  => true,
                ]);
            }
        }

        return $this->outputData;
    }
}
