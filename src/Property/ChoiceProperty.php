<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class ChoiceProperty extends Property
{
    /**
     * @param mixed[] $choices
     */
    protected $choices;

    /**
     * @param SchemaInterface $schema The schema that the property is part of.
     * @param string $name The name of the schema.
     * @param mixed[] $definition Must contain a key named 'one_of' containing the values,
     *                            that will be allowed to pass the property's validation.
     * @param PropertyInterface $parent If the schema is created by an assoc or sequence prop,
     *                                  this must be the creating parent property.
     */
    public function __construct(SchemaInterface $schema, $name, array $definition, PropertyInterface $parent = null)
    {
        parent::__construct($schema, $name, $definition, $parent);

        $this->choices = isset($definition['one_of']) ? $definition['one_of'] : [];
    }

    /**
     * Tells if a given value is a valid choice according to the property's definition.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value)
    {
        return in_array($value, $this->choices) ? Ok::unit() : Error::unit([ Error::INVALID_CHOICE ]);
    }
}
