<?php declare(strict_types=1);

use ZFekete\DataStructures\Vector;
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
{
    public function setTestProvider()
    {
        $resource = \fopen('php://stdout', 'r');

        return [
            [Vector::create(Vector::TYPE_INT),    1,      Vector::create(Vector::TYPE_INT, [1])],
            [Vector::create(Vector::TYPE_STRING), '1',    Vector::create(Vector::TYPE_STRING, ['1'])],
            [Vector::create(Vector::TYPE_FLOAT),  2.42,   Vector::create(Vector::TYPE_FLOAT, [2.42])],
            [Vector::create(Vector::TYPE_BOOL),   true,   Vector::create(Vector::TYPE_STRING, [true])],
            [Vector::create(Vector::TYPE_ARRAY),  [1, 2], Vector::create(Vector::TYPE_STRING, [[1, 2]])],
            [
                Vector::create(Vector::TYPE_RESOURCE),
                $resource,
                Vector::create(Vector::TYPE_RESOURCE, [$resource])
            ],
        ];
    }


    /**
     * @dataProvider setTestProvider
     *
     * @param Vector $source
     * @param mixed  $value
     * @param Vector $expected
     */
    public function testSet(Vector $source, $value, Vector $expected)
    {
        $this->assertEquals($expected->all(), $source->set(0, $value)->all());
    }
}
