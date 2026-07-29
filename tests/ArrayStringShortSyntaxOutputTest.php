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
 * @author   Philip Michael Raab <philip@cathedral.co.za>
 * @package  inanepain\stdlib
 * @category stdlib
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Tests;

use ArrayIterator;
use Inane\Stdlib\Output\ArrayStringShortSyntaxOutput;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Output\ArrayStringShortSyntaxOutput`.
 */
final class ArrayStringShortSyntaxOutputTest extends TestCase {
    /**
     * Verifies output converts data into short array syntax string format.
     *
     * @return void
     */
    public function testOutputConvertsArrayToShortSyntaxString(): void {
        $output = new ArrayStringShortSyntaxOutput([
            'name' => 'Ada',
            'count' => 2,
        ]);

        $this->assertSame(
            "[\n  'name' => 'Ada',\n  'count' => 2,\n]",
            $output->output(),
        );
    }

    /**
     * Verifies output is cached after first conversion.
     *
     * @return void
     */
    public function testOutputCachesConvertedValue(): void {
        $payload = new class ([
            'first' => 'value',
        ]) extends ArrayIterator {
            public int $validCalls = 0;

            public function valid(): bool {
                $this->validCalls++;

                return parent::valid();
            }
        };

        $output = new ArrayStringShortSyntaxOutput($payload);

        $first = $output->output();
        $firstValidCalls = $payload->validCalls;
        $second = $output->output();

        $this->assertSame($first, $second);
        $this->assertGreaterThan(0, $firstValidCalls);
        $this->assertSame($firstValidCalls, $payload->validCalls);
    }
}
