<?php declare(strict_types=1);

namespace ZFekete\DataStructures\Vector;

use Closure;
use InvalidArgumentException;
use TypeError;
use ZFekete\DataStructures\Exception\InvalidTypeException;

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
    protected string $type;

    /**
     * Vector constructor.
     *
     * @param string $type
     * @param array  $elements
     */
    public function __construct(string $type, array $elements = [])
    {
        assert($this->assertType($type), new InvalidTypeException('Invalid type provided: ' . $type));

        parent::__construct($elements);

        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function get(int $key, $default = null)
    {
        assert(
            $this->assertValue($default),
            new TypeError(sprintf('Argument 2 has to be %s, %s received!',
                $this->type, gettype($default)
            ))
        );

        return parent::get($key, $default);
    }

    /**
     * Sets new value in the Vector on the given key.
     *
     * Returns back the result in a new Vector instance.
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return static
     */
    public function set(int $key, $value): self
    {
        assert(
            $this->assertValue($value),
            new TypeError(sprintf('Argument 2 has to be %s, %s received!',
                $this->type, gettype($value)
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
        assert(
            $this->assertValue($default),
            new TypeError(sprintf('Argument 1 has to be %s, %s received!',
                $this->type, gettype($default)
            ))
        );

        return parent::firstValue($default);
    }

    /**
     * @inheritdoc
     */
    public function lastValue($default = null)
    {
        assert(
            $this->assertValue($default),
            new TypeError(sprintf('Argument 1 has to be %s, %s received!',
                $this->type, gettype($default)
            ))
        );

        return parent::lastValue($default);
    }

    /**
     * @inheritdoc
     */
    public function filter(Closure $callback = null): self
    {
        if ($callback === null) {
            return new static($this->type, array_filter($this->elements));
        }

        $items = array_filter($this->elements, $callback ?: null, ARRAY_FILTER_USE_BOTH);

        return new static($this->type, $items);
    }

    /**
     * Returns back true of the given search can be found in the Vector. If the vector is empty or the value does not
     * present in the vector it returns false.
     *
     * @param mixed $search
     *
     * @return bool
     */
    public function contains($search): bool
    {
        assert(
            $this->assertValue($search),
            new TypeError(sprintf('Argument 1 has to be %s, %s received!',
                $this->type, gettype($search)
            ))
        );

        return in_array($search, $this->elements, true);
    }

    /**
     * Applies the given callback function on each element of the Vector. A new vector instance will be returned with
     * the modified values.
     *
     * @param Closure $cb
     *
     * @return static
     */
    public function map(Closure $cb): self
    {
        $keys = array_keys($this->elements);

        $elements = array_map($cb, $this->elements, $keys);

        assert($this->assertValues($elements), new TypeError(
            'Callback function returned back wrong type!'
        ));

        return static::create($this->type, $elements);
    }

    /**
     * Returns a new Vector with the values of the current, merged together with the values from the given vector.
     *
     * @param Vector $vector
     *
     * @return static
     */
    public function merge(Vector $vector): self
    {
        assert($this->assertVector($vector), new TypeError(
            sprintf('Argument 1 has to be a Vector with type of %s!', $this->type)
        ));

        return static::create($this->type, array_merge($this->elements, $vector->elements));
    }

    /**
     * Returns back a Vector containing all element of the current vector that are not present in the given array.
     *
     * @param array $elements
     *
     * @return static
     */
    public function diff(array $elements): self
    {
        assert($this->assertValues($elements), new TypeError(
            sprintf('Argument 1 has to be an array of %s!', $this->type)
        ));

        return $this->diffArray($elements);
    }

    /**
     * Creates a diff from the current Vector and the given array and returns the result in a new Vector instance.
     *
     * @param array $elements
     *
     * @return static
     */
    protected function diffArray(array $elements): self
    {
        return static::create($this->type, array_diff($this->elements, $elements));
    }

    /**
     * Returns back a Vector containing all element of the current vector that are not present in the given vector.
     *
     * @param Vector $vector
     *
     * @return static
     */
    public function diffVector(Vector $vector): self
    {
        assert($this->assertVector($vector), new TypeError(
            sprintf('Argument 1 has to be a Vector with type of %s!', $this->type)
        ));

        return static::diffArray($vector->elements);
    }

    /**
     * Returns back a Vector containing all element of the current vector whose keys are not present in the given array.
     *
     * @param array $elements
     *
     * @return static
     */
    public function diffKeys(array $elements): self
    {
        assert($this->assertKeys($elements), new TypeError(
            'Argument 1 ha to be an integer indexed array!'
        ));

        return $this->diffArrayKeys($elements);
    }

    /**
     * Creates a key diff from the current Vector and the given array and returns the result in a new Vector instance.
     *
     * @param array $elements
     *
     * @return static
     */
    protected function diffArrayKeys(array $elements): self
    {
        return static::create($this->type, array_diff_key($this->elements, $elements));
    }

    /**
     * Returns back a Vector containing all element of the current Vector whose keys are not present in the given
     * Vector.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function diffVectorKeys(AbstractVector $vector): self
    {
        return $this->diffArrayKeys($vector->elements);
    }

    /**
     * Returns back a Vector containing all element from the current Vector which are present in the given array.
     *
     * @param array $elements
     *
     * @return static
     */
    public function intersect(array $elements): self
    {
        assert($this->assertValues($elements), new TypeError(
            sprintf('Argument 1 has to be an array of %s!', $this->type)
        ));

        return static::intersectArray($elements);
    }

    /**
     * Creates an intersection from the current Vector and the given array and returns the result in a new Vector
     * instance.
     *
     * @param array $elements
     *
     * @return static
     */
    protected function intersectArray(array $elements): self
    {
        return static::create($this->type, array_intersect($this->elements, $elements));
    }

    /**
     * Returns back a Vector containing all element from the current Vector which are present in the given Vector.
     *
     * @param Vector $vector
     *
     * @return static
     */
    public function intersectVector(Vector $vector): self
    {
        assert($this->assertVector($vector), new TypeError(
            sprintf('Argument 1 has to be a Vector with type of %s!', $this->type)
        ));

        return static::intersectArray($vector->elements);
    }

    /**
     * Returns back a Vector containing all element from the current Vector which keys are present in the given array.
     *
     * @param mixed[] $elements
     *
     * @return static
     */
    public function intersectKeys(array $elements): self
    {
        assert($this->assertKeys($elements), new TypeError(
            'Argument 1 ha to be an integer indexed array!'
        ));

        return $this->intersectArrayKeys($elements);
    }

    /**
     * Creates a key intersection  from the current Vector and the given array and returns the result in a new Vector
     * instance.
     *
     * @param array $elements
     *
     * @return static
     */
    protected function intersectArrayKeys(array $elements): self
    {
        return static::create($this->type, array_intersect_key($this->elements, $elements));
    }

    /**
     * Returns back a Vector containing all element from the current Vector which keys are present in the given Vector.
     *
     * @param AbstractVector $vector
     *
     * @return static
     */
    public function intersectVectorKeys(AbstractVector $vector): self
    {
        return $this->intersectArrayKeys($vector->elements);
    }

    /**
     * Replaces the values of the current Vector with the values from the given array on the same keys.
     * Returns back the result in a new Vector instance.
     * If a key from the current Vector exists in the given array, its value will be replaced in the resulting Vector
     * by the value from the given array.
     * If a key exists in the given array, and not the current, it will be created in the resulting Vector.
     * If a key only exists in the current Vector, it will be left as is.
     * The replace is not recursive.
     *
     * @param array $elements
     *
     * @return static
     */
    public function replace(array $elements): self
    {
        assert($this->assertValues($elements), new TypeError(
            sprintf('Argument 1 has to be a Vector with type of %s!', $this->type)
        ));

        return $this->replaceArray($elements);
    }

    /**
     * Replaces the values of the current Vector with the values from the given array on the same keys.
     * Returns back the result in a new Vector instance.
     * If a key from the current Vector exists in the given array, its value will be replaced in the resulting Vector
     * by the value from the given array.
     * If a key exists in the given array, and not the current, it will be created in the resulting Vector.
     * If a key only exists in the current Vector, it will be left as is.
     * The replace is not recursive.
     *
     * @param array $elements
     *
     * @return static
     */
    protected function replaceArray(array $elements): self
    {
        return static::create($this->type, array_replace($this->elements, $elements));
    }

    /**
     * Replaces the values of the current Vector with the values from the given Vector on the same keys.
     * Returns back the result in a new Vector instance.
     * If a key from the current Vector exists in the given Vector, its value will be replaced in the resulting Vector
     * by the value from the given Vector.
     * If a key exists in the given Vector, and not the current, it will be created in the resulting Vector.
     * If a key only exists in the current Vector, it will be left as is.
     * The replace is not recursive.
     *
     * @param Vector $vector
     *
     * @return static
     */
    public function replaceVector(Vector $vector): self
    {
        assert($this->assertVector($vector), new TypeError(
            sprintf('Argument 1 has to be a Vector with type of %s!', $this->type)
        ));

        return $this->replaceArray($vector->elements);
    }

    /**
     * Pushes the given elements to the end of the Vector. Values will be pushed in the order they were passed to the
     * method.
     *
     * @param mixed ...$elements
     *
     * @return static
     */
    public function push(... $elements): self
    {
        assert(
            $this->assertValues($elements),
            new TypeError('All arguments has to be a type of ' . $this->type)
        );

        $items = $this->elements;

        array_push($items, ... $elements);

        return static::create($this->type, $items);
    }

    /**
     * Pushes the given elements to the beginning of he Vector. Values will be pushes one by one from in the order they
     * were passed to the method.
     *
     * @param int[] ...$elements
     *
     * @return static
     */
    public function unshift(... $elements): self
    {
        assert(
            $this->assertValues($elements),
            new TypeError('All arguments has to be a type of ' . $this->type)
        );

        if ($this->isEmpty()) {
            return new static($this->type, array_reverse($elements));
        }

        $firstKey = $this->firstKey() - 1;

        $items = $this->elements + array_combine(range($firstKey, $firstKey - (count($elements) - 1)), $elements);

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
    public function only(array $keys): self
    {
        assert(
            $this->assertKeys(array_flip($keys)),
            new InvalidArgumentException('Argument 1 has to be an array of integers!')
        );

        return static::create($this->type, array_flip($keys))->replaceArray($this->elements);
    }

    /**
     * Returns back every elements from the vector that are not preset in the given keys parameter.
     *
     * @param int[] $keys
     *
     * @return static
     */
    public function except(array $keys): self
    {
        assert(
            $this->assertKeys(array_flip($keys)),
            new InvalidArgumentException('Argument 1 has to be an array of integers!')
        );

        return static::create($this->type, array_flip($keys))->diffArray($this->elements);
    }

    /**
     * Creates an instance of vector using the given elements.
     *
     * @param string $type
     * @param array  $elements
     *
     * @return static
     */
    public static function create(string $type, array $elements = []): self
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
        return in_array($type, self::TYPES, true) || class_exists($type);
    }

    final protected function assertValue($value) : bool
    {
        return $value instanceof $this->type || gettype($value) === $this->type || $value === null;
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
