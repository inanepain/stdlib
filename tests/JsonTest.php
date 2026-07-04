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

use Inane\Stdlib\Exception\JsonException;
use Inane\Stdlib\Json;
use Inane\Stdlib\Options;
use PHPUnit\Framework\TestCase;

use function file_put_contents;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function uniqid;
use function unlink;

use const JSON_ERROR_NONE;

/**
 * Tests for `Inane\Stdlib\Json`.
 */
final class JsonTest extends TestCase {
    /**
     * Ensures valid JSON strings are recognised and report no error details.
     *
     * @return void
     */
    public function testIsJsonStringAcceptsValidJsonAndPopulatesNoError(): void {
        $error = [];

        $result = Json::isJsonString('{"name":"Ada"}', true, $error);

        $this->assertTrue($result);
        $this->assertSame(JSON_ERROR_NONE, $error['code']);
        $this->assertSame('No error', $error['message']);
    }

    /**
     * Ensures invalid JSON strings are rejected and expose error metadata.
     *
     * @return void
     */
    public function testIsJsonStringRejectsInvalidJsonAndReturnsErrorInfo(): void {
        $error = [];

        $result = Json::isJsonString('{"name":', true, $error);

        $this->assertFalse($result);
        $this->assertNotSame(JSON_ERROR_NONE, $error['code']);
        $this->assertNotSame('No error', $error['message']);
    }

    /**
     * Verifies default encoding options include numeric conversion and unescaped slashes.
     *
     * @return void
     */
    public function testEncodeAppliesDefaultNumericAndEscapeOptions(): void {
        $json = Json::encode([
            'id'  => '42',
            'url' => 'https://example.test/path',
        ]);

        $this->assertIsString($json);
        $this->assertStringContainsString('"id":42', $json);
        $this->assertStringContainsString('https://example.test/path', $json);
        $this->assertStringNotContainsString('https:\\/\\/example.test\\/path', $json);
    }

    /**
     * Verifies encode can apply pretty-print and hexadecimal safety options together.
     *
     * @return void
     */
    public function testEncodeSupportsPrettyAndHexOutput(): void {
        $json = Json::encode(
            ['x' => '<"\'&>'],
            ['pretty' => true, 'hex' => true, 'escape' => false, 'numeric' => false],
        );

        $this->assertIsString($json);
        $this->assertStringContainsString("\n", $json);
        $this->assertStringContainsString('\\u003C', $json);
        $this->assertStringContainsString('\\u003E', $json);
    }

    /**
     * Verifies encoding failures return false and expose an error payload.
     *
     * @return void
     */
    public function testEncodeReturnsFalseAndErrorDetailsOnInvalidUtf8ByDefault(): void {
        $error = [];
        $json = Json::encode(["value" => "\xB1\x31"], [], null, $error);

        $this->assertFalse($json);
        $this->assertNotEmpty($error);
        $this->assertNotSame(JSON_ERROR_NONE, $error['code']);
    }

    /**
     * Verifies throw mode rethrows as `Inane\Stdlib\Exception\JsonException`.
     *
     * @return void
     */
    public function testEncodeThrowsJsonExceptionWhenOnErrorIsThrow(): void {
        $this->expectException(JsonException::class);

        Json::encode(["value" => "\xB1\x31"], ['onerror' => 'throw']);
    }

    /**
     * Verifies decode returns arrays by default and can return an `Options` object.
     *
     * @return void
     */
    public function testDecodeReturnsArrayOrOptionsDependingOnOption(): void {
        $array = Json::decode('{"name":"Ada"}');
        $options = Json::decode('{"name":"Ada"}', ['asOptions' => true]);

        $this->assertIsArray($array);
        $this->assertSame('Ada', $array['name']);

        $this->assertInstanceOf(Options::class, $options);
        $this->assertSame('Ada', $options->name);
    }

    /**
     * Verifies decode failures return null and include the error payload.
     *
     * @return void
     */
    public function testDecodeReturnsNullAndErrorOnInvalidJson(): void {
        $error = [];
        $decoded = Json::decode('{"name":', [], $error);

        $this->assertNull($decoded);
        $this->assertNotEmpty($error);
        $this->assertNotSame(JSON_ERROR_NONE, $error['code']);
    }

    /**
     * Verifies decode throw mode raises a stdlib `JsonException`.
     *
     * @return void
     */
    public function testDecodeThrowsJsonExceptionWhenOnErrorIsThrow(): void {
        $this->expectException(JsonException::class);

        Json::decode('{"name":', ['onerror' => 'throw']);
    }

    /**
     * Verifies file decoding reads valid JSON from readable files.
     *
     * @return void
     */
    public function testDecodeFileReturnsDecodedDataForReadableFile(): void {
        $file = tempnam(sys_get_temp_dir(), 'json-test-');
        $this->assertIsString($file);

        file_put_contents($file, '{"status":"ok","count":2}');

        $decoded = Json::decodeFile($file);

        $this->assertIsArray($decoded);
        $this->assertSame('ok', $decoded['status']);
        $this->assertSame(2, $decoded['count']);

        unlink($file);
    }

    /**
     * Verifies file decoding returns null for missing or unreadable files.
     *
     * @return void
     */
    public function testDecodeFileReturnsNullForMissingFile(): void {
        $missing = sprintf('%s/json-missing-%s.json', sys_get_temp_dir(), uniqid('', true));

        $decoded = Json::decodeFile($missing);

        $this->assertNull($decoded);
    }
}
