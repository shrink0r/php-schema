<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class Property implements PropertyInterface
{
    /**
     * @var SchemaInterface $schema
     */
    protected $schema;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var bool $required
     */
    protected $required;

    /**
     * @var PropertyInterface $parent
     */
    protected $parent;

    /**
     * @param SchemaInterface $schema The schema that the property is part of.
     * @param string $name The name of the schema.
     * @param mixed[] $definition May contain the key 'required', if omitted will default to true.
     * @param PropertyInterface $parent If the schema is created by an assoc or sequence prop,
     *                                          this must be the creating parent property.
     */
    public function __construct(SchemaInterface $schema, $name, array $definition, PropertyInterface $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->schema = $schema;
        $this->required = isset($definition['required']) ? $definition['required'] : true;
    }

    /**
     * Validation stub that always returns ok.
     *
     * @param mixed $value
     *
     * @return ResultInterface Always returns Ok.
     */
    public function validate($value)
    {
        return Ok::unit();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return $this->parent instanceof PropertyInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }
}
