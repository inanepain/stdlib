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
use Inane\Stdlib\Exception\JsonException;
use Inane\Stdlib\Json;

use function is_object;
use function is_string;
use function unserialize;

/**
 * ArrayOutput
 */
class ArrayOutput extends AbstractOutput {
    use TraversableToArray;

    /**
     * Processes the input data and converts it into an array format, ensuring compatibility with various input types.
     *
     * @param mixed $inputData The input data to be processed or utilised by the class.
     *
     * @return array The processed representation of the input data in an array format.
     * @throws JsonException
     */
    public function output(mixed $inputData = null): array {
        $this->setInputData($inputData);

        if (!isset($this->outputData)) {
            $data = $this->inputData;
            if (is_string($data)) {
                if (XmlOutput::isXmlString($data)) {
                    $data = new XmlOutput($data)->output();
                } elseif(Json::isJsonString($data)) {
                    $data = Json::decode($data, [
                        'numeric' => true,
                        'escape'  => true,
                    ]);
                } else {
                    $result = @unserialize($data, ['allowed_classes' => true]);
                    if ($result !== false) $data = $result;
                }
            }
            if (is_object($data)) $data = static::iteratorToArrayDeep($data);
            elseif (!is_array($data)) $data = [$data];

            $this->outputData = $data;
        }

        return $this->outputData;
    }
}
