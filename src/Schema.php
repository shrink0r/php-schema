<?php

namespace Shrink0r\Configr;

use Shrink0r\Configr\Property\AssocProperty;
use Shrink0r\Configr\Property\DynamicProperty;
use Shrink0r\Configr\Property\FqcnProperty;
use Shrink0r\Configr\Property\PropertyInterface;
use Shrink0r\Configr\Property\ScalarProperty;
use Shrink0r\Configr\Property\SequenceProperty;

class Schema implements SchemaInterface
{
    protected $parentProperty;

    protected $properties;

    protected $customTypes;

    public function __construct(
        $name,
        array $schema,
        PropertyInterface $parentProperty = null
    ) {
        $this->customTypes = [];
        $this->type = $schema['type'];
        $this->parentProperty = $parentProperty;

        $customTypes = isset($schema['customTypes']) ? $schema['customTypes'] : [];
        if (is_array($customTypes)) {
            foreach ($customTypes as $name => $definition) {
                $this->customTypes[$name] = new Schema($name, $definition, $parentProperty);
            }
        }
        $properties = isset($schema['properties']) ? $schema['properties'] : [];
        if (is_array($properties)) {
            $this->properties = $this->handleProperties($properties);
        } else {
            throw new \Exception("Missing required key 'properties' within given schema.");
        }
    }

    public function validate(array $config)
    {
        $errors = [];
        foreach ($this->properties as $property) {
            $result = $property->validate($config);
            if ($result instanceof Error) {
                if ($property->getName() === ':any_name:') {
                    $errors = array_merge($errors, $result->unwrap());
                } else {
                    $errors[$property->getName()] = $result->unwrap();
                }
            }
        }

        return empty($errors) ? Ok::unit() : Error::unit($errors);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCustomTypes()
    {
        return $this->customTypes;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    protected function handleProperties(array $propertyDefs)
    {
        $properties = [];
        foreach ($propertyDefs as $propertyName => $propertyDef) {
            if ($property = $this->createProperty($propertyName, $propertyDef)) {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    protected function createProperty($propertyName, array $propertyDef)
    {
        $property = null;
        $propType = $propertyDef['type'];
        unset($propertyDef['type']);

        switch ($propType) {
            case 'scalar':
                $property = new ScalarProperty($this, $propertyName, $propertyDef, $this->parentProperty);
                break;
            case 'dynamic':
                $property = new DynamicProperty($this, $propertyName, $propertyDef, $this->parentProperty);
                break;
            case 'assoc':
                $property = new AssocProperty($this, $propertyName, $propertyDef, $this->parentProperty);
                break;
            case 'sequence':
                $property = new SequenceProperty($this, $propertyName, $propertyDef, $this->parentProperty);
                break;
            case 'fqcn':
                $property = new FqcnProperty($this, $propertyName, $propertyDef, $this->parentProperty);
                break;
            default:
                throw new \Exception("Unsupported prop-type '$propType' given.");
        }

        return $property;
    }
}
