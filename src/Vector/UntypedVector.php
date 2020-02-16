<?php declare(strict_types=1);

namespace ZFekete\DataStructures\Vector;

use Closure;
use InvalidArgumentException;
use function array_combine;
use function array_diff;
use function array_diff_key;
use function array_fill_keys;
use function array_flip;
use function array_intersect;
use function array_intersect_key;
use function array_keys;
use function array_map;
use function array_merge;
use function array_push;
use function array_replace;
use function array_reverse;
use function assert;
use function count;
use function in_array;
use function range;

class UntypedVector extends AbstractVector
{
    /**
     * Vector constructor.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * Sets new value in the vector on the given key.
     *
     * Returns back the result in a new vector instance.
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return static
     */
    public function set(int $key, $value): UntypedVector
    {
        $items = $this->elements;

        $items[$key] = $value;

        return new static($items);
    }

    /**
     * Returns whether the Vector contains the given needle.
     *
     * @param mixed $needle
     * @param bool  $strict
     *
     * @return bool
     */
    public function contains($needle, bool $strict = true): bool
    {
        return in_array($needle, $this->elements, $strict);
    }

    /**
     * Returns back elements from the vector on the keys given in the $keys parameter. If a key does not exist, in the
     * vector, the key will exist in the resulting vector with null value.
     *
     * @param int[] $keys
     *
     * @return static
     */
    public function only(array $keys): UntypedVector
    {
        assert(
            $this->assertKeys(array_flip($keys)),
            new InvalidArgumentException('Argument 1 has to be an array of integers!')
        );

        return static::create(array_fill_keys($keys, null))
                     ->replace(array_intersect_key($this->elements, array_flip($keys)));
    }

    /**
     * Returns back every elements from the vector that are not preset in the given keys parameter.
     *
     * @param int[] $keys
     *
     * @return static
     */
    public function except(array $keys): UntypedVector
    {
        assert(
            $this->assertKeys(array_flip($keys)),
            new InvalidArgumentException('Argument 1 has to be an array of integers!')
        );

        return static::create(array_diff_key($this->elements, array_flip($keys)));
    }

    /**
     * Applies the given callback function on each element of the vector. A new vector instance will be returned with
     * the modified values.
     *
     * @param Closure $cb
     *
     * @return static
     */
    public function map(Closure $cb): UntypedVector
    {
        $keys = array_keys($this->elements);

        return static::create(array_map($cb, $this->elements, $keys));
    }

    /**
     * Returns a new vector with the values of the current, merged together with the values from the given vector.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function merge(AbstractVector $vector): UntypedVector
    {
        return static::create(array_merge($this->elements, $vector->elements));
    }

    /**
     * Returns back a vector containing all element of the current vector that are not present in the given array.
     *
     * @param mixed[] $elements
     *
     * @return static
     */
    public function diff(array $elements): UntypedVector
    {
        assert(
            $this->assertKeys($elements),
            new InvalidArgumentException('Argument 1 has to be an array of integers!')
        );

        return static::create(array_diff($this->elements, $elements));
    }

    /**
     * Returns back a vector containing all element of the current vector that are not present in the given vector.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function diffVector(AbstractVector $vector): UntypedVector
    {
        return static::diff($vector->elements);
    }

    /**
     * Returns back a vector containing all element of the current vector whose keys are not present in the given array..
     *
     * @param mixed[] $vector
     *
     * @return static
     */
    public function diffKeys(array $vector): UntypedVector
    {
        return static::create(array_diff_key($this->elements, $vector));
    }

    /**
     * Returns back a vector containing all element of the current vector whose keys are not present in the given
     * vector.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function diffVectorKeys(AbstractVector $vector): UntypedVector
    {
        return static::diffKeys($vector->elements);
    }

    /**
     * Returns back a vector containing all element from the current vector which are present in the given array.
     *
     * @param mixed[] $elements
     *
     * @return static
     */
    public function intersect(array $elements): UntypedVector
    {
        return static::create(array_intersect($this->elements, $elements));
    }

    /**
     * Returns back a vector containing all element from the current vector which are present in the given vector.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function intersectVector(AbstractVector $vector): UntypedVector
    {
        return static::intersect($vector->elements);
    }

    /**
     * Returns back a vector containing all element from the current vector which keys are present in the given array.
     *
     * @param mixed[] $elements
     *
     * @return static
     */
    public function intersectKeys(array $elements): UntypedVector
    {
        return static::create(array_intersect_key($this->elements, $elements));
    }

    /**
     * Returns back a vector containing all element from the current vector which keys are present in the given vector.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function intersectVectorKeys(AbstractVector $vector): UntypedVector
    {
        return static::intersectKeys($vector->elements);
    }

    /**
     * Replaces the values of the values of current vector with the values from the given vector on the same keys.
     * Returns back the result in a new vector instance.
     * If a key from the current vector exists in the given vector, its value will be replaced in the resulting vector
     * by the value from the given vector.
     * If a key exists in the given vector, and not the current, it will be created in the resulting vector.
     * If a key only exists in the current vector, it will be left as is.
     * The replace is not recursive.
     *
     * @param array $element
     *
     * @return static
     */
    public function replace(array $element): UntypedVector
    {
        return static::create(array_replace($this->elements, $element));
    }

    /**
     * Replaces the values of the values of current vector with the values from the given vector on the same keys.
     * Returns back the result in a new vector instance.
     * If a key from the current vector exists in the given vector, its value will be replaced in the resulting vector
     * by the value from the given vector.
     * If a key exists in the given vector, and not the current, it will be created in the resulting vector.
     * If a key only exists in the current vector, it will be left as is.
     * The replace is not recursive.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function replaceVector(AbstractVector $vector): UntypedVector
    {
        return static::create(array_replace($this->elements, $vector->elements));
    }

    /**
     * Pushes the given elements to the end of the vector. Values will be pushed in the order they were passed to the
     * method.
     *
     * @param mixed ...$elements
     *
     * @return static
     */
    public function push(... $elements): UntypedVector
    {
        $items = $this->elements;

        array_push($items, ... $elements);

        return static::create($items);
    }

    /**
     * Pushes the given elements to the beginning of he vector. Values will be pushes one by one from in the order they
     * were passed to the method.
     *
     * @param int[] ...$elements
     *
     * @return static
     */
    public function unshift(... $elements): UntypedVector
    {
        if ($this->isEmpty()) {
            return new static(array_reverse($elements));
        }

        $firstKey = $this->firstKey() - 1;

        $items = $this->elements + array_combine(range($firstKey, $firstKey - (count($elements) - 1)), $elements);

        return static::create($items);
    }

    /**
     * Creates an instance of vector using the given elements.
     *
     * @param array $elements
     *
     * @return static
     */
    public static function create(array $elements = []): UntypedVector
    {
        return new static($elements);
    }
}