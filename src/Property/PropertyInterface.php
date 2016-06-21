<?php

namespace Shrink0r\Configr\Property;

interface PropertyInterface
{
    /**
     * Tells if a given value is valid according to the property's specifics.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value);

    /**
     * Returns the property's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the property's parent property, in case it's part of a nested schema.
     *
     * @return PropertyInterface Returns null, if the schema is the root schema, hence has no parent property.
     */
    public function getParent();

    /**
     * Tells if the schema has a parent property, that it was created by.
     *
     * @return bool
     */
    public function hasParent();

    /**
     * Tells if the property must occur within the data being validated.
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Returns the property's root schema.
     *
     * @return SchemaInterface
     */
    public function getSchema();
}
