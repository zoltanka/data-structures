<?php declare(strict_types=1);

namespace ZFekete\DataStructures\Vector;

use ArrayIterator;
use InvalidArgumentException;
use Traversable;
use ZFekete\DataStructures\Exception\InvalidOffsetException;
use IteratorAggregate;
use JsonSerializable;
use Closure;
use function array_filter;
use function array_key_exists;
use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_pop;
use function array_shift;
use function array_values;
use function assert;
use function call_user_func_array;
use function count;
use function is_bool;
use function is_int;
use const ARRAY_FILTER_USE_BOTH;

abstract class AbstractVector implements IteratorAggregate, JsonSerializable
{
    /**
     * @var array
     */
    protected array $elements;

    /**
     * AbstractVector constructor.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        assert($this->assertKeys($elements), new InvalidArgumentException('Invalid argument provided!'));

        $this->elements = $elements;
    }

    /**
     * Checks of all keys of the given array is integer. Returns back true if yes, otherwise false.
     *
     * @param mixed[] $assert
     *
     * @return bool
     */
    protected function assertKeys(array $assert): bool
    {
        return array_filter(array_keys($assert), function ($key): bool {
            return $this->assertKey($key) === false;
        }) === [];
    }

    /**
     * Returns back true if the given value is integer.
     *
     * @param mixed $key
     *
     * @return bool
     */
    protected function assertKey($key): bool
    {
        return is_int($key);
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
    abstract public function set(int $key, $value): AbstractVector;

    /**
     * Returns back the a value from the vector, stored on the given key. If the given key does not exist in the vector
     * it returns back the value provided in $default parameter.
     *
     * @param int        $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(int $key, $default = null)
    {
        return $this->elements[$key] ?? $default;
    }

    /**
     * Returns back a value from the vector, stored on the given key. If the key does not exist throws an exception.
     *
     * @param int $key
     *
     * @throws InvalidOffsetException
     *
     * @return mixed
     */
    public function at(int $key)
    {
        if ($this->has($key) === false) {
            throw new InvalidOffsetException("Offset \"$key\" does not exist!");
        }

        return $this->elements[$key];
    }

    /**
     * Returns back true if the given key exists in the vector.
     *
     * @param int $key
     *
     * @return bool
     */
    public function has(int $key): bool
    {
        return array_key_exists($key, $this->elements);
    }

    /**
     * Returns back the value on the lowest key in the vector. If the vector is empty, it returns back the value given
     * in the $default parameter.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function firstValue($default = null)
    {
        if ($this->isEmpty()) {
            return $default;
        }

        return $this->elements[$this->firstKey()];
    }

    /**
     * Returns whether the vector is empty or not.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Returns back how many elements the vector has.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * Returns back the first key inserted into the vector. If the vector is empty null will be returned.
     *
     * @return int|null
     */
    public function firstKey(): ?int
    {
        return array_key_first($this->elements);
    }

    /**
     * Returns back the value from the vector on the highest key. If the vector is empty it returns back the value
     * passed in the $default parameter.
     *
     * @param bool $default
     *
     * @return mixed
     */
    public function lastValue($default = null)
    {
        if ($this->isEmpty()) {
            return $default;
        }

        return $this->elements[$this->lastKey()];
    }

    /**
     * Returns back the last key inserted into the vector. If the vector is empty null will be returned.
     *
     * @return int|null
     */
    public function lastKey(): ?int
    {
        return array_key_last($this->elements);
    }

    /**
     * Filters the elements of the vector. If no parameter provided, an item will be removed base on its truthiness.
     * Otherwise the given callback function will be called on each element of the vector. Keeps the keys of the values.
     *
     * Returns back the result in a new vector instance.
     *
     * @param Closure|null $callback
     *
     * @return static
     */
    public function filter(?Closure $callback = null): AbstractVector
    {
        if ($callback === null) {
            return new static(array_filter($this->elements));
        }

        $items = array_filter($this->elements, $callback, ARRAY_FILTER_USE_BOTH);

        return new static($items);
    }

    /**
     * Tests whether all elements in the vector pass the test implemented by the given function.
     *
     * @param Closure $closure
     *
     * @return bool
     */
    public function every(Closure $closure): bool
    {
        if ($this->isEmpty()) { return false; }

        foreach ($this->elements as $k => $v) {
            $result = call_user_func_array($closure, [$v, $k]);

            assert(
                is_bool($result),
                new InvalidArgumentException('The provided callback function returned a non-bool type!')
            );

            if ($result === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tests whether at least one element in the vector passes the test implemented by the given function.
     *
     * @param Closure $closure
     *
     * @return bool
     */
    public function some(Closure $closure): bool
    {
        foreach ($this->elements as $k => $v) {
            $result = call_user_func_array($closure, [$v, $k]);

            assert(
                is_bool($result),
                new InvalidArgumentException('The provided callback function returned a non-bool type!')
            );

            if ($result === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns back elements from the vector on the keys given in the $keys parameter. If a key does not exist, in the
     * vector, the key will exist in the resulting vector with null value.
     *
     * @param int[] $keys
     *
     * @return static
     */
    abstract public function only(array $keys): AbstractVector;

    /**
     * Returns back every elements from the vector that are not preset in the given keys parameter.
     *
     * @param int[] $keys
     *
     * @return static
     */
    abstract public function except(array $keys): AbstractVector;

    /**
     * Applies the given callback function on each element of the vector. A new vector instance will be returned with
     * the modified values.
     *
     * @param Closure $cb
     *
     * @return static
     */
    abstract public function map(Closure $cb): AbstractVector;

    /**
     * Pushes the given elements to the beginning of he vector. Values will be pushes one by one from in the order they
     * were passed to the method.
     *
     * @param mixed[] ...$elements
     *
     * @return static
     */
    abstract public function unshift(... $elements): AbstractVector;

    /**
     * Shifts and element off from the beginning of the vector and returns it. Null will be returned if the vector is
     * empty.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * Pushes the given elements to the end of the vector. Values will be pushed in the order they were passed to the
     * method.
     *
     * @param mixed ...$elements
     *
     * @return static
     */
    abstract public function push(... $elements): AbstractVector;

    /**
     * Pops off an element from the end of the vector and returns it. Null will be returned if the vector is empty.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    /**
     * Empties the vector. Applies the changes on the current instance.
     *
     * @return static
     */
    public function clear(): AbstractVector
    {
        $this->elements = [];

        return $this;
    }

    /**
     * Returns whether the vector is NOT empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return $this->isEmpty() === false;
    }

    /**
     * Returns back the contents of the vector.
     *
     * @return mixed[]
     */
    public function all(): array
    {
        return $this->elements;
    }

    /**
     * Returns back only the values of the vector, re-indexing it.
     *
     * @return mixed[]
     */
    public function values(): array
    {
        return array_values($this->elements);
    }

    /**
     * Returns back the keys of the vector.
     *
     * @return int[]
     */
    public function keys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * Creates a copy from the vector.
     *
     * @return static
     */
    public function clone(): AbstractVector
    {
        return new static($this->elements);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->elements;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }
}
