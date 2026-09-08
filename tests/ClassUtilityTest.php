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

use Inane\File\File;
use Inane\Stdlib\Exception\Exception;
use Inane\Stdlib\Utility\ClassUtility;
use PHPUnit\Framework\TestCase;

use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * Tests for `Inane\Stdlib\Utility\ClassUtility`.
 */
final class ClassUtilityTest extends TestCase {
    /**
     * Temporary files created during a test.
     *
     * @var array<int, string>
     */
    private array $files = [];

    /**
     * Removes any temporary files created by a test.
     *
     * @return void
     */
    protected function tearDown(): void {
        foreach ($this->files as $file) if (is_file($file)) unlink($file);

        $this->files = [];
    }

    /**
     * Writes source to a temporary php file.
     *
     * @param string $source php code written to the file.
     *
     * @return string path of the temporary file.
     */
    private function createFile(string $source): string {
        $dir = sys_get_temp_dir() . '/inane-stdlib-tests';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file = tempnam($dir, 'cls') . '.php';
        file_put_contents($file, $source);
        $this->files[] = $file;

        return $file;
    }

    /**
     * Verifies the fully qualified class name is returned for a namespaced class.
     *
     * @return void
     */
    public function testGetClassFromFileReturnsFullyQualifiedName(): void {
        $file = $this->createFile(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace Inane\Stdlib\Tests\Fixture;

        class Alpha {
        }
        PHP);

        $this->assertSame('Inane\Stdlib\Tests\Fixture\Alpha', ClassUtility::getClassFromFile($file));
    }

    /**
     * Verifies a `File` instance is accepted as well as a path string.
     *
     * @return void
     */
    public function testGetClassFromFileAcceptsFileObject(): void {
        $file = $this->createFile(<<<'PHP'
        <?php

        namespace Inane\Stdlib\Tests\Fixture;

        class Beta {
        }
        PHP);

        $this->assertSame('Inane\Stdlib\Tests\Fixture\Beta', ClassUtility::getClassFromFile(new File($file)));
    }

    /**
     * Verifies only the class name is returned when there is no namespace.
     *
     * @return void
     */
    public function testGetClassFromFileWithoutNamespaceReturnsClassName(): void {
        $file = $this->createFile(<<<'PHP'
        <?php

        class Gamma {
        }
        PHP);

        $this->assertSame('Gamma', ClassUtility::getClassFromFile($file));
    }

    /**
     * Verifies the first class is returned when a file declares several.
     *
     * @return void
     */
    public function testGetClassFromFileReturnsFirstClassDeclared(): void {
        $file = $this->createFile(<<<'PHP'
        <?php

        namespace Inane\Stdlib\Tests\Fixture;

        class Delta {
        }

        class Epsilon {
        }
        PHP);

        $this->assertSame('Inane\Stdlib\Tests\Fixture\Delta', ClassUtility::getClassFromFile($file));
    }

    /**
     * Verifies null is returned when the file contains no class.
     *
     * @return void
     */
    public function testGetClassFromFileReturnsNullWhenNoClassFound(): void {
        $file = $this->createFile(<<<'PHP'
        <?php

        namespace Inane\Stdlib\Tests\Fixture;

        function zeta(): string {
            return 'zeta';
        }
        PHP);

        $this->assertNull(ClassUtility::getClassFromFile($file));
    }

    /**
     * Verifies an invalid file raises an `Exception`.
     *
     * @return void
     */
    public function testGetClassFromFileThrowsExceptionForMissingFile(): void {
        $this->expectException(Exception::class);

        ClassUtility::getClassFromFile(sys_get_temp_dir() . '/inane-stdlib-tests/does-not-exist.php');
    }

    /**
     * Verifies the `ClassIdTrait` is available via `ClassUtility`.
     *
     * @return void
     */
    public function testClassIdIsAvailableFromClassIdTrait(): void {
        $this->assertSame('classutility', ClassUtility::classId(1));
        $this->assertSame('Utility/ClassUtility', ClassUtility::classId(2, '/', false));
    }
}
