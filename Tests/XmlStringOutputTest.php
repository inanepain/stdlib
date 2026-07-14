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

use Inane\Stdlib\Output\XmlStringOutput;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Output\XmlStringOutput`.
 */
final class XmlStringOutputTest extends TestCase {
    /**
     * Verifies array input is converted to an XML string representation.
     *
     * @return void
     */
    public function testOutputReturnsXmlStringForArrayInput(): void {
        $output = new XmlStringOutput([
            'name' => 'Ada',
            'items' => ['first'],
        ]);

        $xml = $output->output();

        $this->assertStringContainsString('<?xml version="1.0"?>', $xml);
        $this->assertStringContainsString('<name>Ada</name>', $xml);
        $this->assertStringContainsString('<item>first</item>', $xml);
    }

    /**
     * Verifies valid XML input remains valid when emitted as a string.
     *
     * @return void
     */
    public function testOutputReturnsXmlStringForXmlInput(): void {
        $output = new XmlStringOutput('<?xml version="1.0"?><root><name>Ada</name></root>');

        $xml = $output->output();

        $this->assertStringContainsString('<name>Ada</name>', $xml);
    }
}
