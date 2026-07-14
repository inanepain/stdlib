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

use Inane\Stdlib\Output\XmlOutput;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * Tests for `Inane\Stdlib\Output\XmlOutput`.
 */
final class XmlOutputTest extends TestCase {
    /**
     * Verifies valid XML strings are parsed directly.
     *
     * @return void
     */
    public function testOutputParsesValidXmlStringInput(): void {
        $xmlString = '<?xml version="1.0"?><root><name>Ada</name></root>';

        $output = new XmlOutput($xmlString);
        $xml = $output->output();

        $this->assertInstanceOf(SimpleXMLElement::class, $xml);
        $this->assertSame('Ada', (string) $xml->name);
    }

    /**
     * Verifies arrays are converted into XML with escaped scalar values.
     *
     * @return void
     */
    public function testOutputConvertsArrayToXmlWithEscapedValues(): void {
        $output = new XmlOutput([
            'name' => 'A & B',
            'items' => ['first', 'second'],
        ]);

        $xml = $output->output()->asXML();

        $this->assertIsString($xml);
        $this->assertStringContainsString('<name>A &amp; B</name>', $xml);
        $this->assertStringContainsString('<item>first</item>', $xml);
        $this->assertStringContainsString('<item>second</item>', $xml);
    }

    /**
     * Verifies scalar input is normalised by `ArrayOutput` and emitted as XML.
     *
     * @return void
     */
    public function testOutputConvertsScalarInputToItemXml(): void {
        $output = new XmlOutput(7);

        $xml = $output->output()->asXML();

        $this->assertIsString($xml);
        $this->assertStringContainsString('<item>7</item>', $xml);
    }
}
