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

use Inane\Stdlib\Converters\TraversableToArray;
use Inane\Stdlib\Json;

use function is_array;
use function is_object;
use function is_string;
use function json_last_error;
use function unserialize;

use const JSON_ERROR_NONE;

/**
 * ArrayOutput
 */
class ArrayOutput extends AbstractOutput {
    use TraversableToArray;

    /**
     * Processes the input data and converts it into an array format, ensuring compatibility with various input types.
     *
     * @return array The processed representation of the input data in an array format.
     */
    public function output(): array {
        if (!isset($this->outputData)) {
            if (is_array($this->inputData)) $result = $this->inputData;
            elseif (is_string($this->inputData)) {
                $result = Json::decode($this->inputData, [
                    'numeric' => true,
                    'escape'  => true,
                ]);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $result = @unserialize($this->inputData, ['allowed_classes' => true]);
                    if ($result === false) $result = [$this->inputData];
                }
            } elseif (is_object($this->inputData)) $result = static::iteratorToArrayDeep($this->inputData);
            else $result = [$this->inputData];

            $this->outputData = $result;
        }

        return $this->outputData;
    }
}
