<?php

declare(strict_types = 1);

namespace Inane\Stdlib\Tests;

use Inane\Stdlib\Converters\CastAndAssignTrait;
use Inane\Stdlib\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use stdClass;
use ValueError;

enum CastAndAssignStatus: string {
    case Pending = 'pending';
    case Complete = 'complete';
}

enum CastAndAssignDirection {
    case North;
    case South;
}

final class CastAndAssignTraitTest extends TestCase {
    use CastAndAssignTrait;

    /**
     * Checks numeric conversions and validation.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testNumericAssignment(): void {
        $integer = 0;
        self::castAndAssign($integer, '42');
        $this->assertSame(42, $integer);

        $float = 0.0;
        self::castAndAssign($float, '3.5');
        $this->assertSame(3.5, $float);

        foreach ([0, 0.0] as $target) {
            $original = $target;
            try {
                self::castAndAssign($target, 'invalid');
                $this->fail('Non-numeric input should be rejected.');
            } catch (InvalidArgumentException) {
                $this->assertSame($original, $target);
            }
        }
    }

    /**
     * Checks string, boolean and array conversions.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testStringBooleanAndArrayAssignment(): void {
        $string = '';
        self::castAndAssign($string, 27);
        $this->assertSame('27', $string);

        $boolean = false;
        self::castAndAssign($boolean, 'yes');
        $this->assertTrue($boolean);
        self::castAndAssign($boolean, 'off');
        $this->assertFalse($boolean);

        $array = [];
        self::castAndAssign($array, ['key' => 1]);
        $this->assertSame(['key' => 1], $array);
    }

    /**
     * Checks invalid scalar and array values are rejected.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testInvalidStringBooleanAndArrayAssignment(): void {
        $string = 'original';
        try {
            self::castAndAssign($string, []);
            $this->fail('An array cannot be cast to a string.');
        } catch (InvalidArgumentException) {
            $this->assertSame('original', $string);
        }

        $boolean = true;
        try {
            self::castAndAssign($boolean, 'perhaps');
            $this->fail('An invalid boolean should be rejected.');
        } catch (InvalidArgumentException) {
            $this->assertNull($boolean);
        }

        $array = ['original'];
        try {
            self::castAndAssign($array, 'not an array');
            $this->fail('Non-array input should be rejected.');
        } catch (InvalidArgumentException) {
            $this->assertSame(['original'], $array);
        }
    }

    /**
     * Checks null preservation and direct assignment.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testNullAssignment(): void {
        $value = null;
        self::castAndAssign($value, 'ignored');
        $this->assertNull($value);

        self::castAndAssign($value, ['assigned'], false);
        $this->assertSame(['assigned'], $value);
    }

    /**
     * Checks backed enum cases and backing values.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testBackedEnumAssignment(): void {
        $status = CastAndAssignStatus::Pending;
        self::castAndAssign($status, 'complete');
        $this->assertSame(CastAndAssignStatus::Complete, $status);

        self::castEnum($status, CastAndAssignStatus::Pending);
        $this->assertSame(CastAndAssignStatus::Pending, $status);
    }

    /**
     * Checks invalid backing values propagate PHP's native error.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ValueError
     */
    public function testInvalidBackedEnumValue(): void {
        $status = CastAndAssignStatus::Pending;

        $this->expectException(ValueError::class);
        self::castEnum($status, 'unknown');
    }

    /**
     * Checks pure enums match case names only.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testPureEnumAssignment(): void {
        $direction = CastAndAssignDirection::North;
        self::castAndAssign($direction, 'South');
        $this->assertSame(CastAndAssignDirection::South, $direction);

        $this->expectException(InvalidArgumentException::class);
        self::castEnum($direction, 'south');
    }

    /**
     * Checks matching objects are replaced directly.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testMatchingObjectAssignment(): void {
        $object = new stdClass();
        $replacement = new stdClass();
        self::castAndAssign($object, $replacement);
        $this->assertSame($replacement, $object);
    }

    /**
     * Checks static factory hydration takes precedence.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testFromArrayHydration(): void {
        $object = new class('original') {
            public function __construct(public string $name) {}

            /**
             * Builds a replacement from array input.
             *
             * @param array<string, string> $data Input fields.
             *
             * @return self
             */
            public static function fromArray(array $data): self {
                return new self($data['name']);
            }
        };

        self::castObject($object, ['name' => 'updated']);
        $this->assertSame('updated', $object->name);
    }

    /**
     * Checks instance hydration retains object identity.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testHydrateMethod(): void {
        $object = new class('original') {
            public function __construct(public string $name) {}

            /**
             * Updates the object from array input.
             *
             * @param array<string, string> $data Input fields.
             *
             * @return void
             */
            public function hydrate(array $data): void {
                $this->name = $data['name'];
            }
        };
        $original = $object;

        self::castAndAssign($object, ['name' => 'updated']);
        $this->assertSame($original, $object);
        $this->assertSame('updated', $object->name);
    }

    /**
     * Checks positional constructor hydration and unsupported input.
     *
     * @return void
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function testConstructorHydration(): void {
        $object = new class('original') {
            public function __construct(public string $name) {}
        };

        self::castAndAssign($object, ['updated']);
        $this->assertSame('updated', $object->name);

        $this->expectException(InvalidArgumentException::class);
        self::castObject($object, 'unsupported');
    }
}
