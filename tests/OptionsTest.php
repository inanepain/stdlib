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

use Inane\Stdlib\Exception\RuntimeException;
use Inane\Stdlib\Options;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Stdlib\Options`.
 */
final class OptionsTest extends TestCase {
    /**
     * Verifies nested arrays are converted to nested `Options` instances and
     * JSON input is decoded correctly during construction.
     *
     * @return void
     */
    public function testConstructBuildsNestedOptionsFromArrayAndJsonString(): void {
        $fromArray = new Options([
            'section' => ['enabled' => true],
            'name' => 'app',
        ]);

        $this->assertInstanceOf(Options::class, $fromArray->section);
        $this->assertTrue($fromArray->section->enabled);
        $this->assertSame('app', $fromArray->name);

        $fromJson = new Options('{"feature": {"active": true}, "port": 8080}');
        $this->assertInstanceOf(Options::class, $fromJson->feature);
        $this->assertTrue($fromJson->feature->active);
        $this->assertSame(8080, $fromJson->port);
    }

    /**
     * Verifies kebab-case keys can be read and updated via camelCase/PascalCase
     * aliases.
     *
     * @return void
     */
    public function testGetAndSetSupportCaseConversionForKebabKeys(): void {
        $options = new Options([
            'db-host' => 'localhost',
            'api-key' => 'old',
        ]);

        $this->assertSame('localhost', $options->get('dbHost'));
        $this->assertSame('localhost', $options->get('DbHost'));

        $options->apiKey = 'new';
        $this->assertSame('new', $options->get('api-key'));
        $this->assertFalse($options->offsetExists('apiKey'));
    }

    /**
     * Verifies `pull()` returns a value and removes the key.
     *
     * @return void
     */
    public function testPullReturnsValueAndUnsetsKey(): void {
        $options = new Options(['token' => 'abc']);

        $value = $options->pull('token');

        $this->assertSame('abc', $value);
        $this->assertFalse($options->has('token'));
    }

    /**
     * Verifies a locked options object rejects writes and lock cascades to
     * nested option objects.
     *
     * @return void
     */
    public function testLockPreventsWritesAndLocksNestedOptions(): void {
        $options = new Options([
            'name' => 'before',
            'nested' => ['flag' => true],
        ]);

        $options->lock();

        $this->assertTrue($options->isLocked());
        $this->assertTrue($options->nested->isLocked());

        $this->expectException(RuntimeException::class);
        $options->name = 'after';
    }

    /**
     * Verifies write attempts on a locked object can be ignored when
     * `lockWriteError` is disabled.
     *
     * @return void
     */
    public function testLockedWriteCanFailSilentlyWhenLockWriteErrorIsDisabled(): void {
        $options = new Options(['name' => 'before']);
        $options->lock();
        $options->lockWriteError = false;

        $options->name = 'after';
        $options->unset('name');

        $this->assertNull($options->name);
        $this->assertFalse($options->has('name'));
    }

    /**
     * Verifies `merge()` appends integer keys, overwrites scalar string keys,
     * and recursively merges nested options.
     *
     * @return void
     */
    public function testMergeAppliesAppendOverwriteAndRecursiveMergeRules(): void {
        $options = new Options([
            'name' => 'base',
            'nested' => ['keep' => 1],
            0 => 'first',
        ]);

        $options->merge([
            'name' => 'updated',
            'nested' => ['add' => 2],
            1 => 'second',
        ]);

        $this->assertSame('updated', $options->name);
        $this->assertSame(1, $options->nested->keep);
        $this->assertSame(2, $options->nested->add);
        $this->assertSame('first', $options->get(0));
        $this->assertSame('second', $options->get(1));
    }

    /**
     * Verifies `defaults()` fills only missing or replaceable values and keeps
     * `false` values unchanged.
     *
     * @return void
     */
    public function testDefaultsFillsMissingAndReplaceableValuesButNotFalse(): void {
        $options = new Options([
            'title' => '',
            'enabled' => false,
            'nested' => ['name' => null],
        ]);

        $options->defaults([
            'title' => 'Default Title',
            'enabled' => true,
            'missing' => 'created',
            'nested' => ['name' => 'Nested Default', 'x' => 'y'],
        ]);

        $this->assertSame('Default Title', $options->title);
        $this->assertFalse($options->enabled);
        $this->assertSame('created', $options->missing);
        $this->assertNull($options->nested->name);
        $this->assertFalse($options->nested->has('x'));
    }

    /**
     * Verifies `modify()` updates only existing keys and recursively modifies
     * nested options.
     *
     * @return void
     */
    public function testModifyUpdatesOnlyExistingKeys(): void {
        $options = new Options([
            'name' => 'base',
            'nested' => ['keep' => 1],
        ]);

        $options->modify([
            'name' => 'changed',
            'added' => 'ignored',
            'nested' => ['keep' => 2, 'new' => 3],
        ]);

        $this->assertSame('changed', $options->name);
        $this->assertFalse($options->has('added'));
        $this->assertSame(2, $options->nested->keep);
        $this->assertFalse($options->nested->has('new'));
    }

    /**
     * Verifies `complete()` only adds missing keys and supports excluded keys.
     *
     * @return void
     */
    public function testCompleteAddsMissingKeysAndHonoursExcludeList(): void {
        $options = new Options([
            'name' => 'base',
            'nested' => ['keep' => 1],
        ]);

        $options->complete([
            'name' => 'ignored',
            'add' => 'added',
            'skip' => 'excluded',
            'nested' => ['keep' => 1, 'new' => 2],
        ], ['skip']);

        $this->assertSame('base', $options->name);
        $this->assertSame('added', $options->add);
        $this->assertSame('excluded', $options->skip);
        $this->assertSame(1, $options->nested->keep);
        $this->assertSame(2, $options->nested->new);
    }

    /**
     * Verifies sorting and unique filtering can return copies while leaving the
     * original options unchanged.
     *
     * @return void
     */
    public function testSortAndUniqueCanReturnCopiesWithoutMutatingOriginal(): void {
        $options = new Options([
            'c' => 3,
            'a' => 1,
            'b' => 1,
        ]);

        $sortedCopy = $options->sort(true, true);
        $uniqueCopy = $options->unique(true);

        $this->assertSame(['a', 'b', 'c'], $sortedCopy->keys());
        $this->assertSame(['c', 'a', 'b'], $options->keys());
        $this->assertSame(['c' => 3, 'a' => 1], $uniqueCopy->toArray());
        $this->assertSame(['c' => 3, 'a' => 1, 'b' => 1], $options->toArray());
    }

    /**
     * Verifies serialisation helpers preserve option data.
     *
     * @return void
     */
    public function testSerialisationRoundTripsData(): void {
        $options = new Options(['name' => 'inane', 'count' => 2]);

        $state = $options->__serialize();
        $restored = new Options();
        $restored->__unserialize($state);

        $legacy = new Options();
        $legacy->unserialize($options->serialize() ?? '');

        $this->assertSame($options->toArray(), $restored->toArray());
        $this->assertSame($options->toArray(), $legacy->toArray());
    }
}
