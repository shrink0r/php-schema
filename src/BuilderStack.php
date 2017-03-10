<?php

namespace Shrink0r\PhpSchema;

/**
 * Helper class to allow Builders to be implemented without circular
 * references. Represents the current nesting produced using $builder->child
 * accesses.
 */
class BuilderStack implements BuilderInterface, \ArrayAccess
{
    /**
     * @var BuilderInterface[]
     */
    protected $builders;

    /**
     * @param array $builders Must be contiguous start with index 0.
     */
    public function __construct(array $builders)
    {
        if (count($builders) === 0) {
            throw new Exception('BuilderStack can not be empty');
        }
        $this->builders = $builders;
    }

    /**
     * @return BuilderInterface
     */
    protected function first()
    {
        return $this->builders[0];
    }

    /**
     * @return BuilderInterface
     */
    protected function last()
    {
        return $this->builders[count($this->builders) - 1];
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $defaults = [])
    {
        return $this->first()->build($defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return $this->builders[0];
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        if (count($this->builders) === 1) {
            return $this->builders[0];
        }
        return new static(array_slice($this->builders, 0, -1));
    }

    /**
     * {@inheritdoc}
     */
    public function valueOf($key)
    {
        return $this->last()->valueOf($key);
    }

    /**
     * Tells if the given key exists in the builder at the top of the stack.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->last()[$key]);
    }

    /**
     * Navigate to the given key, creating it along the way if it does not yet exist.
     *
     * @param string $key
     *
     * @return BuilderInterface Returns self
     */
    public function offsetGet($key)
    {
        return $this->last()[$key];
    }

    /**
     * Assign a given value to the given key to the builder at the top the stack.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function offsetSet($key, $value)
    {
        $this->last()[$key] = $value;

        return $this;
    }

    /**
     * Unset the given key in the builder at the top of the stack.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        unset($this->last()[$key]);
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
        $value = $this->last()->{$key};
        if ($value instanceof static) {
            if ($this->last() !== $value->first()) {
                throw new Exception('Trying to merge incompatible BuilderStacks');
            }
            return new static(array_merge($this->builders, array_slice($value->builders, 1)));
        } else {
            return new static(array_merge($this->builders, [$value]));
        }
    }

    /**
     * Assign a given value to the builder at the top of builder the stack.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->last()->{$key} = $value;
    }

    /**
     * Tells if the given key exists in the builder at the top of the stack.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->last()->{$key});
    }

    /**
     * Unset the given key in the builder at the top of the stack.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->last()->{$key});
    }

    /**
     * Assign a given value to the given key to the builder at the top of the stack.
     *
     * @param string $key
     * @param mixed[] $args
     *
     * @return $this
     */
    public function __call($key, array $args = [])
    {
        $this->last()->{$key}(...$args);
        return $this;
    }
}
