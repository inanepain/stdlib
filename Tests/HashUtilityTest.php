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

use Inane\Stdlib\Hash\HashType;
use Inane\Stdlib\Hash\HashUtility;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Hash\HashUtility`.
 */
final class HashUtilityTest extends TestCase {
    /**
     * Verifies MD5-like hashes are identified correctly with and without
     * extended matching enabled.
     *
     * @return void
     */
    public function testIdentifyHashForMd5LengthWithAndWithoutExtendedMatching(): void {
        $md5Hash = 'd41d8cd98f00b204e9800998ecf8427e';

        $basic = HashUtility::identifyHash($md5Hash);
        $this->assertSame([HashType::MD5], $basic);

        $extended = HashUtility::identifyHash($md5Hash, true);
        $this->assertContains(HashType::MD5, $extended);
        $this->assertContains(HashType::HAVAL_128, $extended);
        $this->assertContains(HashType::Snefru_128, $extended);
        $this->assertContains(HashType::RIPEMD_128, $extended);
        $this->assertCount(4, $extended);
    }

    /**
     * Verifies identification by format for prefixed hash styles and fixed
     * lengths that have specific handling.
     *
     * @return void
     */
    public function testIdentifyHashSupportsPrefixedAndSpecialLengthFormats(): void {
        $this->assertContains(HashType::Bcrypt, HashUtility::identifyHash('$2y$10$abcdefghijklmnopqrstuu0m6eh7qfYEf4b4gQzFfL4xN6w8fQfB2'));
        $this->assertContains(HashType::PBKDF2, HashUtility::identifyHash('$pbkdf2-sha256$1000$salt$hash'));
        $this->assertContains(HashType::Argon2, HashUtility::identifyHash('$argon2id$v=19$m=65536,t=4,p=1$c2FsdA$YWJj'));
        $this->assertContains(HashType::RIPEMD_320, HashUtility::identifyHash(str_repeat('a', 80)));
    }

    /**
     * Verifies non-hash-like input returns no matching hash types.
     *
     * @return void
     */
    public function testIdentifyHashReturnsEmptyArrayForUnknownInput(): void {
        $this->assertSame([], HashUtility::identifyHash('not-a-hash'));
    }

    /**
     * Verifies hashing works with enum and string hash type inputs.
     *
     * @return void
     */
    public function testHashAcceptsEnumAndStringHashType(): void {
        $data = 'inane-fw';

        $this->assertSame(hash('md5', $data), HashUtility::hash($data, HashType::MD5));
        $this->assertSame(hash('sha256', $data), HashUtility::hash($data, 'sha-256'));
    }

    /**
     * Verifies invalid hash algorithm names raise a `ValueError`.
     *
     * @return void
     */
    public function testHashThrowsValueErrorForInvalidHashType(): void {
        $this->expectException(\ValueError::class);

        HashUtility::hash('value', 'definitely-invalid-hash');
    }

    /**
     * Verifies algorithm listing returns available hash algorithm names.
     *
     * @return void
     */
    public function testListAlgorythemsReturnsAvailableAlgorithms(): void {
        $algorithms = HashUtility::listAlgorythems();

        $this->assertIsArray($algorithms);
        $this->assertContains('md5', $algorithms);
    }
}
