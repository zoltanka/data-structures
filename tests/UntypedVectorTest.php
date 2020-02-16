<?php declare(strict_types=1);

use ZFekete\DataStructures\Exception\InvalidOffsetException;
use ZFekete\DataStructures\Tests\Mock\UntypedVectorMock as UntypedVector;
use PHPUnit\Framework\TestCase;

class UntypedVectorTest extends TestCase
{
    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $testCouldRun = true;

        if (($zendAssertIni = ini_get('zend.assertions')) != '1') {
            echo sprintf("zend.assertions=%s; Unit test should be run with this settings value 1\n", $zendAssertIni);
            $testCouldRun = false;
        }

        if (($assertExceptionIni = ini_get('assert.exception')) != '1') {
            echo sprintf("assert.exception=%s; Unit test should be run with this settings value 1\n", $assertExceptionIni);
            $testCouldRun = false;
        }

        if (($assertActiveIni = ini_get('assert.active')) != '1') {
            echo sprintf("assert.active=%s; Unit test should be run with this settings value 1\n", $assertActiveIni);
            $testCouldRun = false;
        }

        if ($testCouldRun === false) {
            die();
        }
    }

    //<editor-fold desc="all()">
    /**
     * Provider for {@see UntypedVectorTest::testAll()}
     *
     * @return array
     */
    public function allTestProvider(): array
    {
        return [
            [UntypedVector::create([]), []],
            [UntypedVector::create([1, 2, 3]), [1, 2, 3]],
            [UntypedVector::create(['Foo', 'Baz']), ['Foo', 'Baz']],
            [UntypedVector::create([2 => 'Foo', 18 => 'Baz']), [2 => 'Foo', 18 => 'Baz']],
            [UntypedVector::create([2 => 'Foo', -1 => 'Baz']), [2 => 'Foo', -1 => 'Baz']],
            [UntypedVector::create([2 => 'Foo', 0 => true]), [2 => 'Foo', 0 => true]]
        ];
    }

    /**
     * @dataProvider allTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::all
     *
     * @param UntypedVector $vector
     * @param array         $expected
     *
     * @return void
     */
    public function testAll(UntypedVector $vector, array $expected): void
    {
        $this->assertSame($expected, $vector->all());
    }
    //</editor-fold>

    //<editor-fold desc="values()">
    /**
     * Provider for {@see UntypedVectorTest::testValues()}
     *
     * @return array
     */
    public function valuesTestProvider(): array
    {
        return [
            // Single types
            [UntypedVector::create(['Foo', 'Baz']), ['Foo', 'Baz']],
            [UntypedVector::create([2 => 'Foo', 19 => 'Baz']), ['Foo', 'Baz']],
            [UntypedVector::create([2 => 'Foo', -19 => 'Baz']), ['Foo', 'Baz']],

            [UntypedVector::create([0, 2]), [0, 2]],
            [UntypedVector::create([2 => 1, 19 => 2]), [1, 2]],
            [UntypedVector::create([2 => 1, -19 => 2]), [1, 2]],

            // Various types
            [UntypedVector::create(['Foo', 12]), ['Foo', 12]],
            [UntypedVector::create([2 => 'Foo', 19 => 12]), ['Foo', 12]],
            [UntypedVector::create([2 => 'Foo', -19 => 12]), ['Foo', 12]],
        ];
    }

    /**
     * @dataProvider valuesTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::values
     *
     * @param UntypedVector $source
     * @param array         $expected
     *
     * @return void
     */
    public function testValues(UntypedVector $source, array $expected): void
    {
        $this->assertSame($expected, $source->values());
    }
    //</editor-fold>

    //<editor-fold desc="keys()">
    /**
     * Provider for {@see UntypedVectorTest::testKeys()}
     *
     * @return array
     */
    public function keysTestProvider(): array
    {
        return [
            [UntypedVector::create([]), []],

            [UntypedVector::create([1, 2]), [0, 1]],
            [UntypedVector::create(['Foo', true]), [0, 1]],

            [UntypedVector::create([0 => 12, 2 => 13]), [0, 2]],
            [UntypedVector::create([0 => 12, -2 => 13]), [0, -2]],
        ];
    }

    /**
     * @dataProvider keysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::keys
     *
     * @param UntypedVector $source
     * @param array         $expected
     *
     * @return void
     */
    public function testKeys(UntypedVector $source, array $expected): void
    {
        $this->assertSame($expected, $source->keys());
    }
    //</editor-fold>

    //<editor-fold desc="set()">
    /**
     * Provider for {@see UntypedVectorTest::testSet()}
     *
     * @return array
     */
    public function setTestProvider(): array
    {
        return [
            // int type
            [UntypedVector::create([]), 1, 2, UntypedVector::create([1 => 2])],
            [UntypedVector::create([]), -1, 2, UntypedVector::create([-1 => 2])],
            [UntypedVector::create([]), 0, 2, UntypedVector::create([0 => 2])],
            [UntypedVector::create([2 => 5]), 2, -2, UntypedVector::create([2 => -2])],
            [UntypedVector::create([2 => 5]), 1, -2, UntypedVector::create([2 => 5, 1 => -2])],

            // String type
            [UntypedVector::create([]), 1, 'Foo', UntypedVector::create([1 => 'Foo'])],
            [UntypedVector::create([]), -1, 'Foo', UntypedVector::create([-1 => 'Foo'])],
            [UntypedVector::create([]), 0, 'Foo', UntypedVector::create([0 => 'Foo'])],
            [UntypedVector::create([2 => 'Foo']), 2, 'Baz', UntypedVector::create([2 => 'Baz'])],
            [UntypedVector::create([2 => 'Foo']), 1, 'Baz', UntypedVector::create([2 => 'Foo', 1 => 'Baz'])],
        ];
    }

    /**
     * @dataProvider setTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::set
     * @covers \ZFekete\DataStructures\Vector\AbstractVector::set
     *
     * Test the set method with various types.
     *
     * @param UntypedVector $a
     * @param int           $key
     * @param mixed         $value
     * @param UntypedVector $expected
     *
     * @return void
     */
    public function testSet(UntypedVector $a, int $key, $value, UntypedVector $expected): void
    {
        $this->assertSame($expected->elements, $a->set($key, $value)->elements);
    }

    /**
     * Provider for {@see UntypedVectorTest::testSetTypes()}
     *
     * @return array
     */
    public function setTestTypesProvider(): array
    {
        return [
            // Mixed types to empty Vector
            [UntypedVector::create([]), 1, '1', UntypedVector::create([1 => '1'])],
            [UntypedVector::create([]), -1, '2', UntypedVector::create([-1 => '2'])],
            [UntypedVector::create([]), 0, '2', UntypedVector::create([0 => '2'])],
            [UntypedVector::create([]), 1, 2, UntypedVector::create([1 => 2])],
            [UntypedVector::create([]), 2, true, UntypedVector::create([2 => true])],
            [UntypedVector::create([]), 3, 2.18, UntypedVector::create([3 => 2.18])],

            [UntypedVector::create([1 => true]), 1, 1, UntypedVector::create([1 => 1])],
        ];
    }

    /**
     * @dataProvider setTestTypesProvider
     *
     * Tests the "set" method with different types.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::set
     *
     * @param UntypedVector $a
     * @param int           $key
     * @param mixed         $value
     * @param UntypedVector $expected
     *
     * @return void
     */
    public function testSetTypes(UntypedVector $a, int $key, $value, UntypedVector $expected): void
    {
        $new = $a->set($key, $value);

        $this->assertSame($expected->elements, $new->elements);
    }

    /**
     * Tests, that the set method should create a new object after the call.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::set
     *
     * @return void
     */
    public function testSetReference(): void
    {
        $key   = 0;
        $value = 2;

        $source = UntypedVector::create();

        $a = $source->set($key, $value);
        $b = $source->set($key, $value);

        $this->assertNotSame($a, $b);
    }
    //</editor-fold>

    //<editor-fold desc="get()">
    /**
     * Tests the "get" method for different types.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::get
     *
     * @return void
     */
    public function testGet(): void
    {
        $source  = UntypedVector::create([1 => 'Foo', 2 => 666]);
        $default = 'Default';

        $this->assertSame(null, $source->get(0));
        $this->assertNotSame('666', $source->get(2));
        $this->assertSame(666, $source->get(2));
        $this->assertSame($default, $source->get(10, $default));
    }
    //</editor-fold>

    //<editor-fold desc="at()">
    /**
     * Tests the "at" method with existing keys.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::at
     *
     * @throws InvalidOffsetException
     *
     * @return void
     */
    public function testAtSuccessful(): void
    {
        $source = UntypedVector::create([1 => 'Foo', 2 => 666]);

        $this->assertSame(666, $source->at(2));
        $this->assertSame('Foo', $source->at(1));
        $this->assertEquals('666', $source->at(2));
        $this->assertNotSame('666', $source->at(2));
    }

    /**
     * Tests the "at" method with non-existing key.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::at
     *
     * @throws InvalidOffsetException
     *
     * @return void
     */
    public function testAtUnsuccessful(): void
    {
        $source = UntypedVector::create([1 => 'Foo', 2 => 666]);

        $this->expectException(InvalidOffsetException::class);

        $source->at(3);
    }
    //</editor-fold>

    //<editor-fold desc="only()">
    /**
     * Provider for {@see UntypedVectorTest::testOnlySuccessful()}
     *
     * @return array
     */
    public function onlySuccessfulTestProvider(): array
    {
        $keys = [1, 3];

        return [
            [UntypedVector::create([1 => 12, 3 => null]), $keys, [1 => 12, 3 => null]],
            [UntypedVector::create([1 => 12, 3 => 'Foo']), $keys, [1 => 12, 3 => 'Foo']],
            [UntypedVector::create([1 => null, 3 => null]), $keys, [1 => null, 3 => null]]
        ];
    }

    /**
     * @dataProvider onlySuccessfulTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::only
     *
     * @param UntypedVector $source
     * @param int[]         $keys
     * @param array         $expected
     */
    public function testOnlySuccessful(UntypedVector $source, array $keys, array $expected): void
    {
        $this->assertSame($expected, $source->only($keys)->elements);
    }

    /**
     * Provider for {@see UntypedVectorTest::testOnlyUnsuccessful()}
     *
     * @return array
     */
    public function onlyUnsuccessfulTestProvider(): array
    {
        $keys = [1, 2, 4];

        return [
            [UntypedVector::create(), $keys, array_fill_keys($keys, null)],
            [UntypedVector::create([1 => 12, 3 => null]), $keys, [1 => 12, 2 => null, 4 => null]]
        ];
    }

    /**
     * @dataProvider onlyUnsuccessfulTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::only()
     *
     * @param UntypedVector $source
     * @param int[]         $keys
     * @param array         $expected
     */
    public function testOnlyUnsuccessful(UntypedVector $source, array $keys, array $expected)
    {
        $this->assertSame($expected, $source->only($keys)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="except()">
    /**
     * Provider for {@see UntypedVectorTest::testExcept()}
     *
     * @return array
     */
    public function exceptTestProvider(): array
    {
        $keys = [1, 3];

        return [
            [UntypedVector::create(['Foo', 'Baz', true]), $keys, [0 => 'Foo', 2 => true]],
            [UntypedVector::create([1 => 'Foo', 2 => 'Baz', 3 => true]), $keys, [2 => 'Baz']],
            [UntypedVector::create([2 => 'Foo', 4 => 'Baz', 0 => true]), $keys, [2 => 'Foo', 4 => 'Baz', 0 => true]],
        ];
    }

    /**
     * Tests the "except" method.
     *
     * @dataProvider exceptTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::except
     *
     * @param UntypedVector $source
     * @param int[]         $keys
     * @param array         $expected
     *
     * @return void
     */
    public function testExcept(UntypedVector $source, array $keys, array $expected): void
    {
        $this->assertSame($expected, $source->except($keys)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="firstKey()">
    /**
     * Provider for {@see UntypedVectorTest::testFirstKey()}
     *
     * @return array
     */
    public function firstKeyProvider(): array
    {
        return [
            [UntypedVector::create([1 => 'Foo', 2 => 666, -5 => 'Baz']), 1],
            [UntypedVector::create([-5 => 'Baz', 1 => 'Foo', 2 => 666]), -5]
        ];
    }

    /**
     * Tests "firstKey" method.
     *
     * @dataProvider firstKeyProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::firstKey
     *
     * @param UntypedVector $vector
     * @param mixed         $expected
     *
     * @return void
     */
    public function testFirstKey(UntypedVector $vector, int $expected): void
    {
        $this->assertSame($expected, $vector->firstKey());
    }
    //</editor-fold>

    //<editor-fold desc="firstValue()">
    /**
     * Provider for {@see UntypedVectorTest::testFirstValue()}
     *
     * @return array
     */
    public function firstValueTestProvider(): array
    {
        return [
            [UntypedVector::create([1 => 'Foo', 3 => 666]), 'Foo'],
            [UntypedVector::create([3 => 666, 1 => 'Foo']), 666],
            [UntypedVector::create(), null]
        ];
    }

    /**
     * Tests "firstValue" method without testing the default value parameter.
     *
     * @dataProvider firstValueTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::firstValue
     *
     * @param UntypedVector $vector
     * @param mixed         $expected
     *
     * @return void
     */
    public function testFirstValue(UntypedVector $vector, $expected): void
    {
        $this->assertSame($expected, $vector->firstValue());
    }

    /**
     * Tests "firstValue" method with default value parameter.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::firstValue
     *
     * @return void
     */
    public function testFirstValueWithDefault(): void
    {
        $source  = UntypedVector::create([]);
        $default = 12;

        $this->assertSame($default, $source->firstValue($default));
    }
    //</editor-fold>

    //<editor-fold desc="lastKey()">
    /**
     * Provider for {@see UntypedVectorTest::testLastKey()}
     *
     * @return array
     */
    public function lastKeyProvider(): array
    {
        return [
            [UntypedVector::create([1 => 'Foo', 2 => 666, -5 => 'Baz']), -5],
            [UntypedVector::create([-5 => 'Baz', 1 => 'Foo', 2 => 666]), 2]
        ];
    }

    /**
     * Tests "lastKey" method.
     *
     * @dataProvider lastKeyProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::lastKey
     *
     * @param UntypedVector $vector
     * @param int           $expected
     *
     * @return void
     */
    public function testLastKey(UntypedVector $vector, int $expected): void
    {
        $this->assertSame($expected, $vector->lastKey());
    }
    //</editor-fold>

    //<editor-fold desc="lastValue()">
    /**
     * Provider for {@see UntypedVectorTest::testLastValue()}
     *
     * @return array
     */
    public function lastValueTestProvider(): array
    {
        return [
            [UntypedVector::create([1 => 'Foo', 3 => 666]), 666],
            [UntypedVector::create([3 => 666, 1 => 'Foo']), 'Foo'],
            [UntypedVector::create(), null]
        ];
    }

    /**
     * Tests "lastValue" method without default parameter.
     *
     * @dataProvider lastValueTestProvider
     *
     * @param UntypedVector $vector
     * @param mixed         $expected
     *
     * @return void
     */
    public function testLastValue(UntypedVector $vector, $expected): void
    {
        $this->assertSame($expected, $vector->lastValue());
    }

    /**
     * Tests "lastValue" method with default value parameter.
     *
     * @return void
     */
    public function testLastValueWithDefault(): void
    {
        $source  = UntypedVector::create([]);
        $default = 12;

        $this->assertSame($default, $source->lastValue($default));
        $this->assertSame(null, $source->lastValue());
    }
    //</editor-fold>

    //<editor-fold desc="count()">
    /**
     * Provider for {@see UntypedVectorTest::testCount()}
     *
     * @return array
     */
    public function countTestProvider(): array
    {
        return [
            [UntypedVector::create([]), 0],
            [UntypedVector::create([1, 2, 3]), 3],
            [UntypedVector::create(['Foo', null, 12]), 3],
            [UntypedVector::create([null, null]), 2],
        ];
    }

    /**
     * Test "count" method.
     *
     * @dataProvider countTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::count
     *
     * @param UntypedVector $source
     * @param int           $expected
     *
     * @return void
     */
    public function testCount(UntypedVector $source, int $expected): void
    {
        $this->assertEquals($expected, $source->count());
    }
    //</editor-fold>

    //<editor-fold desc="isEmpty()">
    /**
     * Tests "isEmpty" method.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::isEmpty
     *
     * @return void
     */
    public function testIsEmpty(): void
    {
        $this->assertTrue(UntypedVector::create()->isEmpty());
        $this->assertTrue(UntypedVector::create([])->isEmpty());

        $this->assertFalse(UntypedVector::create([null])->isEmpty());
        $this->assertFalse(UntypedVector::create([false])->isEmpty());
        $this->assertFalse(UntypedVector::create([1])->isEmpty());
        $this->assertFalse(UntypedVector::create([[]])->isEmpty());
    }
    //</editor-fold>

    //<editor-fold desc="isNotEmpty()">
    /**
     * Tests "isNotEmpty" method.
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::isNotEmpty
     *
     * @return void
     */
    public function testIsNotEmpty(): void
    {
        $this->assertFalse(UntypedVector::create()->isNotEmpty());
        $this->assertFalse(UntypedVector::create([])->isNotEmpty());

        $this->assertTrue(UntypedVector::create([null])->isNotEmpty());
        $this->assertTrue(UntypedVector::create([false])->isNotEmpty());
        $this->assertTrue(UntypedVector::create([1])->isNotEmpty());
        $this->assertTrue(UntypedVector::create([[]])->isNotEmpty());
    }
    //</editor-fold>

    //<editor-fold desc="test()">
    /**
     * Provider for {@see UntypedVectorTest::testHas()}
     *
     * @return array
     */
    public function hasTestProvider(): array
    {
        return [
            [UntypedVector::create(),                      1, false],
            [UntypedVector::create(['Foo']),               1, false],
            [UntypedVector::create(['Foo', 'Baz']),        1, true],
            [UntypedVector::create(['Foo', null, 'Baz']),  1, true],
            [UntypedVector::create(['Foo', false, 'Baz']), 1, true],
        ];
    }

    /**
     * Tests "has" method.
     *
     * @dataProvider hasTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::has
     *
     * @param UntypedVector $source
     * @param int           $key
     * @param bool          $expected
     *
     * @return void
     */
    public function testHas(UntypedVector $source, int $key, bool $expected): void
    {
        $this->assertSame($expected, $source->has($key));
    }
    //</editor-fold>

    //<editor-fold desc="contains()">
    /**
     * Provider for {@see UntypedVectorTest::testContainUnstrict()}
     *
     * @return array
     */
    public function containsUnstrictTestProvider(): array
    {
        $stringNeedle    = 'Foo';
        $stringIntNeedle = '1';

        $intNeedle = 2;

        return [
            [UntypedVector::create(), $intNeedle, false],
            [UntypedVector::create([1, 3, 5]), $intNeedle, false],
            [UntypedVector::create([2, 4, 6]), $intNeedle, true],
            [UntypedVector::create([true, false]), $intNeedle, true],
            [UntypedVector::create([false, false]), $intNeedle, false],

            [UntypedVector::create(), $stringNeedle, false],
            [UntypedVector::create([1, 3, 5]), $stringNeedle, false],
            [UntypedVector::create([2, 4, 6]), $stringNeedle, false],
            [UntypedVector::create([true, false]), $stringNeedle, true],
            [UntypedVector::create([false, false]), $stringNeedle, false],

            [UntypedVector::create(), $stringIntNeedle, false],
            [UntypedVector::create([1, 3, 5]), $stringIntNeedle, true],
            [UntypedVector::create([2, 4, 6]), $stringIntNeedle, false],
            [UntypedVector::create([true, false]), $stringIntNeedle, true],
            [UntypedVector::create([false, false]), $stringIntNeedle, false],
        ];
    }

    /**
     * Test "contains" method with strict flag passed as false.
     *
     * @dataProvider containsUnstrictTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::contains
     *
     * @param UntypedVector $source
     * @param mixed         $value
     * @param bool          $expected
     *
     * @return void
     */
    public function testContainUnstrict(UntypedVector $source, $value, bool $expected): void
    {
        $this->assertSame($expected, $source->contains($value, false));
    }

    /**
     * Provider for {@see UntypedVectorTest::testContainStrict()}
     *
     * @return array
     */
    public function containsStrictTestProvider(): array
    {
        $stringNeedle    = 'Foo';
        $stringIntNeedle = '1';

        $intNeedle = 2;

        return [
            [UntypedVector::create(), $intNeedle, false],
            [UntypedVector::create([1, 3, 5]), $intNeedle, false],
            [UntypedVector::create([2, 4, 6]), $intNeedle, true],
            [UntypedVector::create([true, false]), $intNeedle, false],
            [UntypedVector::create([false, false]), $intNeedle, false],

            [UntypedVector::create(), $stringNeedle, false],
            [UntypedVector::create([1, 3, 5]), $stringNeedle, false],
            [UntypedVector::create([2, 4, 6]), $stringNeedle, false],
            [UntypedVector::create([true, false]), $stringNeedle, false],
            [UntypedVector::create([false, false]), $stringNeedle, false],

            [UntypedVector::create(), $stringIntNeedle, false],
            [UntypedVector::create([1, 3, 5]), $stringIntNeedle, false],
            [UntypedVector::create([2, 4, 6]), $stringIntNeedle, false],
            [UntypedVector::create([true, false]), $stringIntNeedle, false],
            [UntypedVector::create([false, false]), $stringIntNeedle, false],
        ];
    }

    /**
     * Test "contains" method with strict flag passed as true.
     *
     * @dataProvider containsStrictTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::contains
     *
     * @param UntypedVector $source
     * @param mixed         $value
     * @param bool          $expected
     *
     * @return void
     */
    public function testContainStrict(UntypedVector $source, $value, bool $expected): void
    {
        $this->assertSame($expected, $source->contains($value, true));
    }
    //</editor-fold>

    //<editor-fold desc="every()">
    /**
     * Provider for {@see UntypedVectorTest::testEvery()}
     *
     * @return array
     */
    public function everyTestProvider(): array
    {
        $intTester = static function($val) : bool {
            return is_int($val);
        };

        $gt5Tester = static function($val) : bool {
            return $val > 5;
        };

        $stringTester = static function($val) : bool {
            return is_string($val);
        };

        return [
            [UntypedVector::create(), $intTester, false],
            [UntypedVector::create(), $stringTester, false],
            [UntypedVector::create(), $gt5Tester, false],

            [UntypedVector::create([7, 8, null]), $intTester, false],
            [UntypedVector::create([1, 'Foo', 'Baz']), $stringTester, false],
            [UntypedVector::create([0, 1, 3]), $gt5Tester, false],

            [UntypedVector::create([1, 2, 4]), $intTester, true],
            [UntypedVector::create(['Foo', 'Baz']), $stringTester, true],
            [UntypedVector::create([7, 8, 9]), $gt5Tester, true],
        ];
    }

    /**
     * @dataProvider everyTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::every
     *
     * @param UntypedVector $source
     * @param Closure       $tester
     * @param bool          $expected
     *
     * @return void
     */
    public function testEvery(UntypedVector $source, Closure $tester, bool $expected): void
    {
        $this->assertSame($expected, $source->every($tester));
    }
    //</editor-fold>

    //<editor-fold desc="some()">
    /**
     * Provider for {@see UntypedVectorTest::testSome()}
     *
     * @return array
     */
    public function someTestProvider(): array
    {
        $intTester = static function ($val): bool {
            return is_int($val);
        };

        $gt5Tester = static function ($val): bool {
            return $val > 5;
        };

        $stringTester = static function ($val): bool {
            return is_string($val);
        };

        return [
            [UntypedVector::create(), $intTester, false],
            [UntypedVector::create(), $stringTester, false],
            [UntypedVector::create(), $gt5Tester, false],

            [UntypedVector::create(['true', [1], null]), $intTester, false],
            [UntypedVector::create([1, true, 12.32]), $stringTester, false],
            [UntypedVector::create([0, 1, 3]), $gt5Tester, false],

            [UntypedVector::create([7, 8, null]), $intTester, true],
            [UntypedVector::create([1, 'Foo', 'Baz']), $stringTester, true],
            [UntypedVector::create([0, 1, 7]), $gt5Tester, true],

            [UntypedVector::create([1, 2, 4]), $intTester, true],
            [UntypedVector::create(['Foo', 'Baz']), $stringTester, true],
            [UntypedVector::create([7, 8, 9]), $gt5Tester, true],
        ];
    }

    /**
     * @dataProvider someTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::some
     *
     * @param UntypedVector $source
     * @param Closure       $tester
     * @param bool          $expected
     *
     * @return void
     */
    public function testSome(UntypedVector $source, Closure $tester, bool $expected): void
    {
        $this->assertSame($expected, $source->some($tester));
    }
    //</editor-fold>

    //<editor-fold desc="map()">
    /**
     * Provider for {@see UntypedVectorTest::testMap()}
     *
     * @return array
     */
    public function mapTestProvider(): array
    {
        $doingNothingCb = function(array $x) : array {
            return $x;
        };

        $squareCb = function(int $x) : int {
            return $x * $x;
        };

        $appendCb = function(string $x) : string {
            return "Prefix " . $x;
        };

        return [
            [UntypedVector::create([]), $doingNothingCb, []],
            [UntypedVector::create([1, 2, 3, 4]), $squareCb, [1, 4, 9, 16]],
            [UntypedVector::create(['Foo', 'Baz']), $appendCb, ['Prefix Foo', 'Prefix Baz']]
        ];
    }

    /**
     * @dataProvider mapTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::map
     *
     * @param UntypedVector $source
     * @param Closure       $callback
     * @param array         $expected
     *
     * @return void
     */
    public function testMap(UntypedVector $source, Closure $callback, array $expected): void
    {
        $sourceBackup = $source;

        $result = $source->map($callback);

        $this->assertSame($expected, $result->elements);
        $this->assertSame($source->elements, $sourceBackup->elements);
    }
    //</editor-fold>

    //<editor-fold desc="merge()">
    /**
     * Provider for {@see UntypedVectorTest::testMerge()}
     *
     * @return array
     */
    public function mergeTestProvider(): array
    {
        return [
            [UntypedVector::create([]),      UntypedVector::create([]),  [],  []],
            [UntypedVector::create([1]),     UntypedVector::create([2]), [1, 2], [2, 1]],
            [UntypedVector::create(['Foo']), UntypedVector::create([1]), ['Foo', 1], [1, 'Foo']],

            [
                UntypedVector::create([0 => 'Foo', 2 => 'Baz']),
                UntypedVector::create([0 => 1, 1 => 2]),
                [0 => 'Foo', 1 => 'Baz', 2 => 1, 3 => 2],
                [0 => 1, 1 => 2, 2 => 'Foo', 3 => 'Baz']
            ],
        ];
    }

    /**
     * @dataProvider mergeTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::merge
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expectedBtoA
     * @param array         $expectedAtoB
     *
     * @return void
     */
    public function testMerge(UntypedVector $a, UntypedVector $b, array $expectedBtoA, array $expectedAtoB): void
    {
        $aSave = $a;
        $bSave = $b;

        $this->assertSame($expectedBtoA, $a->merge($b)->elements);
        $this->assertSame($expectedAtoB, $b->merge($a)->elements);

        $this->assertSame($a->elements, $aSave->elements);
        $this->assertSame($b->elements, $bSave->elements);
    }
    //</editor-fold>

    //<editor-fold desc="filter()">
    /**
     * Provider for {@see UntypedVectorTest::testFilter()}
     *
     * @return array
     */
    public function filterTestProvider(): array
    {
        $intFilter = static function($x) : bool {
            return is_int($x);
        };
        $gt5Filter = static function($x) : bool {
            return $x > 5;
        };

        return [
            [UntypedVector::create([]),                   [],                null],
            [UntypedVector::create([6, 'Foo', 'Baz', 0]), [6, 'Foo', 'Baz'], null],
            [UntypedVector::create([6, 'Foo', 'Baz', 0]), [0 => 6, 3 => 0],  $intFilter],
            [UntypedVector::create([6, 'Foo', 'Baz', 0]), [6], $gt5Filter],
        ];
    }


    /**
     * @dataProvider filterTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::filter
     *
     * @param UntypedVector $source
     * @param array         $expected
     * @param Closure|null  $callback
     *
     * @return void
     */
    public function testFilter(UntypedVector $source, array $expected, ?Closure $callback = null): void
    {
        $save = $source;

        $this->assertSame($expected, $source->filter($callback)->elements);
        $this->assertSame($save->elements, $source->elements);
    }
    //</editor-fold>

    //<editor-fold desc="diff()">
    /**
     * Provider for {@see UntypedVectorTest::testDiff()}
     *
     * @return array
     */
    public function diffTestProvider(): array
    {
        return [
            [UntypedVector::create([]), [], []],
            [UntypedVector::create([]), [1], []],
            [UntypedVector::create([1]), [1], []],
            [UntypedVector::create([1, 3, 5]), [1, 2, 3], [2 => 5]],
            [UntypedVector::create(['Foo', 'Baz', 1, 2]), ['Baz', 2], [0 => 'Foo', 2 => 1]],
        ];
    }

    /**
     * @dataProvider diffTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::diff
     *
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     *
     * @return void
     */
    public function testDiff(UntypedVector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->diff($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="diffVector()">
    /**
     * Provider for {@see UntypedVectorTest::testDiffVector()}
     *
     * @return array
     */
    public function diffVectorTestProvider(): array
    {
        return [
            [UntypedVector::create(), UntypedVector::create([]), []],
            [UntypedVector::create([]), UntypedVector::create([]), []],
            [UntypedVector::create([]), UntypedVector::create([1]), []],
            [UntypedVector::create([1]), UntypedVector::create([1]), []],
            [UntypedVector::create([1, 3, 5]), UntypedVector::create([1, 2, 3]), [2 => 5]],
            [UntypedVector::create(['Foo', 'Baz', 1, 2]), UntypedVector::create(['Baz', 2]), [0 => 'Foo', 2 => 1]],
        ];
    }

    /**
     * @dataProvider diffVectorTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::diffVector
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expected
     *
     * @return void
     */
    public function testDiffVector(UntypedVector $a, UntypedVector $b, array $expected): void
    {
        $this->assertSame($expected, $a->diffVector($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="diffKeys()">
    /**
     * Provider for {@see UntypedVectorTest::testDiffKeys()}
     *
     * @return array
     */
    public function diffKeysTestProvider(): array
    {
        return [
            [UntypedVector::create(),   [], []],
            [UntypedVector::create([]), [], []],
            [UntypedVector::create([]), [1, 2], []],
            [UntypedVector::create([3, 4]), [], [3, 4]],
            [UntypedVector::create([0 => 3, 5 => 4]), [], [0 => 3, 5 => 4]],
            [UntypedVector::create([0 => 'Foo', 5 => 'Baz']), [0 => 2, 2 => 1], [5 => 'Baz']],
        ];
    }

    /**
     * @dataProvider diffKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::diffKeys
     *
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     *
     * @return void
     */
    public function testDiffKeys(UntypedVector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->diffKeys($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="diffVectorKeys()">
    /**
     * Provider for {@see UntypedVectorTest::testDiffVectorKeys()}
     *
     * @return array
     */
    public function diffVectorKeysTestProvider(): array
    {
        return [
            [UntypedVector::create(),                         UntypedVector::create([]),               []],
            [UntypedVector::create([]),                       UntypedVector::create([]),               []],
            [UntypedVector::create([]),                       UntypedVector::create([1, 2]),           []],
            [UntypedVector::create([3, 4]),                   UntypedVector::create([]),               [3, 4]],
            [UntypedVector::create([0 => 3, 5 => 4]),         UntypedVector::create([]),               [0 => 3, 5 => 4]],
            [UntypedVector::create([0 => 'Foo', 5 => 'Baz']), UntypedVector::create([0 => 2, 2 => 1]), [5 => 'Baz']],
        ];
    }

    /**
     * @dataProvider diffVectorKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::diffVectorKeys
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expected
     *
     * @return void
     */
    public function testDiffVectorKeys(UntypedVector $a, UntypedVector $b, array $expected): void
    {
        $this->assertSame($expected, $a->diffVectorKeys($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="intersect()">
    /**
     * Provider for {@see UntypedVectorTest::testIntersect()}
     *
     * @return array
     */
    public function intersectTestProvider(): array
    {
        return [
            [UntypedVector::create(),                     [],         []],
            [UntypedVector::create([]),                   [],         []],
            [UntypedVector::create([]),                   [1],        []],
            [UntypedVector::create([2]),                  [],         []],
            [UntypedVector::create([2]),                  [2],        [2]],
            [UntypedVector::create([1, 3, 4, 5]),         [1, 2, 3],  [0 => 1, 1 => 3]],
            [UntypedVector::create([1, 4, 3, 5]),         [1, 2, 3],  [0 => 1, 2 => 3]],
            [UntypedVector::create(['Foo', 'Baz', 1, 2]), ['Baz', 1], [1 => 'Baz', 2 => 1]]
        ];
    }

    /**
     * @dataProvider intersectTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::intersect
     *
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     *
     * @return void
     */
    public function testIntersect(UntypedVector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersect($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="intersectVector()">
    /**
     * Provider for {@see UntypedVectorTest::testIntersectVector()}
     *
     * @return array
     */
    public function intersectVectorTestProvider(): array
    {
        return [
            [UntypedVector::create(),                     UntypedVector::create([]),         []],
            [UntypedVector::create([]),                   UntypedVector::create([]),         []],
            [UntypedVector::create([]),                   UntypedVector::create([1]),        []],
            [UntypedVector::create([2]),                  UntypedVector::create([]),         []],
            [UntypedVector::create([2]),                  UntypedVector::create([2]),        [2]],
            [UntypedVector::create([1, 3, 4, 5]),         UntypedVector::create([1, 2, 3]),  [0 => 1, 1 => 3]],
            [UntypedVector::create([1, 4, 3, 5]),         UntypedVector::create([1, 2, 3]),  [0 => 1, 2 => 3]],
            [UntypedVector::create(['Foo', 'Baz', 1, 2]), UntypedVector::create(['Baz', 1]), [1 => 'Baz', 2 => 1]]
        ];
    }

    /**
     * @dataProvider intersectVectorTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::intersectVector
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expected
     *
     * @return void
     */
    public function testIntersectVector(UntypedVector $a, UntypedVector $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersectVector($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="intersectKeys">
    /**
     * Provider for {@see UntypedVectorTest::testIntersectKeys()}
     *
     * @return array
     */
    public function intersectKeysTestProvider(): array
    {
        return [
            [UntypedVector::create(),                 [],     []],
            [UntypedVector::create([]),               [],     []],
            [UntypedVector::create([2 => 1, 4 => 2]), [],     []],
            [UntypedVector::create([]),               [1, 2], []],

            [UntypedVector::create([0 => 'Foo', 3 => 1]), [0 => 1, 2 => 2],             [0 => 'Foo']],
            [UntypedVector::create([2 => 'Baz', 5 => 3]), [3 => 1, 4 => 2, 5 => 'Baz'], [5 => 3]],
        ];
    }

    /**
     * @dataProvider intersectKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::intersectKeys
     *
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     *
     * @return void
     */
    public function testIntersectKeys(UntypedVector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersectKeys($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="intersectVectorKeys">
    /**
     * Provider for {@see UntypedVectorTest::testIntersectVectorKeys()}
     *
     * @return array
     */
    public function intersectVectorKeysTestProvider(): array
    {
        return [
            [UntypedVector::create(),                 UntypedVector::create([]),     []],
            [UntypedVector::create([]),               UntypedVector::create([]),     []],
            [UntypedVector::create([2 => 1, 4 => 2]), UntypedVector::create([]),     []],
            [UntypedVector::create([]),               UntypedVector::create([1, 2]), []],

            [UntypedVector::create([0 => 'Foo', 3 => 1]), UntypedVector::create([0 => 1, 2 => 2]),             [0 => 'Foo']],
            [UntypedVector::create([2 => 'Baz', 5 => 3]), UntypedVector::create([3 => 1, 4 => 2, 5 => 'Baz']), [5 => 3]],
        ];
    }

    /**
     * @dataProvider intersectVectorKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::intersectVectorKeys
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expected
     *
     * @return void
     */
    public function testIntersectVectorKeys(UntypedVector $a, UntypedVector $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersectVectorKeys($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="replace">
    /**
     * Provider for {@see UntypedVectorTest::testReplace()}
     *
     * @return array
     */
    public function replaceTestProvider(): array
    {
        return [
            [UntypedVector::create(), [], []],
            [UntypedVector::create([]), [], []],
            [UntypedVector::create([1 => 'Foo']), [], [1 => 'Foo']],
            [UntypedVector::create([1 => 'Foo']), [2 => 'Baz'], [1 => 'Foo', 2 => 'Baz']],
            [UntypedVector::create([1 => 'Foo']), [1 => 'Baz'], [1 => 'Baz']],
            [UntypedVector::create([1 => true]), [1 => 12.12], [1 => 12.12]]
        ];
    }

    /**
     * @dataProvider replaceTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::replace
     *
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     *
     * @return void
     */
    public function testReplace(UntypedVector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->replace($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="replaceVector">
    /**
     * Provider for {@see UntypedVectorTest::testReplaceVector()}
     *
     * @return array
     */
    public function replaceVectorTestProvider(): array
    {
        return [
            [UntypedVector::create(), UntypedVector::create([]), []],
            [UntypedVector::create([]), UntypedVector::create([]), []],
            [UntypedVector::create([1 => 'Foo']), UntypedVector::create([]), [1 => 'Foo']],
            [UntypedVector::create([1 => 'Foo']), UntypedVector::create([2 => 'Baz']), [1 => 'Foo', 2 => 'Baz']],
            [UntypedVector::create([1 => 'Foo']), UntypedVector::create([1 => 'Baz']), [1 => 'Baz']],
            [UntypedVector::create([1 => true]), UntypedVector::create([1 => 12.12]), [1 => 12.12]]
        ];
    }

    /**
     * @dataProvider replaceVectorTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::replaceVector
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array $expected
     *
     * @return void
     */
    public function testReplaceVector(UntypedVector $a, UntypedVector $b, array $expected): void
    {
        $this->assertSame($expected, $a->replaceVector($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="shift">
    /**
     * Provider for {@see UntypedVectorTest::testShift()}
     *
     * @return array
     */
    public function shiftTestProvider(): array
    {
        return [
            [UntypedVector::create(), UntypedVector::create([]), null],
            [UntypedVector::create([]), UntypedVector::create([]), null],
            [UntypedVector::create([1 => 'Foo']), UntypedVector::create([]), 'Foo'],
            [UntypedVector::create(['Foo']), UntypedVector::create([]), 'Foo'],
            [UntypedVector::create([0 => 'Foo', 1 => 'Baz']), UntypedVector::create([0 => 'Baz']), 'Foo'],
            [UntypedVector::create([1 => 'Foo', 0 => 'Baz']), UntypedVector::create([0 => 'Baz']), 'Foo'],
        ];
    }

    /**
     * @dataProvider shiftTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::shift
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param mixed         $value
     *
     * @return void
     */
    public function testShift(UntypedVector $a, UntypedVector $b, $value): void
    {
        $retrieved = $a->shift();

        $this->assertSame($value, $retrieved);
        $this->assertSame($b->elements, $a->elements);
    }
    //</editor-fold>

    //<editor-fold desc="unshift">
    /**
     * Provider for {@see UntypedVectorTest::testUnshift()}
     *
     * @return array
     */
    public function unshiftTestProvider(): array
    {
        return [
            [UntypedVector::create([-2 => 1]), 2, UntypedVector::create([-3 => 2, -2 => 1])],
            [UntypedVector::create([0 => 1]), 2, UntypedVector::create([-1 => 2, 0 => 1])],
            [UntypedVector::create([0 => 1]), 1, UntypedVector::create([-1 => 1, 0 => 1])],
            [UntypedVector::create([2 => 4]), 2, UntypedVector::create([1 => 2, 2 => 4])],
            [UntypedVector::create([5 => 12]), 9, UntypedVector::create([4 => 9, 5 => 12])],
            [UntypedVector::create([9 => 12]), [1, 2, 3, 4], UntypedVector::create([5 => 4, 6 => 3, 7 => 2, 8 => 1, 9 => 12])],
            [UntypedVector::create([-3 => 5]), [1, 2, 3, 4], UntypedVector::create([-7 => 4, -6 => 3, -5 => 2, -4 => 1, -3 => 5])],
            [UntypedVector::create([]), [1, 2, 3, 4], UntypedVector::create([4, 3, 2, 1])]
        ];
    }

    /**
     * @dataProvider unshiftTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::unshift
     *
     * @param UntypedVector $a
     * @param int|int[]     $b
     * @param UntypedVector $expected
     *
     * @return void
     */
    public function testUnshift(UntypedVector $a, $b, UntypedVector $expected): void
    {
        if (is_array($b)) {
            $this->assertEquals($expected->elements, $a->unshift(... $b)->elements);
        } else {
            $this->assertEquals($expected->elements, $a->unshift($b)->elements);
        }
    }
    //</editor-fold>

    //<editor-fold desc="push">
    /**
     * Provider for {@see UntypedVectorTest::testPush()}
     *
     * @return array
     */
    public function pushTestProvider()
    {
        return [
            [UntypedVector::create([-1 => 1]), 2, UntypedVector::create([-1 => 1, 0 => 2])],
            [UntypedVector::create([0 => 1]), 2, UntypedVector::create([0 => 1, 1 => 2])],
            [UntypedVector::create([0 => 1]), 1, UntypedVector::create([0 => 1, 1 => 1])],
            [UntypedVector::create([2 => 4]), 2, UntypedVector::create([2 => 4, 3 => 2])],
            [UntypedVector::create([5 => 12]), 9, UntypedVector::create([5 => 12, 6 => 9])]
        ];
    }

    /**
     * @dataProvider pushTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::push
     *
     * @param UntypedVector $a
     * @param int           $b
     * @param UntypedVector $expected
     *
     * @return void
     */
    public function testPush(UntypedVector $a, int $b, UntypedVector $expected): void
    {
        $this->assertEquals($expected->elements, $a->push($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="pop">
    /**
     * Provider for {@see UntypedVectorTest::testPop()}
     *
     * @return array
     */
    public function popTestProvider(): array
    {
        return [
            [UntypedVector::create(), UntypedVector::create([]), null],
            [UntypedVector::create([]), UntypedVector::create([]), null],
            [UntypedVector::create([1 => 'Foo']), UntypedVector::create([]), 'Foo'],
            [UntypedVector::create([1 => 'Foo', 2 => 'Baz']), UntypedVector::create([1 => 'Foo']), 'Baz'],
        ];
    }

    /**
     * @dataProvider popTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::pop
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param mixed         $value
     *
     * @return void
     */
    public function testPop(UntypedVector $a, UntypedVector $b, $value): void
    {
        $retrieved = $a->pop();

        $this->assertSame($retrieved, $value);
        $this->assertSame($b->elements, $a->elements);
    }
    //</editor-fold>

    //<editor-fold desc="clear">
    /**
     * Provider for {@see UntypedVectorTest::testClear()}
     *
     * @return array
     */
    public function clearTestProvider(): array
    {
        return [
            [UntypedVector::create(), []],
            [UntypedVector::create([]), []],
            [UntypedVector::create([]), []],
        ];
    }

    /**
     * @dataProvider clearTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::clear
     *
     * @param UntypedVector $a
     * @param array $expected
     *
     * @return void
     */
    public function testClear(UntypedVector $a, array $expected): void
    {
        $a->clear();

        $this->assertSame($expected, $a->elements);
    }
    //</editor-fold>

    //<editor-fold desc="jsonSerialize">
    /**
     * Provider for {@see UntypedVectorTest::testJsonSerialize()} and {@see UntypedVectorTest::testNativeJsonSerialize()}
     *
     * @return array
     */
    public function jsonSerializeTestProvider(): array
    {
        $caseA = [true];
        $caseB = [1, 3, 4, 5];
        $caseC = [1, 'Foo', 2.22];
        $caseD = [0 => 1, 2 => 'Foo', 3 => 2.22];

        return [
            [UntypedVector::create(), []],
            [UntypedVector::create([]), []],
            [UntypedVector::create($caseA), $caseA],
            [UntypedVector::create($caseB), $caseB],
            [UntypedVector::create($caseC), $caseC],
            [UntypedVector::create($caseD), $caseD],
        ];
    }

    /**
     * @dataProvider jsonSerializeTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::jsonSerialize
     *
     * @param UntypedVector $a
     * @param array         $expected
     *
     * @return void
     */
    public function testJsonSerialize(UntypedVector $a, array $expected): void
    {
        $this->assertSame($expected, $a->jsonSerialize());
    }

    /**
     * @dataProvider jsonSerializeTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::jsonSerialize
     *
     * @param UntypedVector $a
     * @param array         $expected
     *
     * @return void
     */
    public function testNativeJsonSerialize(UntypedVector $a, array $expected): void
    {
        $this->assertSame(json_encode($expected), json_encode($a));
    }
    //</editor-fold>

    //<editor-fold desc="clone">
    /**
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::clone()
     *
     * @return void
     */
    public function testCloneWithEmpty(): void
    {
        $a = UntypedVector::create();
        $b = $a->clone();

        $this->assertSame($a->elements, $b->elements);
        $this->assertNotSame($b, $a);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::clone()
     *
     * @return void
     */
    public function testCloneWithScalars(): void
    {
        $a = UntypedVector::create([1, 'Foo', true, 12.22]);
        $b = $a->clone();

        $this->assertSame($a->elements, $b->elements);
        $this->assertNotSame($b, $a);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::clone()
     *
     * @throws Exception
     *
     * @return void
     */
    public function testCloneWithReference(): void
    {
        $date = new DateTime();

        $a = UntypedVector::create([$date]);
        $b = $a->clone();

        $this->assertSame($a->elements, $b->elements);
        $this->assertNotSame($b, $a);

        $this->assertSame($a->firstValue(), $b->firstValue());
    }
    //</editor-fold>

    //<editor-fold desc="getIterator">
    /**
     * @covers \ZFekete\DataStructures\Vector\UntypedVector::getIterator()
     *
     * @return void
     */
    public function testIfIterable(): void
    {
        $elements = [1, true, 'Foo'];

        $a = new UntypedVector($elements);

        $new = [];
        foreach ($a as $item) {
            $new[] = $item;
        }

        $this->assertSame($new, $a->elements);
    }
    //</editor-fold>
}
