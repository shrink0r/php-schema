<?php

namespace Shrink0r\Configr;

/**
 * Default implementation of the BuilderInterface.
 */
class Builder implements BuilderInterface
{
    /**
     * @var mixed[] $data The builder's data
     */
    protected $data;

    /**
     * @var string[] $valuePath Holds the keys leading to the current data slice.
     */
    protected $valuePath;

    /**
     * @var mixed[] $valuePtr Holds a reference to a slice of the data.
     */
    protected $valuePtr;

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
        $this->valuePath = [];
        $this->schema = $schema;
        $this->valuePtr = &$this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $defaults = [])
    {
        $builtConfig = array_replace_recursive($defaults, $this->data);
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
        $value = isset($this->valuePtr[$key]) ? $this->valuePtr[$key] : null;
        $this->rewind();

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        $valuePath = $this->valuePath;

        $this->rewind();

        array_pop($valuePath);
        while (!empty($valuePath)) {
            $curPath = array_shift($valuePath);
            $this->{$curPath};
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->valuePath = [];
        $this->valuePtr = &$this->data;

        return $this;
    }

    /**
     * Tells if the given key exists relative to the builder's current position.
     * Rewinds the builder.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        $exists = isset($this->valuePtr[$key]);
        $this->rewind();

        return $exists;
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
        return $this->{$key};
    }

    /**
     * Assign a given value to the given key relative to the buider's current position.
     * Rewinds the builder afterwards, so any proceeding accesses must start from root again.
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->{$key} = $value;

        return $this;
    }

    /**
     * Unset the given key relative to the buider's current position.
     * Rewinds the builder afterwards, so any proceeding accesses must start from root again.
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetUnset($key)
    {
        if (isset($this->valuePtr[$key])) {
            unset($this->valuePtr[$key]);
        }
        $this->rewind();
    }

    /**
     * Navigate to the given key, creating it along the way if it does not yet exist.
     *
     * @param string $key
     *
     * @return BuilderInterface Returns self
     */
    public function __get($key)
    {
        if (!isset($this->valuePtr[$key])) {
            $this->valuePtr[$key] = [];
        }
        $this->valuePath[] = $key;
        $this->valuePtr = &$this->valuePtr[$key];

        return $this;
    }

    /**
     * Assign a given value to the given key relative to the buider's current position.
     * Rewinds the builder afterwards, so any proceeding accesses must start from root again.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->valuePtr[$key] = $value;
        $this->rewind();
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
            $this->valuePtr[$key] = $args[0];
        }

        return $this;
    }
}
