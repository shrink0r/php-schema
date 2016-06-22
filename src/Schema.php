<?php

namespace Shrink0r\Configr;

use Shrink0r\Configr\Property\PropertyInterface;
use Shrink0r\Configr\ResultInterface;

/**
 * Default implementation of the SchemaInterface.
 */
class Schema implements SchemaInterface
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var PropertyInterface $parent
     */
    protected $parent;

    /**
     * @var PropertyInterface[] $properties
     */
    protected $properties = [];

    /**
     * @var SchemaInterface[]
     */
    protected $customTypes = [];

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param string $name The name of the schema.
     * @param mixed[] $schema Must contain keys for 'type', 'properties' and 'customTypes'.
     * @param Factory $factory Will be used to create objects while processing the given schema.
     * @param PropertyInterface $parent If created below a prop (assoc, etc.) this will hold that property.
     */
    public function __construct($name, array $schema, FactoryInterface $factory, PropertyInterface $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->factory = $factory;
        $this->type = $schema['type'];

        list($customTypes, $properties) = $this->validateSchema($schema);
        foreach ($customTypes as $name => $definition) {
            $this->customTypes[$name] = $this->factory->createSchema($name, $definition, $parent);
        }
        $this->properties = $this->factory->createProperties($properties, $this, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $data)
    {
        $errors = [];

        $mergeErrors = function (ResultInterface $result) use (&$errors) {
            if ($result instanceof Error) {
                $errors = array_merge($errors, $result->unwrap());
            }
        };

        $mergeErrors($this->validateMappedValues($data));
        $mergeErrors($this->validateAnyValues($data));

        return empty($errors) ? Ok::unit() : Error::unit($errors);
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTypes()
    {
        return $this->customTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}}
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Validates the values of all explictly defined schema properties.
     *
     * @param array $data
     *
     * @return ResultInterface
     */
    protected function validateMappedValues(array $data)
    {
        $errors = [];

        foreach (array_diff_key($this->properties, [ ':any_name:' => 1 ]) as $key => $property) {
            $result = $this->selectValue($property, $data);
            if ($result instanceof Ok) {
                $result = $property->validate($result->unwrap());
            }
            if ($result instanceof Error) {
                $errors[$key] = $result->unwrap();
            }
        }

        return empty($errors) ? Ok::unit() : Error::unit($errors);
    }

    /**
     * If the schema has a property named ':any_name:', this method will validate all keys,
     * that have not been explicitly addressed by the schema.
     *
     * @param mixed[] $data
     *
     * @return ResultInterface
     */
    protected function validateAnyValues(array $data)
    {
        $errors = [];

        if (isset($this->properties[':any_name:'])) {
            foreach (array_diff_key($data, $this->properties) as $key => $value) {
                $result = $this->properties[':any_name:']->validate($value);
                if ($result instanceof Error) {
                    $errors[$key] = $result->unwrap();
                }
            }
        }

        return empty($errors) ? Ok::unit() : Error::unit($errors);
    }

    /**
     * Returns the property's corresponding value from the given data array.
     *
     * @param PropertyInterface $property
     * @param array $data
     *
     * @return ResultInterface If the value does not exist an error is returned; otherwise Ok is returned.
     */
    protected function selectValue(PropertyInterface $property, array $data)
    {
        $errors = [];
        $key = $property->getName();
        $value = isset($data[$key]) ? $data[$key] : null;

        if (is_null($value) && $property->isRequired()) {
            if (!array_key_exists($key, $data)) {
                $errors[] = Error::MISSING_KEY;
            }
            $errors[] = Error::MISSING_VALUE;
        }

        return empty($errors) ? Ok::unit($value) : Error::unit($errors);
    }

    /**
     * Ensures that the given schema has valid values and yields defaults where available.
     *
     * @param mixed[] $schema
     *
     * @return mixed[] Returns the given schema plus defaults where applicable.
     */
    protected function validateSchema(array $schema)
    {
        $customTypes = isset($schema['customTypes']) ? $schema['customTypes'] : [];
        if (!is_array($customTypes)) {
            throw new Exception("Given value for key 'customTypes' is not an array.");
        }

        $properties = isset($schema['properties']) ? $schema['properties'] : null;
        if (!is_array($properties)) {
            throw new Exception("Missing valid value for 'properties' key within given schema.");
        }

        return [ $customTypes, $properties ];
    }
}
