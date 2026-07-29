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

use Inane\Stdlib\Output\SerializedOutput;
use PHPUnit\Framework\TestCase;

use function serialize;

final class SerializeCounterPayload {
    public int $calls = 0;

    public function __serialize(): array {
        $this->calls++;

        return ['call' => $this->calls];
    }
}

/**
 * Tests for `Inane\Stdlib\Output\SerializedOutput`.
 */
final class SerializedOutputTest extends TestCase {
    /**
     * Verifies output serialises the provided input payload.
     *
     * @return void
     */
    public function testOutputSerializesInputData(): void {
        $input = ['name' => 'Ada', 'enabled' => true, 'count' => 2];

        $output = new SerializedOutput($input);

        $this->assertSame(serialize($input), $output->output());
    }

    /**
     * Verifies output value is cached after the first serialisation.
     *
     * @return void
     */
    public function testOutputCachesSerializedValue(): void {
        $payload = new SerializeCounterPayload();

        $output = new SerializedOutput($payload);

        $first = $output->output();
        $second = $output->output();

        $this->assertSame($first, $second);
        $this->assertSame(1, $payload->calls);
    }
}
