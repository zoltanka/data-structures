<?php declare(strict_types=1);

namespace ZFekete\Collection;

use ZFekete\Exception\InvalidOffsetException;

class Vector
{
    /**
     * @var array
     */
    protected $elements;


    /**
     * Vector constructor.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        \assert($this->assertKeys($elements), new \InvalidArgumentException('Invalid argument provided!'));

        $this->elements = $elements;
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
    public function set(int $key, $value) : self
    {
        $items = $this->elements;

        $items[$key] = $value;

        return new static($items);
    }


    /**
     * Returns back the a value from the vector, stored on the given key. If the given key does not exist in the vector
     * it returns back the value provided in $default parameter.
     *
     * @param int  $key
     * @param null $default
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
     * Empties the vector. Applies the changes on the current instance.
     *
     * @return self
     */
    public function clear() : self
    {
        $this->elements = [];

        return $this;
    }


    /**
     * Returns back how many elements the vector has.
     *
     * @return int
     */
    public function count() : int
    {
        return \count($this->elements);
    }


    /**
     * Returns whether the vector is empty or not.
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return $this->count() === 0;
    }


    /**
     * Returns whether the vector is NOT empty.
     *
     * @return bool
     */
    public function isNotEmpty() : bool
    {
        return $this->isEmpty() === false;
    }


    /**
     * Filters the elements of the vector. If no parameter provided, an item will be removed base on its truthiness.
     * Otherwise the given callback function will be called on each element of the vector.
     *
     * Returns back the result in a new vector instance.
     *
     * @param \Closure|null $callback
     *
     * @return static
     */
    public function filter(\Closure $callback = null) : self
    {
        $items = \array_filter($this->elements, $callback ?: null, \ARRAY_FILTER_USE_BOTH);

        return new static($items);
    }


    /**
     * Returns back the lowest key of the vector. If the vector is empty null will be returned.
     *
     * @return int|null
     */
    public function firstKey() : ?int
    {
        return \array_key_first($this->elements);
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
     * Returns back the highest key of the vector. If the vector is empty null will be returned.
     *
     * @return int|null
     */
    public function lastKey() : ?int
    {
        return \array_key_last($this->elements);
    }


    /**
     * Returns back the value from the vector on the highest key. If the vector is empty it returns back the value
     * passed in the $default parameter.
     *
     * @param bool $default
     *
     * @return mixed
     */
    public function lastValue($default = true)
    {
        if ($this->isEmpty()) {
            return $default;
        }

        return $this->elements[$this->lastKey()];
    }


    /**
     * Returns back true of the given search can be found in the vector. If the vector is empty or the value does not
     * present in the vector it returns false.
     *
     * @param mixed $search
     * @param bool  $strict
     *
     * @return bool
     */
    public function contains($search, bool $strict = true) : bool
    {
        return \in_array($search, $this->elements, $strict);
    }


    /**
     * Tests the items of the vector against the given
     *
     * @param \Closure $closure
     *
     * @return bool
     */
    public function test(\Closure $closure) : bool
    {
        foreach ($this->elements as $k => $v) {
            $result = \call_user_func_array($closure, [$v, $k]);

            \assert(
                \is_bool($result),
                new \InvalidArgumentException('The provided callback function returned a non-bool type!')
            );

            if ($result === false) {
                return false;
            }
        }

        return true;
    }


    /**
     * Returns back elements from the vector on the keys given in the $keys parameter. If a key does not exist, in the
     * vector, the key will exist in the resulting vector with null value.
     *
     * @param int[] $keys
     *
     * @return static
     */
    public function only(array $keys) : self
    {
        \assert(
            $this->assertKeys(\array_flip($keys)),
            new \InvalidArgumentException('Argument 1 has to be an array of integers!')
        );

        return static::create(\array_flip($keys))->replace($this);
    }


    /**
     * Returns back every elements from the vector that are not preset in the given keys parameter.
     *
     * @param int[] $keys
     *
     * @return static
     */
    public function except(array $keys) : self
    {
        \assert(
            $this->assertKeys(\array_flip($keys)),
            new \InvalidArgumentException('Argument 1 has to be an array of integers!')
        );

        return static::create(\array_flip($keys))->diff($this);
    }


    /**
     * Applies the given callback function on each element of the vector. A new vector instance will be returned with
     * the modified values.
     *
     * @param \Closure $cb
     *
     * @return static
     */
    public function map(\Closure $cb) : self
    {
        $keys = \array_keys($this->elements);

        return static::create(\array_map($cb, $this->elements, $keys));
    }


    /**
     * Returns a new vector with the values of the current, merged together with the values from the given vector.
     *
     * @param Vector $vector
     *
     * @return static
     */
    public function merge(Vector $vector) : self
    {
        return static::create(\array_merge($this->elements, $vector->elements));
    }


    /**
     * Returns back a vector containing all element of the current vector whose keys are not present in the given
     * vector.
     *
     * @param Vector $vector
     *
     * @return static
     */
    public function diff(Vector $vector) : self
    {
        return static::create(\array_diff_key($this->elements, $vector->elements));
    }


    /**
     * Returns back a vector containing all element from the current vector which keys that are present in the given
     * vector.
     *
     * @param Vector $vector
     *
     * @return static
     */
    public function intersect(Vector $vector) : self
    {
        return static::create(\array_intersect_key($this->elements, $vector->elements));
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
     * @param Vector $vector
     *
     * @return static
     */
    public function replace(Vector $vector) : self
    {
        return static::create(\array_replace($this->elements, $vector->elements));
    }


    /**
     * Returns back true if the given key exists in the vector.
     *
     * @param int $key
     *
     * @return bool
     */
    public function has(int $key) : bool
    {
        return \array_key_exists($key, $this->elements);
    }


    /**
     * Pushes the given elements to the end of the vector. Values will be pushed in the order they were passed to the
     * method.
     *
     * @param int ...$elements
     *
     * @return static
     */
    public function push(... $elements) : self
    {
        $items = $this->elements;

        \array_push($items, ... $elements);

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
    public function unshift(... $elements) : self
    {
        if ($this->isEmpty()) {
            return new static(\array_reverse($elements));
        }

        $firstKey = $this->firstKey() - 1;

        $items = $this->elements + \array_combine(\range($firstKey, $firstKey - (\count($elements) - 1)), $elements);

        return static::create($items);
    }


    /**
     * Returns back the contents of the vector.
     *
     * @return mixed[]
     */
    public function toArray() : array
    {
        return $this->elements;
    }


    /**
     * Returns back only the values of the vector, re-indexing it.
     *
     * @return mixed[]
     */
    public function value() : array
    {
        return \array_values($this->elements);
    }


    /**
     * Returns back the keys of the vector.
     *
     * @return int[]
     */
    public function keys() : array
    {
        return \array_keys($this->elements);
    }


    /**
     * Creates an instance of vector using the given elements.
     *
     * @param array $elements
     *
     * @return static
     */
    public static function create(array $elements = []) : self
    {
        return new static($elements);
    }


    /**
     * Returns back true if the given value is integer.
     *
     * @param mixed $key
     *
     * @return bool
     */
    protected function assertKey($key) : bool
    {
        return \is_int($key);
    }


    /**
     * Checks of all keys of the given array is integer. Returns back true if yes, otherwise false.
     *
     * @param mixed[] $assert
     *
     * @return bool
     */
    protected function assertKeys(array $assert) : bool
    {
        return \array_filter(\array_keys($assert), function ($key) : bool {
            return $this->assertKey($key) === false;
        }) === [];
    }
}