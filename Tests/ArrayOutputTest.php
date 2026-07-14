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
use Inane\Stdlib\Output\ArrayOutput;
use PHPUnit\Framework\TestCase;

use function serialize;

/**
 * Tests for `Inane\Stdlib\Output\ArrayOutput`.
 */
final class ArrayOutputTest extends TestCase {
    /**
     * Verifies that array input is returned unchanged.
     *
     * @return void
     */
    public function testOutputReturnsArrayInputUnchanged(): void {
        $input = ['name' => 'Ada', 'count' => 2];

        $output = new ArrayOutput($input);

        $this->assertSame($input, $output->output());
    }

    /**
     * Verifies that JSON string input is decoded and numeric strings are converted.
     *
     * @return void
     */
    public function testOutputDecodesJsonStringInput(): void {
        $output = new ArrayOutput('{"name":"Ada","count":"2"}');
        $result = $output->output();

        $this->assertSame('Ada', $result['name']);
        $this->assertSame('2', $result['count']);
    }

    /**
     * Verifies that non-JSON string input falls back to `unserialize`.
     *
     * @return void
     */
    public function testOutputUnserializesSerializedStringInput(): void {
        $input = serialize(['enabled' => true, 'attempts' => 3]);

        $output = new ArrayOutput($input);

        $this->assertSame(['enabled' => true, 'attempts' => 3], $output->output());
    }

    /**
     * Verifies that plain strings are wrapped in a one-item array when decoding fails.
     *
     * @return void
     */
    public function testOutputWrapsPlainStringWhenJsonAndSerializationFail(): void {
        $input = 'just-a-string';

        $output = new ArrayOutput($input);

        $this->assertSame([$input], $output->output());
    }

    /**
     * Verifies that traversable objects are deeply converted into arrays.
     *
     * @return void
     */
    public function testOutputConvertsTraversableObjectToArray(): void {
        $input = new ArrayIterator([
            'top' => 'value',
            'nested' => new ArrayIterator([
                'leaf' => 42,
            ]),
        ]);

        $output = new ArrayOutput($input);

        $this->assertSame(
            [
                'top' => 'value',
                'nested' => ['leaf' => 42],
            ],
            $output->output(),
        );
    }
}
