<?php

namespace Shrink0r\PhpSchema;

/**
 * Default implementation of the BuilderInterface.
 */
class Builder implements BuilderInterface, \ArrayAccess
{
    /**
     * @var mixed[] $data The builder's data
     */
    protected $data;

    /**
     * @var SchemaInterface $schema
     */
    protected $schema;

    /**
     * @param SchemaInterface $schema Schema to validate against when building
     */
    public function __construct(SchemaInterface $schema = null)
    {
        $this->data = [];
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $defaults = [])
    {
        $builtConfig = $defaults;
        foreach($this->data as $key => $value) {
            $builtConfig[$key] = $value instanceof BuilderInterface
                ? $value->build(isset($defaults[$key]) ? $defaults[$key] : [])->unwrap()
                : $value;
        }

        if (!$this->schema) {
            return Ok::unit($builtConfig);
        }

        $validationResult = $this->schema->validate($builtConfig);
        return $validationResult instanceof Error ? $validationResult : Ok::unit($builtConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function valueOf($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return $this;
    }

    /**
     * Tells if the given key exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->{$key});
    }

    /**
     * Navigate to the given key, creating it along the way if it does not yet exist.
     *
     * @param string $key
     *
     * @return BuilderInterface The nested BuilderInterface at $key
     */
    public function offsetGet($key)
    {
        return $this->{$key};
    }

    /**
     * Assign a given value to the given key.
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->{$key} = $value;
    }

    /**
     * Unset the given key.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        unset($this->{$key});
    }

    /**
     * Navigate to the given key, creating it along the way if it does not yet exist.
     *
     * @param string $key
     *
     * @return BuilderInterface Returns a new BuilderStack instance representing
     *                          the current access path.
     */
    public function __get($key)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = new Builder();
        }

        if (!$this->data[$key] instanceof BuilderInterface) {
            throw new Exception("Can not access scalar value at '$key' with accessor. Use valueOf() instead");
        }

        return new BuilderStack([$this, $this->data[$key]]);
    }

    /**
     * Assign a given value to the given key. If an array is given it is
     * recursively converted to Builder objects. BuilderInterface instances
     * can be used to insert another Builder in the given location.
     * Changing the value from a child Builder to another value or other way
     * around is not possible.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if (is_array($value)) {
            $builder = new Builder();
            foreach($value as $k => $v) {
                $builder->{$k} = $v;
            }
            $value = $builder;
        }

        if (isset($this->data[$key])) {
            $valueIsBuilder = $value instanceof BuilderInterface;
            $dataIsBuilder = $this->data[$key] instanceof BuilderInterface;
            if($dataIsBuilder !== $valueIsBuilder) {
                throw new Exception("Trying to overwrite value at '$key' with incompatible data");
            }
        }
        $this->data[$key] = $value;
    }

    /**
     * Tells if the given key exists.
     *
     * @param string $key
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Unset the given key.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Assign a given value to the given key relative to the buider's current position.
     * Does not rewind the builder and returns self, so fluent assignment of values is possible.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return BuilderInterface Returns self
     */
    public function __call($key, array $args = [])
    {
        if (count($args) !== 0) {
            $this->{$key} = $args[0];
        }

        return $this;
    }
}
