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

use Inane\Stdlib\Merge\Merge;
use Inane\Stdlib\Merge\MergeMethod;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Merge\Merge`.
 */
final class MergeTest extends TestCase {
    /**
     * Verifies add-only merge keeps existing values and adds only missing keys.
     *
     * @return void
     */
    public function testMergeOptionsAddOnlyAddsMissingKeysWithoutOverwriting(): void {
        $target = [
            'name' => 'existing',
            'nested' => ['keep' => 1],
        ];

        $source = [
            'name' => 'new',
            'added' => true,
            'nested' => ['keep' => 2, 'new' => 3],
        ];

        $result = Merge::mergeOptionsAddOnly($target, $source);

        $this->assertSame('existing', $result['name']);
        $this->assertTrue($result['added']);
        $this->assertSame(['keep' => 1], $result['nested']);
    }

    /**
     * Verifies update-only merge updates existing keys and ignores new ones.
     *
     * @return void
     */
    public function testMergeOptionsUpdateOnlyUpdatesExistingKeysOnly(): void {
        $target = [
            'name' => 'existing',
            'nested' => ['keep' => 1],
        ];

        $source = [
            'name' => 'updated',
            'added' => true,
            'nested' => ['keep' => 2, 'new' => 3],
        ];

        $result = Merge::mergeOptionsUpdateOnly($target, $source);

        $this->assertSame('updated', $result['name']);
        $this->assertArrayNotHasKey('added', $result);
        $this->assertSame(['keep' => 2], $result['nested']);
    }

    /**
     * Verifies add-and-update merge updates existing keys and adds missing keys.
     *
     * @return void
     */
    public function testMergeOptionsAddAndUpdateAddsAndUpdatesKeys(): void {
        $target = [
            'name' => 'existing',
            'nested' => ['keep' => 1],
        ];

        $source = [
            'name' => 'updated',
            'added' => true,
            'nested' => ['keep' => 2, 'new' => 3],
        ];

        $result = Merge::mergeOptionsAddAndUpdate($target, $source);

        $this->assertSame('updated', $result['name']);
        $this->assertTrue($result['added']);
        $this->assertSame(['keep' => 2, 'new' => 3], $result['nested']);
    }

    /**
     * Verifies sequential sources are applied in order and later sources win.
     *
     * @return void
     */
    public function testMergeOptionsWithMethodAppliesSourcesInOrder(): void {
        $target = ['name' => 'base', 'count' => 1];

        $result = Merge::mergeOptionsWithMethod(
            MergeMethod::AddAndUpdate,
            $target,
            ['name' => 'first', 'count' => 2],
            ['name' => 'second'],
        );

        $this->assertSame('second', $result['name']);
        $this->assertSame(2, $result['count']);
    }

    /**
     * Verifies instance-level merge honours configured `mergeMethod`.
     *
     * @return void
     */
    public function testMergeOptionsUsesConfiguredInstanceMergeMethod(): void {
        $merge = new Merge();
        $merge->mergeMethod = MergeMethod::AddOnly;

        $result = $merge->mergeOptions(
            ['name' => 'existing'],
            ['name' => 'updated', 'added' => true],
        );

        $this->assertSame('existing', $result['name']);
        $this->assertTrue($result['added']);
    }
}
