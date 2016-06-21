<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\PropertyInterface;
use Shrink0r\Configr\SchemaInterface;

class ChoiceProperty extends Property
{
    protected $choices;

    public function __construct(
        SchemaInterface $schema,
        $name,
        array $definition,
        PropertyInterface $parentProperty = null
    ) {
        $this->choices = isset($definition['one_of']) ? $definition['one_of'] : [];
        unset($definition['one_of']);

        parent::__construct($schema, $name, $definition, $parentProperty);
    }

    /**
     * Tells whether a given value is a valid choice according to the property definition.
     *
     * @param mixed $value
     *
     * @return ResultInterface
     */
    protected function validateValue($value)
    {
        return in_array($value, $this->choices) ? Ok::unit() : Error::unit([ Error::INVALID_CHOICE ]);
    }
}
