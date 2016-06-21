<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class Property implements PropertyInterface
{
    protected $schema;

    protected $name;

    protected $required;

    protected $parent;

    public function __construct(SchemaInterface $schema, $name, array $definition, PropertyInterface $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->schema = $schema;
        $this->required = isset($definition['required']) ? $definition['required'] : true;
    }

    public function validate($value)
    {
        return Ok::unit();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent()
    {
        return $this->parent instanceof PropertyInterface;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
