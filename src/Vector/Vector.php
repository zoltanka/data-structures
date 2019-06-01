<?php declare(strict_types=1);

namespace ZFekete\DataStructures\Vector;

use ZFekete\DataStructures\Exception\InvalidTypeException;
use ZFekete\DataStructures\Exception\TypeMismatchException;

class Vector extends AbstractVector
{
    public const TYPE_INT = 'integer';
    public const TYPE_STRING = 'string';
    public const TYPE_FLOAT = 'double';
    public const TYPE_BOOL = 'boolean';
    public const TYPE_ARRAY = 'array';
    public const TYPE_RESOURCE = 'resource';

    private const TYPES = [
        self::TYPE_INT, self::TYPE_STRING, self::TYPE_FLOAT, self::TYPE_BOOL, self::TYPE_ARRAY, self::TYPE_RESOURCE
    ];

    /**
     * @var string
     */
    protected $type;


    /**
     * TypedVector constructor.
     *
     * @param string $type
     * @param array  $elements
     */
    public function __construct(string $type, array $elements = [])
    {
        \assert($this->assertType($type), new InvalidTypeException('Invalid type provided: ' . $type));

        parent::__construct($elements);

        $this->type = $type;
    }


    /**
     * @inheritdoc
     */
    public function get(int $key, $default = null)
    {
        \assert(
            $this->assertValue($default),
            new InvalidTypeException(\sprintf('Argument 2 expected to be %s, %s received!',
                $this->type, \gettype($default)
            ))
        );

        return parent::get($key, $default);
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
        \assert(
            $this->assertValue($value),
            new TypeMismatchException(\sprintf('Argument 2 expected to be %s, %s received!',
                $this->type, \gettype($value)
            ))
        );

        $items = $this->elements;

        $items[$key] = $value;

        return new static($this->type, $items);
    }


    /**
     * @inheritdoc
     */
    public function firstValue($default = null)
    {
        \assert(
            $this->assertValue($default),
            new InvalidTypeException(\sprintf('Argument 1 expected to be %s, %s received!',
                $this->type, \gettype($default)
            ))
        );

        return parent::firstValue($default);
    }


    /**
     * @inheritdoc
     */
    public function lastValue($default = null)
    {
        \assert(
            $this->assertValue($default),
            new InvalidTypeException(\sprintf('Argument 1 expected to be %s, %s received!',
                $this->type, \gettype($default)
            ))
        );

        return parent::lastValue($default);
    }

    /**
     * @inheritdoc
     */
    public function filter(\Closure $callback = null) : self
    {
        $items = \array_filter($this->elements, $callback ?: null, \ARRAY_FILTER_USE_BOTH);

        return new static($this->type, $items);
    }


    /**
     * Returns back true of the given search can be found in the vector. If the vector is empty or the value does not
     * present in the vector it returns false.
     *
     * @param mixed $search
     *
     * @return bool
     */
    public function contains($search) : bool
    {
        \assert(
            $this->assertValue($search),
            new TypeMismatchException(\sprintf('Argument 1 expected to be %s, %s received!',
                $this->type, \gettype($search)
            ))
        );

        return \in_array($search, $this->elements, true);
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

        $elements = \array_map($cb, $this->elements, $keys);

        \assert($this->assertValues($elements), new InvalidTypeException(
            'Map callback returned back wrong type.'
        ));

        return static::create($this->type, $elements);
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
        \assert($this->assertVector($vector), new TypeMismatchException('Argument 1 has to be a TypedVector with the same type!'));

        return static::create($this->type, \array_merge($this->elements, $vector->elements));
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
        \assert($this->assertVector($vector), new TypeMismatchException('Argument 1 has to be a TypedVector with the same type!'));

        return static::create($this->type, \array_diff_key($this->elements, $vector->elements));
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
        \assert($this->assertVector($vector), new TypeMismatchException('Argument 1 has to be a TypedVector with the same type!'));

        return static::create($this->type, \array_intersect_key($this->elements, $vector->elements));
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
        \assert($this->assertVector($vector), new TypeMismatchException('Argument 1 has to be a TypedVector with the same type!'));

        return static::create($this->type, \array_replace($this->elements, $vector->elements));
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
        \assert($this->assertValues($elements), new TypeMismatchException('All arguments has to be a type of ' . $this->type));

        $items = $this->elements;

        \array_push($items, ... $elements);

        return static::create($this->type, $items);
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
        \assert($this->assertValues($elements), new TypeMismatchException('All arguments has to be a type of ' . $this->type));

        if ($this->isEmpty()) {
            return new static($this->type, \array_reverse($elements));
        }

        $firstKey = $this->firstKey() - 1;

        $items = $this->elements + \array_combine(\range($firstKey, $firstKey - (\count($elements) - 1)), $elements);

        return static::create($this->type, $items);
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

        return static::create($this->type, \array_flip($keys))->replace($this);
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

        return static::create($this->type, \array_flip($keys))->diff($this);
    }


    /**
     * Creates an instance of vector using the given elements.
     *
     * @param string $type
     * @param array  $elements
     *
     * @return static
     */
    public static function create(string $type, array $elements = []) : self
    {
        return new static($type, $elements);
    }


    /**
     * @param string $type
     *
     * @return bool
     */
    final protected function assertType(string $type) : bool
    {
        return \in_array($type, self::TYPES, true) || \class_exists($type);
    }


    final protected function assertValue($value) : bool
    {
        return $value instanceof $this->type || \gettype($value) === $this->type || $value === null;
    }


    final protected function assertValues(array $values) : bool
    {
        foreach ($values as $value) {
            if ($this->assertValue($value) === false) {
                return false;
            }
        }

        return true;
    }


    final protected function assertVector(Vector $structure) : bool
    {
        return $this->type === $structure->type;
    }
}
