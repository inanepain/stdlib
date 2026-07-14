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

use Inane\Stdlib\Output\JsonStringOutput;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Output\JsonStringOutput`.
 */
final class JsonStringOutputTest extends TestCase {
    /**
     * Verifies JSON output applies numeric conversion and unescaped slashes.
     *
     * @return void
     */
    public function testOutputEncodesWithExpectedJsonOptions(): void {
        $output = new JsonStringOutput([
            'id' => '42',
            'url' => 'https://example.test/path',
        ]);

        $json = $output->output();

        $this->assertIsString($json);
        $this->assertStringContainsString('"id":42', $json);
        $this->assertStringContainsString('https://example.test/path', $json);
        $this->assertStringNotContainsString('https:\\/\\/example.test\\/path', $json);
    }

    /**
     * Verifies output is cached after the first conversion.
     *
     * @return void
     */
    public function testOutputCachesEncodedValue(): void {
        $payload = new class implements JsonSerializable {
            public int $calls = 0;

            public function jsonSerialize(): mixed {
                $this->calls++;

                return ['call' => $this->calls];
            }
        };

        $output = new JsonStringOutput($payload);

        $first = $output->output();
        $second = $output->output();

        $this->assertSame('{"call":1}', $first);
        $this->assertSame($first, $second);
        $this->assertSame(1, $payload->calls);
    }
}
