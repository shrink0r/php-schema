<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Schema;
use Shrink0r\Configr\SchemaInterface;

class AssocProperty extends Property
{
    /**
     * @var SchemaInterface $valueSchema
     */
    protected $valueSchema;

    /**
     * @param SchemaInterface $schema The schema that the property is part of.
     * @param string $name The name of the schema.
     * @param mixed[] $definition Must contain a key named 'properties' that defines a schema,
     *                            which the property will proxy validation to.
     * @param PropertyInterface $parentProperty If the schema is created by an assoc or sequence prop,
     *                                          this must be the creating parent property.
     */
    public function __construct(
        SchemaInterface $schema,
        $name,
        array $definition,
        PropertyInterface $parentProperty = null
    ) {
        $this->valueSchema = new Schema(
            $name.'_type',
            [ 'type' => 'assoc', 'properties' => $definition['properties'] ],
            $parentProperty
        );
        unset($definition['properties']);

        parent::__construct($schema, $name, $definition, $parentProperty);
    }

    /**
     * Tells if a given value adhere's to the property's value schema.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value)
    {
        return is_array($value) ? $this->valueSchema->validate($value) : Error::unit([ Error::NON_ARRAY ]);
    }
}
