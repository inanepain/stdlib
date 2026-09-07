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

use InvalidArgumentException;

/**
 * Abstract Output
 *
 * Abstract class representing the blueprint for handling and processing input data
 * into a specific output format. This class ensures that the necessary methods and
 * properties are implemented by its subclasses.
 *
 * @version 0.2.0
 */
abstract class AbstractOutput implements OutputInterface {
    /**
     * Holds the processed output data in the output format.
     *
     * @var mixed The resulting data after processing or computation.
     */
    protected mixed $outputData;

    /**
     * Initialises the class with the provided input data.
     *
     * @param mixed $inputData The input data to be processed or utilised by the class.
     *
     * @return void
     */
    public function __construct(protected mixed $inputData = null) {}

    /**
     * Sets the input data for the class and resets the output data if necessary.
     *
     * @since 0.2.0
     *
     * @param mixed $inputData The input data to be stored and used by the class.
     *                         It will override the existing input data if it differs.
     *
     * @return void
     *
     * @throws InvalidArgumentException If the provided input data is of an unsupported type.
     */
    protected function setInputData(mixed $inputData = null): void {
        if ($inputData !== null && $inputData !== $this->inputData) {
            $this->inputData = $inputData;
            if (isset($this->outputData)) unset($this->outputData);
        }
    }

    /**
     * Processes the input data and converts it into the output format.
     *
     * @param mixed $inputData The input data to be processed or utilised by the class.
     *
     * @return array The processed representation of the input data in the output format.
     */
    abstract public function output(mixed $inputData = null): mixed;
}
