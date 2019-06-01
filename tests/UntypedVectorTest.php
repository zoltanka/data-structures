<?php declare(strict_types=1);

use ZFekete\DataStructures\Tests\Mock\UntypedVectorMock as UntypedVector;
use PHPUnit\Framework\TestCase;

class UntypedVectorTest extends TestCase
{
    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $testCouldRun = true;

        if (($zendAssertIni = ini_get('zend.assertions')) != '1') {
            echo \sprintf("zend.assertions=%s; Unit test should be run with this settings value 1\n", $zendAssertIni);
            $testCouldRun = false;
        }

        if (($assertExceptionIni = ini_get('assert.exception')) != '1') {
            echo \sprintf("assert.exception=%s; Unit test should be run with this settings value 1\n", $assertExceptionIni);
            $testCouldRun = false;
        }

        if (($assertActiveIni = ini_get('assert.active')) != '1') {
            echo \sprintf("assert.active=%s; Unit test should be run with this settings value 1\n", $assertActiveIni);
            $testCouldRun = false;
        }

        if ($testCouldRun === false) {
            die();
        }
    }


    //<editor-fold desc="all()">
    public function allTestProvider()
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
     * @param UntypedVector $vector
     * @param array         $expected
     */
    public function testAll(UntypedVector $vector, array $expected)
    {
        $this->assertSame($expected, $vector->all());
    }
    //</editor-fold>


    //<editor-fold desc="values()">
    public function valuesTestProvider()
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
     * @param UntypedVector $source
     * @param array         $expected
     */
    public function testValues(UntypedVector $source, array $expected)
    {
        $this->assertSame($expected, $source->values());
    }
    //</editor-fold>


    //<editor-fold desc="keys()">
    public function keysTestProvider()
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
     * @param UntypedVector $source
     * @param array         $expected
     */
    public function testKeys(UntypedVector $source, array $expected)
    {
        $this->assertSame($expected, $source->keys());
    }
    //</editor-fold>


    //<editor-fold desc="set()">
    public function setTestProvider()
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
     * @param UntypedVector $a
     * @param int           $key
     * @param mixed         $value
     * @param UntypedVector $expected
     */
    public function testSet(UntypedVector $a, int $key, $value, UntypedVector $expected)
    {
        $this->assertSame($expected->elements, $a->set($key, $value)->elements);
    }


    public function setTestTypesProvider()
    {
        return [
            // Mixed type
            [UntypedVector::create([]), 1, '1', UntypedVector::create([1 => '1'])],
            [UntypedVector::create([]), -1, '2', UntypedVector::create([-1 => '2'])],
            [UntypedVector::create([]), 0, '2', UntypedVector::create([0 => '2'])],

            [UntypedVector::create([1 => true]), 1, 1, UntypedVector::create([1 => 1])],
        ];
    }


    /**
     * @dataProvider setTestTypesProvider
     *
     * @param UntypedVector $a
     * @param int           $key
     * @param mixed         $value
     * @param UntypedVector $expected
     */
    public function testSetTypes(UntypedVector $a, int $key, $value, UntypedVector $expected)
    {
        $new = $a->set($key, $value);

        $this->assertSame($expected->elements, $new->elements);
    }


    public function testSetReference()
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
    public function testGet()
    {
        $source  = UntypedVector::create([1 => 'Foo', 2 => 666]);
        $default = 'Default';

        $this->assertSame(null, $source->get(0));
        $this->assertNotSame('666', $source->get(2));
        $this->assertSame($default, $source->get(10, $default));
    }
    //</editor-fold>


    //<editor-fold desc="at()">
    public function testAtSuccessful()
    {
        $source = UntypedVector::create([1 => 'Foo', 2 => 666]);

        $this->assertSame(666, $source->at(2));
        $this->assertSame('Foo', $source->at(1));
        $this->assertEquals('666', $source->at(2));
        $this->assertNotSame('666', $source->at(2));
    }


    public function testAtUnsuccessful()
    {
        $source = UntypedVector::create([1 => 'Foo', 2 => 666]);

        $this->expectException(\ZFekete\DataStructures\Exception\InvalidOffsetException::class);

        $source->at(3);
    }
    //</editor-fold>


    //<editor-fold desc="only()">
    public function onlySuccessfulTestProvider()
    {
        $keys = [1, 3];

        return [
            [UntypedVector::create([1 => 12, 3 => null]), $keys, [1 => 12, 3 => null]],
            [UntypedVector::create([1 => 12, 3 => 'Foo']), $keys, [1 => 12, 3 => 'Foo']],
            [UntypedVector::create([1 => null, 3 => null]), $keys, [1 => null, 3 => null]],
        ];
    }


    /**
     * @dataProvider onlySuccessfulTestProvider
     *
     * @param UntypedVector $source
     * @param int[]         $keys
     * @param array         $expected
     */
    public function testOnlySuccessful(UntypedVector $source, array $keys, array $expected)
    {
        $this->assertSame($expected, $source->only($keys)->elements);
    }


    public function onlyUnsuccessfulTestProvider()
    {
        $keys = [1, 2, 4];

        return [
            [UntypedVector::create(), $keys, \array_fill_keys($keys, null)],
            [UntypedVector::create([1 => 12, 3 => null]), $keys, [1 => 12, 2 => null, 4 => null]]
        ];
    }


    /**
     * @dataProvider onlyUnsuccessfulTestProvider
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
    public function exceptTestProvider()
    {
        $keys = [1, 3];

        return [
            [UntypedVector::create(['Foo', 'Baz', true]), $keys, [0 => 'Foo', 2 => true]],
            [UntypedVector::create([1 => 'Foo', 2 => 'Baz', 3 => true]), $keys, [2 => 'Baz']],
            [UntypedVector::create([2 => 'Foo', 4 => 'Baz', 0 => true]), $keys, [2 => 'Foo', 4 => 'Baz', 0 => true]],
        ];
    }


    /**
     * @dataProvider exceptTestProvider
     *
     * @param UntypedVector $source
     * @param int[]         $keys
     * @param array         $expected
     */
    public function testExcept(UntypedVector $source, array $keys, array $expected)
    {
        $this->assertSame($expected, $source->except($keys)->elements);
    }
    //</editor-fold>


    //<editor-fold desc="firstKey()">
    public function firstKeyProvider()
    {
        return [
            [UntypedVector::create([1 => 'Foo', 2 => 666, -5 => 'Baz']), 1],
            [UntypedVector::create([-5 => 'Baz', 1 => 'Foo', 2 => 666]), -5]
        ];
    }


    /**
     * @dataProvider firstKeyProvider
     *
     * @param UntypedVector $vector
     * @param mixed         $expected
     */
    public function testFirstKey(UntypedVector $vector, int $expected)
    {
        $this->assertSame($expected, $vector->firstKey());
    }
    //</editor-fold>


    //<editor-fold desc="firstValue()">
    public function firstValueTestProvider()
    {
        return [
            [UntypedVector::create([1 => 'Foo', 3 => 666]), 'Foo'],
            [UntypedVector::create([3 => 666, 1 => 'Foo']), 666],
            [UntypedVector::create(), null]
        ];
    }


    /**
     * @dataProvider firstValueTestProvider
     *
     * @param UntypedVector $vector
     * @param mixed         $expected
     */
    public function testFirstValue(UntypedVector $vector, $expected)
    {
        $this->assertSame($expected, $vector->firstValue());
    }


    public function testFirstValueWithDefault()
    {
        $source  = UntypedVector::create([]);
        $default = 12;

        $this->assertSame($default, $source->firstValue($default));
    }
    //</editor-fold>


    //<editor-fold desc="lastKey()">
    public function lastKeyProvider()
    {
        return [
            [UntypedVector::create([1 => 'Foo', 2 => 666, -5 => 'Baz']), -5],
            [UntypedVector::create([-5 => 'Baz', 1 => 'Foo', 2 => 666]), 2]
        ];
    }


    /**
     * @dataProvider lastKeyProvider
     *
     * @param UntypedVector $vector
     * @param int           $expected
     */
    public function testLastKey(UntypedVector $vector, int $expected)
    {
        $this->assertSame($expected, $vector->lastKey());
    }
    //</editor-fold>


    //<editor-fold desc="lastValue()">
    public function lastValueTestProvider()
    {
        return [
            [UntypedVector::create([1 => 'Foo', 3 => 666]), 666],
            [UntypedVector::create([3 => 666, 1 => 'Foo']), 'Foo'],
            [UntypedVector::create(), null]
        ];
    }


    /**
     * @dataProvider lastValueTestProvider
     *
     * @param UntypedVector $vector
     * @param               $expected
     */
    public function testLastValue(UntypedVector $vector, $expected)
    {
        $this->assertSame($expected, $vector->lastValue());
    }


    public function testLastValueWithDefault()
    {
        $source  = UntypedVector::create([]);
        $default = 12;

        $this->assertSame($default, $source->lastValue($default));
        $this->assertSame(null, $source->lastValue());
    }
    //</editor-fold>


    //<editor-fold desc="count()">
    public function countTestProvider()
    {
        return [
            [UntypedVector::create([]), 0],
            [UntypedVector::create([1, 2, 3]), 3],
            [UntypedVector::create(['Foo', null, 12]), 3],
            [UntypedVector::create([null, null]), 2],
        ];
    }


    /**
     * @dataProvider countTestProvider
     *
     * @param UntypedVector $source
     * @param int           $expected
     */
    public function testCount(UntypedVector $source, int $expected)
    {
        $this->assertEquals($expected, $source->count());
    }
    //</editor-fold>


    //<editor-fold desc="isEmpty()">
    public function testIsEmpty()
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
    public function testIsNotEmpty()
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
    public function hasTestProvider()
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
     * @dataProvider hasTestProvider
     *
     * @param UntypedVector $source
     * @param int           $key
     * @param bool          $expected
     */
    public function testHas(UntypedVector $source, int $key, bool $expected)
    {
        $this->assertSame($expected, $source->has($key));
    }
    //</editor-fold>


    //<editor-fold desc="contains()">
    public function containsUnstrictTestProvider()
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
     * @dataProvider containsUnstrictTestProvider
     *
     * @param UntypedVector $source
     * @param mixed         $value
     * @param bool          $expected
     */
    public function testContainUnstrict(UntypedVector $source, $value, bool $expected)
    {
        $this->assertSame($expected, $source->contains($value, false));
    }


    public function containsStrictTestProvider()
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
     * @dataProvider containsStrictTestProvider
     *
     * @param UntypedVector $source
     * @param mixed         $value
     * @param bool          $expected
     */
    public function testContainStrict(UntypedVector $source, $value, bool $expected)
    {
        $this->assertSame($expected, $source->contains($value, true));
    }
    //</editor-fold>


    //<editor-fold desc="every()">
    public function everyTestProvider()
    {
        $intTester = function($val) : bool {
            return \is_int($val);
        };

        $gt5Tester = function($val) : bool {
            return $val > 5;
        };

        $stringTester = function($val) : bool {
            return \is_string($val);
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
     * @param UntypedVector $source
     * @param Closure       $tester
     * @param bool          $expected
     */
    public function testEvery(UntypedVector $source, \Closure $tester, bool $expected)
    {
        $this->assertSame($expected, $source->every($tester));
    }
    //</editor-fold>


    //<editor-fold desc="some()">
    public function someTestProvider()
    {
        $intTester = function($val) : bool {
            return \is_int($val);
        };

        $gt5Tester = function($val) : bool {
            return $val > 5;
        };

        $stringTester = function($val) : bool {
            return \is_string($val);
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
     * @param UntypedVector $source
     * @param Closure       $tester
     * @param bool          $expected
     */
    public function testSome(UntypedVector $source, \Closure $tester, bool $expected)
    {
        $this->assertSame($expected, $source->some($tester));
    }
    //</editor-fold>


    //<editor-fold desc="map()">
    public function mapTestProvider()
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
     * @param UntypedVector $source
     * @param Closure       $callback
     * @param array         $expected
     */
    public function testMap(UntypedVector $source, \Closure $callback, array $expected)
    {
        $sourceBackup = $source;

        $result = $source->map($callback);

        $this->assertSame($expected, $result->elements);
        $this->assertSame($source->elements, $sourceBackup->elements);
    }
    //</editor-fold>


    //<editor-fold desc="merge()">
    public function mergeTestProvider()
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
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expectedBtoA
     * @param array         $expectedAtoB
     */
    public function testMerge(UntypedVector $a, UntypedVector $b, array $expectedBtoA, array $expectedAtoB)
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
    public function filterTestProvider()
    {
        $intFilter = function($x) : bool {
            return \is_int($x);
        };
        $gt5Filter = function($x) : bool {
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
     * @param UntypedVector $source
     * @param array         $expected
     * @param Closure|null  $callback
     */
    public function testFilter(UntypedVector $source, array $expected, ?\Closure $callback = null)
    {
        $save = $source;

        $this->assertSame($expected, $source->filter($callback)->elements);
        $this->assertSame($save->elements, $source->elements);
    }
    //</editor-fold>


    //<editor-fold desc="diff()">
    public function diffTestProvider()
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
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     */
    public function testDiff(UntypedVector $a, array $b, array $expected)
    {
        $this->assertSame($expected, $a->diff($b));
    }
    //</editor-fold>


    //<editor-fold desc="diffVector()">
    public function diffVectorTestProvider()
    {
        return [
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
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expected
     */
    public function testDiffVector(UntypedVector $a, UntypedVector $b, array $expected)
    {
        $this->assertSame($expected, $a->diffVector($b)->elements);
    }
    //</editor-fold>


    //<editor-fold desc="diffKeys()">
    public function diffKeysTestProvider()
    {
        return [
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
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     */
    public function testDiffKeys(UntypedVector $a, array $b, array $expected)
    {
        $this->assertSame($expected, $a->diffKeys($b)->elements);
    }
    //</editor-fold>


    //<editor-fold desc="diffVectorKeys()">
    public function diffVectorKeysTestProvider()
    {
        return [
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
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expected
     */
    public function testDiffVectorKeys(UntypedVector $a, UntypedVector $b, array $expected)
    {
        $this->assertSame($expected, $a->diffVectorKeys($b)->elements);
    }
    //</editor-fold>


    //<editor-fold desc="intersect()">
    public function intersectTestProvider()
    {
        return [
            [UntypedVector::create([]),                   [],         []],
            [UntypedVector::create([]),                   [1],        []],
            [UntypedVector::create([2]),                  [],         []],
            [UntypedVector::create([2]),                  [2],        [2]],
            [UntypedVector::create([1, 3, 4, 5]),         [1, 2, 3],  [0 => 1, 2 => 3]],
            [UntypedVector::create(['Foo', 'Baz', 1, 2]), ['Baz', 1], [1 => 'Baz', 2 => 1]]
        ];
    }


    /**
     * @dataProvider intersectTestProvider
     *
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     */
    public function testIntersect(UntypedVector $a, array $b, array $expected)
    {
        $this->assertSame($expected, $a->intersect($b)->elements);
    }
    //</editor-fold>


    //<editor-fold desc="intersectVector()">
    public function intersectVectorTestProvider()
    {
        return [
            [UntypedVector::create([]),                   UntypedVector::create([]),         []],
            [UntypedVector::create([]),                   UntypedVector::create([1]),        []],
            [UntypedVector::create([2]),                  UntypedVector::create([]),         []],
            [UntypedVector::create([2]),                  UntypedVector::create([2]),        [2]],
            [UntypedVector::create([1, 3, 4, 5]),         UntypedVector::create([1, 2, 3]),  [0 => 1, 2 => 3]],
            [UntypedVector::create(['Foo', 'Baz', 1, 2]), UntypedVector::create(['Baz', 1]), [1 => 'Baz', 2 => 1]]
        ];
    }


    /**
     * @dataProvider intersectVectorTestProvider
     *
     * @param UntypedVector $a
     * @param UntypedVector $b
     * @param array         $expected
     */
    public function testIntersectVector(UntypedVector $a, UntypedVector $b, array $expected)
    {
        $this->assertSame($expected, $a->intersectVector($b)->elements);
    }
    //</editor-fold>


    //<editor-fold desc="intersectKeys">
    public function intersectKeysTestProvider()
    {
        return [
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
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     */
    public function testIntersectKeys(UntypedVector $a, array $b, array $expected)
    {
        $this->assertSame($expected, $a->intersectKeys($b));
    }
    //</editor-fold>


    //<editor-fold desc="intersectVectorKeys">
    public function intersectVectorKeysTestProvider()
    {
        return [
            [UntypedVector::create([]),               UntypedVector::create([]),     []],
            [UntypedVector::create([2 => 1, 4 => 2]), UntypedVector::create([]),     []],
            [UntypedVector::create([]),               UntypedVector::create([1, 2]), []],

            [UntypedVector::create([0 => 'Foo', 3 => 1]), UntypedVector::create([0 => 1, 2 => 2]),             [0 => 'Foo']],
            [UntypedVector::create([2 => 'Baz', 5 => 3]), UntypedVector::create([3 => 1, 4 => 2, 5 => 'Baz']), [5 => 3]],
        ];
    }


    /**
     * @dataProvider intersectKeysTestProvider
     *
     * @param UntypedVector $a
     * @param array         $b
     * @param array         $expected
     */
    public function testIntersectVectorKeys(UntypedVector $a, array $b, array $expected)
    {
        $this->assertSame($expected, $a->intersectKeys($b));
    }
    //</editor-fold>




    public function testReplace(UntypedVector $a, UntypedVector $b, array $expected)
    {
        $this->assertSame($expected, $a->replace($b));
    }


    public function testShift()
    {

    }


    //<editor-fold desc="unshift()">
    public function unshiftTestProvider()
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
     * @param UntypedVector $a
     * @param int|int[]     $b
     * @param UntypedVector $expected
     */
    public function testUnshift(UntypedVector $a, $b, UntypedVector $expected)
    {
        if (\is_array($b)) {
            $this->assertEquals($expected->all(), $a->unshift(... $b)->all());
        } else {
            $this->assertEquals($expected->all(), $a->unshift($b)->all());
        }
    }
    //</editor-fold>


    //<editor-fold desc="push()">
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
     * @param UntypedVector $a
     * @param int           $b
     * @param UntypedVector $expected
     */
    public function testPush(UntypedVector $a, int $b, UntypedVector $expected)
    {
        $this->assertEquals($expected->all(), $a->push($b)->all());
    }
    //</editor-fold>


    public function testPop()
    {

    }


    public function testClear()
    {

    }


}
