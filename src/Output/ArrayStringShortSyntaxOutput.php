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

use Inane\Stdlib\Converters\ArrayStringShortSyntaxTrait;

/**
 * ArrayShortSyntaxStringOutput
 *
 * Provides functionality to output data in a short array syntax string format.
 */
class ArrayStringShortSyntaxOutput extends AbstractOutput {
    use ArrayStringShortSyntaxTrait;

    /**
     * Convert and return the input data as a short array syntax string.
     *
     * @return string The data as a short array syntax string.
     *
     * @throws \RuntimeException
     */
    public function output(): string {
        if (!isset($this->outputData)) {
            $this->outputData = self::arrayToString(new ArrayOutput($this->inputData)->output());
        }

        return $this->outputData;
    }
}
