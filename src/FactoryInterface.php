<?php

namespace Shrink0r\PhpSchema;

use Shrink0r\PhpSchema\Property\PropertyInterface;

interface FactoryInterface
{
    /**
     * Creates a new schema instance from the given name and definition.
     *
     * @param string $name
     * @param mixed[] $definition
     * @param PropertyInterface $parent
     *
     * @return SchemaInterface
     */
    public function createSchema($name, array $definition, PropertyInterface $parent = null);

    /**
     * Creates an array of properties from the given map of property definitions.
     *
     * @param mixed[] $definitions
     * @param SchemaInterface $schema
     * @param PropertyInterface $parent
     *
     * @return PropertyInterface[] An array of properties where the property names are used as corresponding keys.
     */
    public function createProperties(array $definitions, SchemaInterface $schema, PropertyInterface $parent = null);

    /**
     * Creates a property from the given definition.
     *
     * @param mixed[] $definition
     * @param SchemaInterface $schema
     * @param PropertyInterface $parent
     *
     * @return PropertyInterface
     */
    public function createProperty(array $definition, SchemaInterface $schema, PropertyInterface $parent = null);
}
