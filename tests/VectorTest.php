<?php declare(strict_types=1);

use ZFekete\Collection\Vector;
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
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


    public function setTestProvider()
    {
        return [
            [Vector::create([]), 1, 2, Vector::create([1 => 2])],
            [Vector::create([]), -1, 2, Vector::create([-1 => 2])],
            [Vector::create([]), 0, 2, Vector::create([0 => 2])],
            [Vector::create([2 => 5]), 2, -2, Vector::create([2 => -2])],
            [Vector::create([2 => 5]), 1, -2, Vector::create([1 => -2, 2 => 5])],
        ];
    }


    /**
     * @dataProvider setTestProvider
     *
     * @param Vector $a
     * @param int    $key
     * @param int    $value
     * @param Vector $expected
     */
    public function testSet(Vector $a, int $key, int $value, Vector $expected)
    {
        $this->assertEquals($expected->toArray(), $a->set($key, $value)->toArray());
    }


    public function testSetReference()
    {
        $key   = 0;
        $value = 2;

        $source = Vector::create();

        $a = $source->set($key, $value);
        $b = $source->set($key, $value);

        $this->assertFalse($a === $b);
    }


    public function pushTestProvider()
    {
        return [
            [Vector::create([-1 => 1]), 2, Vector::create([-1 => 1, 0 => 2])],
            [Vector::create([0 => 1]), 2, Vector::create([0 => 1, 1 => 2])],
            [Vector::create([0 => 1]), 1, Vector::create([0 => 1, 1 => 1])],
            [Vector::create([2 => 4]), 2, Vector::create([2 => 4, 3 => 2])],
            [Vector::create([5 => 12]), 9, Vector::create([5 => 12, 6 => 9])]
        ];
    }


    /**
     * @dataProvider pushTestProvider
     *
     * @param Vector $a
     * @param int    $b
     * @param Vector $expected
     */
    public function testPush(Vector $a, int $b, Vector $expected)
    {
        $this->assertEquals($expected->toArray(), $a->push($b)->toArray());
    }


    public function unshiftTestProvider()
    {
        return [
            [Vector::create([-2 => 1]), 2, Vector::create([-3 => 2, -2 => 1])],
            [Vector::create([0 => 1]), 2, Vector::create([-1 => 2, 0 => 1])],
            [Vector::create([0 => 1]), 1, Vector::create([-1 => 1, 0 => 1])],
            [Vector::create([2 => 4]), 2, Vector::create([1 => 2, 2 => 4])],
            [Vector::create([5 => 12]), 9, Vector::create([4 => 9, 5 => 12])],
            [Vector::create([9 => 12]), [1, 2, 3, 4], Vector::create([5 => 4, 6 => 3, 7 => 2, 8 => 1, 9 => 12])],
            [Vector::create([-3 => 5]), [1, 2, 3, 4], Vector::create([-7 => 4, -6 => 3, -5 => 2, -4 => 1, -3 => 5])],
            [Vector::create([]), [1, 2, 3, 4], Vector::create([4, 3, 2, 1])]
        ];
    }


    /**
     * @dataProvider unshiftTestProvider
     *
     * @param Vector    $a
     * @param int|int[] $b
     * @param Vector $expected
     */
    public function testUnshift(Vector $a, $b, Vector $expected)
    {
        if (\is_array($b)) {
            $this->assertEquals($expected->toArray(), $a->unshift(... $b)->toArray());
        } else {
            $this->assertEquals($expected->toArray(), $a->unshift($b)->toArray());
        }
    }
}
