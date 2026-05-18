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
 * AbstractOutput
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
    public function __construct(protected mixed $inputData) {}

    /**
     * Processes the input data and converts it into the output format.
     *
     * @return array The processed representation of the input data in the output format.
     */
    abstract public function output(): mixed;
}
