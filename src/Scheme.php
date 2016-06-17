<?php

namespace Shrink0r\Configr;

use Shrink0r\Monatic\Maybe;

class Scheme implements SchemeInterface
{
    protected $parentProperty;

    protected $properties;

    protected $customTypes;

    public function __construct(
        $name,
        array $scheme,
        PropertyInterface $parentProperty = null
    ) {
        $this->customTypes = [];
        $this->type = $scheme['type'];
        $this->parentProperty = $parentProperty;

        $customTypes = isset($scheme['customTypes']) ? $scheme['customTypes'] : [];
        if (is_array($customTypes)) {
            foreach ($customTypes as $name => $definition) {
                $this->customTypes[$name] = new Scheme($name, $definition, $parentProperty);
            }
        }
        $properties = isset($scheme['properties']) ? $scheme['properties'] : [];
        if (is_array($properties)) {
            $this->properties = $this->handleProperties($properties);
        } else {
            throw new \Exception("Missing required key 'properties' within given scheme.");
        }
    }

    public function validate(array $config)
    {
        $errors = [];
        foreach ($this->properties as $property) {
            $propErrors = $property->validate($config);
            if (!empty($propErrors)) {
                $errors[$property->getName()] = $propErrors;
            }
        }

        return $errors;
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
