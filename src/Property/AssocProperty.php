<?php

namespace Shrink0r\PhpSchema\Property;

use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Exception;
use Shrink0r\PhpSchema\ResultInterface;
use Shrink0r\PhpSchema\SchemaInterface;

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
     * @param PropertyInterface $parent If the schema is created by an assoc or sequence prop,
     *                                  this must be the creating parent property.
     */
    public function __construct(SchemaInterface $schema, $name, array $definition, PropertyInterface $parent = null)
    {
        parent::__construct($schema, $name, $definition, $parent);

        $this->valueSchema = $this->createValueSchema($definition);
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

    /**
     * Creates a schema instance that will be used to proxy the property's validation to.
     *
     * @param mixed[] $definition
     *
     * @return SchemaInterface
     */
    protected function createValueSchema(array $definition)
    {
        if (!isset($definition['properties'])) {
            throw new Exception("Missing required key 'properties' within assoc definition.");
        }
        return $this->getSchema()->getFactory()->createSchema(
            $this->getName().'_type',
            [ 'type' => 'assoc', 'properties' => $definition['properties'] ],
            $this
        );
    }
}
