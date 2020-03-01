<?php declare(strict_types=1);

use ZFekete\DataStructures\Tests\Mock\VectorMock as Vector;
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
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

    //<editor-fold desc="get">
    /**
     * Tests get method without the default parameter.
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::get
     *
     * @return void
     */
    public function testGet(): void
    {
        $vector = new Vector(Vector::TYPE_INT, [1, 2, 5, 7]);

        $this->assertSame(1, $vector->get(0));
        $this->assertSame(2, $vector->get(1));
        $this->assertSame(5, $vector->get(2));
        $this->assertSame(7, $vector->get(3));
    }

    /**
     * Tests get method with a default value, expecting it to be returned.
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::get
     *
     * @return void
     */
    public function testGetWithDefaultValue(): void
    {
        $vector       = new Vector(Vector::TYPE_INT, [1, 5]);
        $defaultValue = 12;

        $this->assertSame($defaultValue, $vector->get(3, $defaultValue));
    }

    /**
     * Tests get with an invalid default value, expecting a TypeError to be thrown.
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::get
     *
     * @return void
     */
    public function testGetWithInvalidDefaultValue(): void
    {
        $vector       = new Vector(Vector::TYPE_INT, [1, 5]);
        $defaultValue = '12';

        $this->expectException(TypeError::class);
        $vector->get(3, $defaultValue);
    }
    //</editor-fold>

    //<editor-fold desc="set">
    /**
     * Provider for {@see VectorTest::testSet()}
     *
     * @return array
     */
    public function setTestProvider(): array
    {
        $resource = fopen('php://stdout', 'r');

        return [
            [Vector::create(Vector::TYPE_INT),    1,      Vector::create(Vector::TYPE_INT, [1])],
            [Vector::create(Vector::TYPE_STRING), '1',    Vector::create(Vector::TYPE_STRING, ['1'])],
            [Vector::create(Vector::TYPE_FLOAT),  2.42,   Vector::create(Vector::TYPE_FLOAT, [2.42])],
            [Vector::create(Vector::TYPE_BOOL),   true,   Vector::create(Vector::TYPE_BOOL, [true])],
            [Vector::create(Vector::TYPE_ARRAY),  [1, 2], Vector::create(Vector::TYPE_ARRAY, [[1, 2]])],
            [
                Vector::create(Vector::TYPE_RESOURCE),
                $resource,
                Vector::create(Vector::TYPE_RESOURCE, [$resource])
            ]
        ];
    }

    /**
     * @dataProvider setTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::set
     *
     * @param Vector $source
     * @param mixed  $value
     * @param Vector $expected
     *
     * @return void
     */
    public function testSet(Vector $source, $value, Vector $expected): void
    {
        $this->assertEquals($expected->elements, $source->set(0, $value)->elements);
    }

    /**
     * Tests set method with incorrect type.
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::set
     *
     * @return void
     */
    public function testSetInvalid(): void
    {
        $vector = new Vector(Vector::TYPE_INT);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf(
            'Argument 2 expected to be %s, %s received',
            Vector::TYPE_INT,
            Vector::TYPE_STRING
        ));

        $vector->set(0, '12');
    }
    //</editor-fold>

    //<editor-fold desc="firstValue">
    /**
     * Provider for {@see VectorTest::testFirstValue()}
     *
     * @return array
     */
    public function firstValueTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT), null],
            [Vector::create(Vector::TYPE_INT, []), null],
            [Vector::create(Vector::TYPE_INT, [5]), 5],
            [Vector::create(Vector::TYPE_INT, [1 => 5]), 5],
            [Vector::create(Vector::TYPE_INT, [2 => 5, 1 => 7]), 5],

            [Vector::create(Vector::TYPE_STRING), null],
            [Vector::create(Vector::TYPE_STRING, []), null],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Foo']), 'Foo'],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Baz', 1 => 'Foo']), 'Baz']
        ];
    }

    /**
     * @dataProvider firstValueTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::firstValue
     *
     * @param Vector $source
     * @param mixed  $expected
     *
     * @return void
     */
    public function testFirstValue(Vector $source, $expected): void
    {
        $this->assertSame($expected, $source->firstValue());
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::firstValue
     *
     * @return void
     */
    public function testFirstValueWithDefault(): void
    {
        $vector = new Vector(Vector::TYPE_INT);
        $default = 7;

        $this->assertSame($default, $vector->firstValue($default));

        $vector = new Vector(Vector::TYPE_INT, []);
        $this->assertSame($default, $vector->firstValue($default));
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::firstValue
     *
     * @return void
     */
    public function testFirstValueWithWrongDefaultValue(): void
    {
        $vector = new Vector(Vector::TYPE_INT, [1, 2, 3]);

        $default = '12';
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf(
            'Argument 1 expected to be %s, %s received!',
            Vector::TYPE_INT,
            Vector::TYPE_STRING
        ));

        $vector->firstValue($default);
    }
    //</editor-fold>

    //<editor-fold desc="lastValue">
    /**
     * Provider for {@see VectorTest::testLastValue()}
     *
     * @return array
     */
    public function lastValueTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT), null],
            [Vector::create(Vector::TYPE_INT, []), null],
            [Vector::create(Vector::TYPE_INT, [5]), 5],
            [Vector::create(Vector::TYPE_INT, [1 => 5]), 5],
            [Vector::create(Vector::TYPE_INT, [2 => 5, 1 => 7]), 7],

            [Vector::create(Vector::TYPE_STRING), null],
            [Vector::create(Vector::TYPE_STRING, []), null],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Foo']), 'Foo'],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Baz', 1 => 'Foo']), 'Foo']
        ];
    }

    /**
     * @dataProvider lastValueTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::lastValue
     *
     * @param Vector $source
     * @param mixed  $expected
     *
     * @return void
     */
    public function testLastValue(Vector $source, $expected): void
    {
        $this->assertSame($expected, $source->lastValue());
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::lastValue
     *
     * @return void
     */
    public function testLastValueWithDefault(): void
    {
        $vector = new Vector(Vector::TYPE_INT);
        $default = 7;

        $this->assertSame($default, $vector->lastValue($default));

        $vector = new Vector(Vector::TYPE_INT, []);
        $this->assertSame($default, $vector->lastValue($default));
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::firstValue
     *
     * @return void
     */
    public function testLastValueWithWrongDefaultValue(): void
    {
        $vector = new Vector(Vector::TYPE_INT, [1, 2, 3]);

        $default = '12';
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf(
            'Argument 1 expected to be %s, %s received!',
            Vector::TYPE_INT,
            Vector::TYPE_STRING
        ));

        $vector->lastValue($default);
    }
    //</editor-fold>

    //<editor-fold desc="filter">
    /**
     * Provider for {@see VectorTest::testFilter()}
     *
     * @return array
     */
    public function filterTestProvider(): array
    {
        $lt5Filter = static function(int $x) : bool {
            return $x < 5;
        };
        $gt5Filter = static function(int $x) : bool {
            return $x > 5;
        };

        return [
            [Vector::create(Vector::TYPE_INT, []),           [],               null],
            [Vector::create(Vector::TYPE_INT, [6, 7, 3, 0]), [6, 7, 3],        null],
            [Vector::create(Vector::TYPE_INT, [6, 7, 3, 0]), [2 => 3, 3 => 0], $lt5Filter],
            [Vector::create(Vector::TYPE_INT, [6, 7, 3, 0]), [0 => 6, 1 => 7], $gt5Filter]
        ];
    }

    /**
     * @dataProvider filterTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::filter
     *
     * @param Vector       $source
     * @param array        $expected
     * @param Closure|null $callback
     *
     * @return void
     */
    public function testFilter(Vector $source, array $expected, ?Closure $callback = null): void
    {
        $save = $source;

        $this->assertSame($expected, $source->filter($callback)->elements);
        $this->assertSame($save->elements, $source->elements);
    }
    //</editor-fold>

    //<editor-fold desc="contains">
    /**
     * Provider for {@see VectorTest::testContains()}
     *
     * @return array
     */
    public function containsTestProvider(): array
    {
        $stringNeedle    = 'Foo';
        $stringIntNeedle = '1';
        $intNeedle       = 2;

        return [
            [Vector::create(Vector::TYPE_INT),            $intNeedle, false],
            [Vector::create(Vector::TYPE_INT, [1, 3, 5]), $intNeedle, false],
            [Vector::create(Vector::TYPE_INT, [2, 4, 6]), $intNeedle, true],

            [Vector::create(Vector::TYPE_STRING),                            $stringNeedle, false],
            [Vector::create(Vector::TYPE_STRING, ['Tree', 'Wood', 'Apple']), $stringNeedle, false],
            [Vector::create(Vector::TYPE_STRING, ['Foo', 'Baz', 'Apple']),   $stringNeedle, true],

            [Vector::create(Vector::TYPE_STRING),                  $stringIntNeedle, false],
            [Vector::create(Vector::TYPE_STRING, ['1', '3', '5']), $stringIntNeedle, true],
            [Vector::create(Vector::TYPE_STRING, ['2', '4', '6']), $stringIntNeedle, false]
        ];
    }

    /**
     * @dataProvider containsTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::contains
     *
     * @param Vector $source
     * @param mixed  $needle
     * @param bool   $expected
     *
     * @return void
     */
    public function testContains(Vector $source, $needle, bool $expected): void
    {
        $this->assertSame($expected, $source->contains($needle));
    }

    /**
     * @return void
     */
    public function testContainsUnsuccessfulBool(): void
    {
        $vector = Vector::create(Vector::TYPE_BOOL, [true, false]);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf(
            'Argument 1 expected to be %s, %s received!',
            Vector::TYPE_BOOL,
            Vector::TYPE_INT
        ));

        $vector->contains(1);
    }

    /**
     * @return void
     */
    public function testContainsUnsuccessfulInt(): void
    {
        $vector = Vector::create(Vector::TYPE_INT, [1, 6, 8]);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf(
            'Argument 1 expected to be %s, %s received!',
            Vector::TYPE_INT,
            Vector::TYPE_STRING
        ));

        $vector->contains('Foo');
    }

    /**
     * @return void
     */
    public function testContainsUnsuccessfulString(): void
    {
        $vector = Vector::create(Vector::TYPE_STRING, ['Apple', 'Wood', 'Tree']);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf(
            'Argument 1 expected to be %s, %s received!',
            Vector::TYPE_STRING,
            Vector::TYPE_INT
        ));

        $vector->contains(1);
    }
    //</editor-fold>

    //<editor-fold desc="map">
    /**
     * Provider for {@see VectorTest::testMap()}
     *
     * @return array
     */
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
            [Vector::create(Vector::TYPE_INT,    []),             $doingNothingCb, []],
            [Vector::create(Vector::TYPE_INT,    [1, 2, 3, 4]),   $squareCb,       [1, 4, 9, 16]],
            [Vector::create(Vector::TYPE_STRING, ['Foo', 'Baz']), $appendCb,       ['Prefix Foo', 'Prefix Baz']]
        ];
    }

    /**
     * @dataProvider mapTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::map()
     *
     * @param Vector  $source
     * @param Closure $callback
     * @param array   $expected
     *
     * @return void
     */
    public function testMap(Vector $source, Closure $callback, array $expected): void
    {
        $save = $source;

        $result = $source->map($callback);

        $this->assertSame($expected, $result->elements);
        $this->assertSame($source->elements, $save->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::map()
     *
     * @return void
     */
    public function testMapUnsuccessful(): void
    {
        $vector = Vector::create(Vector::TYPE_INT, [1, 2, 3, 4]);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Callback function returned back wrong type!');

        $vector->map(fn(int $x) => (string) $x);
    }
    //</editor-fold>

    //<editor-fold desc="merge">
    /**
     * Provider for {@see VectorTest::testMerge()}
     *
     * @return array
     */
    public function mergeTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_STRING), Vector::create(Vector::TYPE_STRING), []],
            [Vector::create(Vector::TYPE_STRING, []), Vector::create(Vector::TYPE_STRING, []), []],
            [Vector::create(Vector::TYPE_STRING, ['Foo']), Vector::create(Vector::TYPE_STRING, ['Baz']), ['Foo', 'Baz']],
            [Vector::create(Vector::TYPE_STRING, ['Baz']), Vector::create(Vector::TYPE_STRING, ['Foo']), ['Baz', 'Foo']],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Baz']), Vector::create(Vector::TYPE_STRING, [5 => 'Foo']), [0 => 'Baz', 1 => 'Foo']],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Baz']), Vector::create(Vector::TYPE_STRING, [2 => 'Foo']), [0 => 'Baz', 1 => 'Foo']],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Foo']), Vector::create(Vector::TYPE_STRING, [2 => 'Baz']), [0 => 'Foo', 1 => 'Baz']],
        ];
    }

    /**
     * @dataProvider mergeTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::merge
     *
     * @param Vector $a
     * @param Vector $b
     * @param array  $expected
     *
     * @return void
     */
    public function testMerge(Vector $a, Vector $b, array $expected): void
    {
        $result = $a->merge($b);

        $this->assertSame($expected, $result->elements);
        $this->assertNotSame($a, $result);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::merge
     *
     * @return void
     */
    public function testMergeUnsuccessful(): void
    {
        $a = new Vector(Vector::TYPE_INT,    []);
        $b = new Vector(Vector::TYPE_STRING, []);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf('Argument 1 has to be a Vector with type of %s!', Vector::TYPE_INT)
        );

        $a->merge($b);
    }
    //</editor-fold>

    //<editor-fold desc="diff">
    /**
     * Provider for {@see VectorTest::testDiff()}
     *
     * @return array
     */
    public function diffTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT), [], []],
            [Vector::create(Vector::TYPE_INT, []), [], []],
            [Vector::create(Vector::TYPE_INT, []), [1], []],
            [Vector::create(Vector::TYPE_INT, [1]), [1], []],
            [Vector::create(Vector::TYPE_INT, [1, 3, 5]), [1, 2, 3], [2 => 5]],
            [Vector::create(Vector::TYPE_STRING, ['Foo', 'Baz', 'Tree', 'Apple']), ['Baz', 'Apple'], [0 => 'Foo', 2 => 'Tree']],
        ];
    }

    /**
     * @dataProvider diffTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::diff
     *
     * @param Vector $a
     * @param array  $b
     * @param array  $expected
     *
     * @return void
     */
    public function testDiff(Vector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->diff($b)->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::diff()
     *
     * @return void
     */
    public function testDiffInvalid(): void
    {
        $a = new Vector(Vector::TYPE_INT, [1, 2, 3]);
        $b = ['Foo', 'Baz'];

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf('Argument 1 has to be an array of %s!', Vector::TYPE_INT));

        $a->diff($b);
    }
    //</editor-fold>

    //<editor-fold desc="diffVector">
    /**
     * Provider for {@see VectorTest::testDiffVector()}
     *
     * @return array
     */
    public function diffVectorTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT), Vector::create(Vector::TYPE_INT, []), []],
            [Vector::create(Vector::TYPE_INT, []), Vector::create(Vector::TYPE_INT, []), []],
            [Vector::create(Vector::TYPE_INT, []), Vector::create(Vector::TYPE_INT, [1]), []],
            [Vector::create(Vector::TYPE_INT, [1]), Vector::create(Vector::TYPE_INT, [1]), []],
            [Vector::create(Vector::TYPE_INT, [1, 3, 5]), Vector::create(Vector::TYPE_INT, [1, 2, 3]), [2 => 5]],
            [Vector::create(Vector::TYPE_STRING, ['Foo', 'Baz', 'Tree', 'Apple']), Vector::create(Vector::TYPE_STRING, ['Baz', 'Apple']), [0 => 'Foo', 2 => 'Tree']],
        ];
    }

    /**
     * @dataProvider diffVectorTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::diffVector
     *
     * @param Vector $a
     * @param Vector $b
     * @param array  $expected
     *
     * @return void
     */
    public function testDiffVector(Vector $a, Vector $b, array $expected): void
    {
        $this->assertSame($expected, $a->diffVector($b)->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::diff()
     *
     * @return void
     */
    public function testDiffVectorInvalid(): void
    {
        $a = new Vector(Vector::TYPE_INT, [1, 2, 3]);
        $b = new Vector(Vector::TYPE_STRING, ['Foo', 'Baz']);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf('Argument 1 has to be a Vector with type of %s!', Vector::TYPE_INT));

        $a->diffVector($b);
    }
    //</editor-fold>

    //<editor-fold desc="diffKeys">
    /**
     * Provider for {@see VectorTest::testDiffKeys()}
     *
     * @return array
     */
    public function diffKeysTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT),   [], []],
            [Vector::create(Vector::TYPE_INT,    []), [], []],
            [Vector::create(Vector::TYPE_INT,    []), [1, 2], []],
            [Vector::create(Vector::TYPE_INT,    [3, 4]), [], [3, 4]],
            [Vector::create(Vector::TYPE_INT,    [0 => 3, 5 => 4]), [], [0 => 3, 5 => 4]],
            [Vector::create(Vector::TYPE_INT,    [0 => 3, 5 => 4]), [1 => 2], [0 => 3, 5 => 4]],
            [Vector::create(Vector::TYPE_STRING, [0 => 'Foo', 5 => 'Baz']), [0 => 2, 2 => 1], [5 => 'Baz']],
        ];
    }

    /**
     * @dataProvider diffKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::diffKeys()
     *
     * @param Vector $a
     * @param array  $b
     * @param array  $expected
     *
     * @return void
     */
    public function testDiffKeys(Vector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->diffKeys($b)->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::diffKeys()
     *
     * @return void
     */
    public function testDiffKeysUnsuccessful(): void
    {
        $a = new Vector(Vector::TYPE_INT, [0 => 1, 1 => 2]);
        $b = ['name' => 'Foo', 0 => 'Baz'];

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Argument 1 ha to be an integer indexed array!');

        $a->diffKeys($b);
    }
    //</editor-fold>

    //<editor-fold desc="diffVectorKeys">
    /**
     * Provider for {@see VectorTest::testDiffVectorKeys()}
     *
     * @return array
     */
    public function diffVectorKeysTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT),                              Vector::create(Vector::TYPE_INT),                   []],
            [Vector::create(Vector::TYPE_INT, []),                          Vector::create(Vector::TYPE_INT),                   []],
            [Vector::create(Vector::TYPE_INT),                              Vector::create(Vector::TYPE_INT, []),               []],
            [Vector::create(Vector::TYPE_INT, []),                          Vector::create(Vector::TYPE_INT, []),               []],
            [Vector::create(Vector::TYPE_INT, []),                          Vector::create(Vector::TYPE_INT, [1, 2]),           []],
            [Vector::create(Vector::TYPE_INT, [3, 4]),                      Vector::create(Vector::TYPE_INT, []),               [3, 4]],
            [Vector::create(Vector::TYPE_INT, [0 => 3, 5 => 4]),            Vector::create(Vector::TYPE_INT, []),               [0 => 3, 5 => 4]],
            [Vector::create(Vector::TYPE_STRING, [0 => 'Foo', 5 => 'Baz']), Vector::create(Vector::TYPE_STRING, [0 => 'Tree', 2 => 'Apple']), [5 => 'Baz']],
            [Vector::create(Vector::TYPE_STRING, [0 => 'Foo', 5 => 'Baz']), Vector::create(Vector::TYPE_INT,    [0 => 2, 2 => 1]),            [5 => 'Baz']],
        ];
    }

    /**
     * @dataProvider diffVectorKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::diffVectorKeys()
     *
     * @param Vector $a
     * @param Vector $b
     * @param array  $expected
     *
     * @return void
     */
    public function testDiffVectorKeys(Vector $a, Vector $b, array $expected): void
    {
        $this->assertSame($expected, $a->diffVectorKeys($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="intersect">
    /**
     * Provider for {@see VectorTest::testIntersect()}
     *
     * @return array
     */
    public function intersectTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT),                       [],         []],
            [Vector::create(Vector::TYPE_INT, []),                   [],         []],
            [Vector::create(Vector::TYPE_INT, []),                   [1],        []],
            [Vector::create(Vector::TYPE_INT, [2]),                  [],         []],
            [Vector::create(Vector::TYPE_INT, [2]),                  [2],        [2]],
            [Vector::create(Vector::TYPE_INT, [1, 3, 4, 5]),         [1, 2, 3],  [0 => 1, 1 => 3]],
            [Vector::create(Vector::TYPE_INT, [1, 4, 3, 5]),         [1, 2, 3],  [0 => 1, 2 => 3]],
            [Vector::create(Vector::TYPE_STRING, ['Foo', 'Baz', 'Tree', 'Apple']), ['Baz', 'Tree'], [1 => 'Baz', 2 => 'Tree']]
        ];
    }

    /**
     * @dataProvider intersectTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::intersect()
     *
     * @param Vector $a
     * @param array  $b
     * @param array  $expected
     *
     * @return void
     */
    public function testIntersect(Vector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersect($b)->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::intersect()
     *
     * @return void
     */
    public function testIntersectUnsuccessful(): void
    {
        $a = new Vector(Vector::TYPE_INT, [1, 2, 3]);
        $b = ['Foo', 1, 'Baz', 12];

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf('Argument 1 has to be an array of %s!', Vector::TYPE_INT)
        );

        $a->intersect($b);
    }
    //</editor-fold>

    //<editor-fold desc="intersectVector">
    /**
     * Provider for {@see VectorTest::testIntersectVector()}
     *
     * @return array
     */
    public function intersectVectorTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT),                       Vector::create(Vector::TYPE_INT),             []],
            [Vector::create(Vector::TYPE_INT, []),                   Vector::create(Vector::TYPE_INT),             []],
            [Vector::create(Vector::TYPE_INT),                       Vector::create(Vector::TYPE_INT, []),         []],
            [Vector::create(Vector::TYPE_INT, []),                   Vector::create(Vector::TYPE_INT, []),         []],
            [Vector::create(Vector::TYPE_INT, []),                   Vector::create(Vector::TYPE_INT, [1]),        []],
            [Vector::create(Vector::TYPE_INT, [2]),                  Vector::create(Vector::TYPE_INT, []),         []],
            [Vector::create(Vector::TYPE_INT, [2]),                  Vector::create(Vector::TYPE_INT, [2]),        [2]],
            [Vector::create(Vector::TYPE_INT, [1, 3, 4, 5]),         Vector::create(Vector::TYPE_INT, [1, 2, 3]),  [0 => 1, 1 => 3]],
            [Vector::create(Vector::TYPE_INT, [1, 4, 3, 5]),         Vector::create(Vector::TYPE_INT, [1, 2, 3]),  [0 => 1, 2 => 3]],
            [Vector::create(Vector::TYPE_STRING, ['Foo', 'Baz', 'Apple', 'Tree']), Vector::create(Vector::TYPE_STRING, ['Baz', 'Tree']), [1 => 'Baz', 3 => 'Tree']]
        ];
    }

    /**
     * @dataProvider intersectVectorTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::intersectVector()
     *
     * @param Vector $a
     * @param Vector $b
     * @param array  $expected
     *
     * @return void
     */
    public function testIntersectVector(Vector $a, Vector $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersectVector($b)->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::intersectVector()
     *
     * @return void
     */
    public function testIntersectVectorUnsuccessful(): void
    {
        $a = new Vector(Vector::TYPE_INT, [1, 2, 3]);
        $b = new Vector(Vector::TYPE_STRING, ['Foo']);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf('Argument 1 has to be a Vector with type of %s!', Vector::TYPE_INT));

        $a->intersectVector($b);
    }
    //</editor-fold>

    //<editor-fold desc="intersectKeys">
    /**
     * Provider for {@see VectorTest::testIntersectKeys()}
     *
     * @return array
     */
    public function intersectKeysTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT),                   [],     []],
            [Vector::create(Vector::TYPE_INT, []),               [],     []],
            [Vector::create(Vector::TYPE_INT, [2 => 1, 4 => 2]), [],     []],
            [Vector::create(Vector::TYPE_INT, []),               [1, 2], []],
            [Vector::create(Vector::TYPE_INT, [1 => 7, 4 => 5]), [1 => 1, 3 => 2], [1 => 7]],

            [Vector::create(Vector::TYPE_STRING, [0 => 'Foo', 3 => 'Baz']), [0 => 'Apple', 2 => 'Tree'],           [0 => 'Foo']],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Baz', 5 => 'Foo']), [2 => "Tree", 4 => 'Foo', 5 => 'Baz'], [2 => 'Baz', 5 => 'Foo']]
        ];
    }

    /**
     * @dataProvider intersectKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::intersectKeys()
     *
     * @param Vector $a
     * @param array  $b
     * @param array  $expected
     *
     * @return void
     */
    public function testIntersectKeys(Vector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersectKeys($b)->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::intersectKeys()
     *
     * @return void
     */
    public function testIntersectKeysUnsuccessful(): void
    {
        $a = new Vector(Vector::TYPE_INT, [1, 4, 6]);
        $b = ['foo' => 1, 1 => 4];

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Argument 1 ha to be an integer indexed array!');

        $a->intersectKeys($b);
    }
    //</editor-fold>

    //<editor-fold desc="intersectVectorKeys">
    /**
     * Provider for {@see VectorTest::testIntersectVectorKeys()}
     *
     * @return array
     */
    public function intersectVectorKeysTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT),                   Vector::create(Vector::TYPE_INT),         []],
            [Vector::create(Vector::TYPE_INT),                   Vector::create(Vector::TYPE_INT, []),     []],
            [Vector::create(Vector::TYPE_INT, []),               Vector::create(Vector::TYPE_INT),         []],
            [Vector::create(Vector::TYPE_INT, []),               Vector::create(Vector::TYPE_INT, []),     []],
            [Vector::create(Vector::TYPE_INT, [2 => 1, 4 => 2]), Vector::create(Vector::TYPE_INT, []),     []],
            [Vector::create(Vector::TYPE_INT, []),               Vector::create(Vector::TYPE_INT, [1, 2]), []],
            [Vector::create(Vector::TYPE_INT, [1 => 7, 4 => 5]), Vector::create(Vector::TYPE_INT, [1 => 1, 3 => 2]), [1 => 7]],

            [Vector::create(Vector::TYPE_STRING, [0 => 'Foo', 3 => 'Baz']), Vector::create(Vector::TYPE_STRING, [0 => 'Apple', 2 => 'Tree']),           [0 => 'Foo']],
            [Vector::create(Vector::TYPE_STRING, [2 => 'Baz', 5 => 'Foo']), Vector::create(Vector::TYPE_STRING, [2 => "Tree", 4 => 'Foo', 5 => 'Baz']), [2 => 'Baz', 5 => 'Foo']],

            [Vector::create(Vector::TYPE_STRING, [0 => 'Foo', 3 => 'Baz']), Vector::create(Vector::TYPE_INT, [3 => 1, 2 => 2]), [3 => 'Baz']]

        ];
    }

    /**
     * @dataProvider intersectVectorKeysTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::intersectVectorKeys()
     *
     * @param Vector $a
     * @param Vector $b
     * @param array  $expected
     *
     * @return void
     */
    public function testIntersectVectorKeys(Vector $a, Vector $b, array $expected): void
    {
        $this->assertSame($expected, $a->intersectVectorKeys($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="replace">
    /**
     * Provider for {@see VectorTest::testReplace()}
     *
     * @return array
     */
    public function replaceTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT), [], []],
            [Vector::create(Vector::TYPE_INT,  []), [], []],

            [Vector::create(Vector::TYPE_INT, [1 => 1]),          [2 => 5],                  [1 => 1, 2 => 5]],
            [Vector::create(Vector::TYPE_INT, [2 => 3, 4 => 12]), [2 => 9, 3 => 6, 5 => 20], [2 => 9, 4 => 12, 3 => 6, 5 => 20]],

            [Vector::create(Vector::TYPE_STRING, [1 => 'Foo']),             [],           [1 => 'Foo']],
            [Vector::create(Vector::TYPE_STRING, [1 => 'Foo']),             [2 => 'Baz'], [1 => 'Foo', 2 => 'Baz']],
            [Vector::create(Vector::TYPE_STRING, [1 => 'Foo', 2 => 'Baz']), [1 => 'Baz'], [1 => 'Baz', 2 => 'Baz']],

            [
                Vector::create(Vector::TYPE_BOOL, [0 => true, 1 => false, 2 => false, 3 => true, 4 => false]),
                [0 => false, 1 => true, 2 => false, 3 => false, 5 => false],
                [0 => false, 1 => true, 2 => false, 3 => false, 4 => false, 5 => false]
            ]
        ];
    }

    /**
     * @dataProvider replaceTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::replace()
     *
     * @param Vector $a
     * @param array  $b
     * @param array  $expected
     *
     * @return void
     */
    public function testReplace(Vector $a, array $b, array $expected): void
    {
        $this->assertSame($expected, $a->replace($b)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="replaceVector">
    /**
     * Provider for {@see VectorTest::testReplaceVector()}
     *
     * @return array
     */
    public function replaceVectorTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT),      Vector::create(Vector::TYPE_INT),     []],
            [Vector::create(Vector::TYPE_INT),      Vector::create(Vector::TYPE_INT, []), []],
            [Vector::create(Vector::TYPE_INT,  []), Vector::create(Vector::TYPE_INT),     []],
            [Vector::create(Vector::TYPE_INT,  []), Vector::create(Vector::TYPE_INT, []), []],

            [Vector::create(Vector::TYPE_INT, [1 => 1]),          Vector::create(Vector::TYPE_INT, [2 => 5]),                  [1 => 1, 2 => 5]],
            [Vector::create(Vector::TYPE_INT, [2 => 3, 4 => 12]), Vector::create(Vector::TYPE_INT, [2 => 9, 3 => 6, 5 => 20]), [2 => 9, 4 => 12, 3 => 6, 5 => 20]],

            [Vector::create(Vector::TYPE_STRING, [1 => 'Foo']),             Vector::create(Vector::TYPE_STRING, []),           [1 => 'Foo']],
            [Vector::create(Vector::TYPE_STRING, [1 => 'Foo']),             Vector::create(Vector::TYPE_STRING, [2 => 'Baz']), [1 => 'Foo', 2 => 'Baz']],
            [Vector::create(Vector::TYPE_STRING, [1 => 'Foo', 2 => 'Baz']), Vector::create(Vector::TYPE_STRING, [1 => 'Baz']), [1 => 'Baz', 2 => 'Baz']],

            [
                Vector::create(Vector::TYPE_BOOL, [0 => true, 1 => false, 2 => false, 3 => true, 4 => false]),
                Vector::create(Vector::TYPE_BOOL, [0 => false, 1 => true, 2 => false, 3 => false, 5 => false]),
                [0 => false, 1 => true, 2 => false, 3 => false, 4 => false, 5 => false]
            ]
        ];
    }

    /**
     * @dataProvider replaceVectorTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::replaceVector()
     *
     * @param Vector $a
     * @param Vector $b
     * @param array  $expected
     *
     * @return void
     */
    public function testReplaceVector(Vector $a, Vector $b, array $expected): void
    {
        $this->assertSame($expected, $a->replaceVector($b)->elements);
    }

    /**
     * @covers \ZFekete\DataStructures\Vector\Vector::replaceVector()
     *
     * @return void
     */
    public function testReplaceVectorUnsuccessful(): void
    {
        $a = new Vector(Vector::TYPE_INT, [2, 5, 7]);
        $b = new Vector(Vector::TYPE_STRING, ['Baz', 'Foo']);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf('Argument 1 has to be a Vector with type of %s!', Vector::TYPE_INT)
        );

        $a->replaceVector($b);
    }
    //</editor-fold>

    //<editor-fold desc="push">
    /**
     * Provider for {@see VectorTest::testPush()}
     *
     * @return array
     */
    public function pushTestProvider(): array
    {
        return [
            [Vector::create(Vector::TYPE_INT, []),        2, [0 => 2]],
            [Vector::create(Vector::TYPE_INT, [1 => 1]),  2, [1 => 1, 2 => 2]],
            [Vector::create(Vector::TYPE_INT, [-1 => 1]), 2, [-1 => 1, 0 => 2]],
            [Vector::create(Vector::TYPE_INT, [9 => 5]),  2, [9 => 5, 10 => 2]],

            [Vector::create(Vector::TYPE_STRING, [9 => 'Foo']),  'Baz', [9 => 'Foo', 10 => 'Baz']],
            [Vector::create(Vector::TYPE_STRING, [-7 => 'Foo']), 'Baz', [-7 => 'Foo', -6 => 'Baz']]
        ];
    }

    /**
     * @dataProvider pushTestProvider
     *
     * @covers \ZFekete\DataStructures\Vector\Vector::push()
     *
     * @param Vector    $a
     * @param mixed  ...$element
     * @param array     $expected
     *
     * @return void
     */
    public function testPush(): void
    {
        $arguments = func_get_args();

        $a        = array_shift($arguments);
        $expected = array_pop($arguments);

        $this->assertSame($expected, $a->push(... $arguments)->elements);
    }
    //</editor-fold>

    //<editor-fold desc="unshift">
    public function testUnshift(... $elements): void
    {

    }
    //</editor-fold>

    //<editor-fold desc="only">
    public function testOnly(): void
    {

    }
    //</editor-fold>

    //<editor-fold desc="except">
    public function testExcept(): void
    {

    }
    //</editor-fold>

    //<editor-fold desc="create">
    public function testCreate(): void
    {

    }
    //</editor-fold>
}
