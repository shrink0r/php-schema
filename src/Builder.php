<?php

namespace Shrink0r\Configr;

class Builder implements BuilderInterface
{
    protected $data;

    protected $valuePath;

    protected $valuePtr;

    protected $schema;

    public function __construct(SchemaInterface $schema = null)
    {
        $this->data = [];
        $this->valuePath = [];
        $this->schema = $schema;
        $this->valuePtr = &$this->data;
    }

    public function build(array $defaults = [])
    {
        $builtConfig = array_replace_recursive($defaults, $this->data);

        if ($this->schema) {
            $validationResult = $this->schema->validate($builtConfig);
            if ($validationResult instanceof Error) {
                $result = $validationResult;
            } else {
                $result = Ok::unit($builtConfig);
            }
        } else {
            $result = Ok::unit($builtConfig);
        }

        return $result;
    }

    public function valueOf($key)
    {
        return isset($this->valuePtr[$key]) ? $this->valuePtr[$key] : null;
    }

    public function __get($key)
    {
        if (!isset($this->valuePtr[$key])) {
            $this->valuePtr[$key] = [];
        }
        $this->valuePath[] = $key;
        $this->valuePtr = &$this->valuePtr[$key];

        return $this;
    }

    public function offsetExists($key)
    {
        return isset($this->valuePtr[$key]);
    }

    public function offsetGet($key)
    {
        return $this->{$key};
    }

    public function offsetSet($key, $value)
    {
        $this->{$key} = $value;
    }

    public function offsetUnset($key)
    {
        if (isset($this->valuePtr[$key])) {
            unset($this->valuePtr[$key]);
        }
    }

    public function __set($key, $value)
    {
        $this->valuePtr[$key] = $value;
        $this->rewind();
    }

    public function __call($key, array $args = [])
    {
        if (count($args) !== 0) {
            $this->valuePtr[$key] = $args[0];
        }

        return $this;
    }

    public function popPath()
    {
        $valuePath = $this->valuePath;
        $valuePtr = $this->valuePtr;
        $this->rewind();

        array_pop($valuePath);
        while (!empty($valuePath)) {
            $curPath = array_shift($valuePath);
            $this->{$curPath};
        }

        return $this;
    }

    public function rewind()
    {
        $this->valuePath = [];
        $this->valuePtr = &$this->data;
    }
}
