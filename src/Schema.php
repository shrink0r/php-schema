<?php

namespace Shrink0r\Configr;

use Shrink0r\Configr\Property\AssocProperty;
use Shrink0r\Configr\Property\BoolProperty;
use Shrink0r\Configr\Property\ChoiceProperty;
use Shrink0r\Configr\Property\EnumProperty;
use Shrink0r\Configr\Property\FloatProperty;
use Shrink0r\Configr\Property\FqcnProperty;
use Shrink0r\Configr\Property\IntProperty;
use Shrink0r\Configr\Property\Property;
use Shrink0r\Configr\Property\PropertyInterface;
use Shrink0r\Configr\Property\ScalarProperty;
use Shrink0r\Configr\Property\SequenceProperty;
use Shrink0r\Configr\Property\StringProperty;

/**
 * Default implementation of the SchemaInterface.
 */
class Schema implements SchemaInterface
{
    /**
     * @var PropertyInterface $parentProperty
     */
    protected $parentProperty;

    /**
     * @var PropertyInterface[] $properties
     */
    protected $properties = [];

    /**
     * @var SchemaInterface[]
     */
    protected $customTypes = [];

    /**
     * @param string $name The name of the schema.
     * @param mixed[] $schema The schema definition.
     * @param PropertyInterface $parentProperty If created below a prop (assoc, etc.) this will hold that property.
     */
    public function __construct(
        $name,
        array $schema,
        PropertyInterface $parentProperty = null
    ) {
        $this->type = $schema['type'];
        $this->parentProperty = $parentProperty;

        $customTypes = isset($schema['customTypes']) ? $schema['customTypes'] : [];
        if (is_array($customTypes)) {
            foreach ($customTypes as $name => $definition) {
                $this->customTypes[$name] = new Schema($name, $definition, $parentProperty);
            }
        }

        $properties = isset($schema['properties']) ? $schema['properties'] : null;
        if (is_array($properties)) {
            foreach ($properties as $name => $definition) {
                $property = $this->createProperty($name, $definition);
                $this->properties[$property->getName()] = $property;
            }
        } else {
            throw new Exception("Missing valid value for 'properties' key within given schema.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $data)
    {
        $errors = [];
        foreach ($this->properties as $property) {
            $propName = $property->getName();
            if ($propName === ':any_name:') {
                continue;
            }
            if (!array_key_exists($propName, $data) && $property->isRequired()) {
                $errors[$propName] = [ Error::MISSING_KEY ];
                continue;
            }
            $value = isset($data[$propName]) ? $data[$propName] : null;
            if (is_null($value)) {
                if ($property->isRequired()) {
                    $errors[$propName] = [ Error::MISSING_VALUE ];
                }
                continue;
            }
            $result = $property->validate($value);
            if ($result instanceof Error) {
                $errors[$propName] = $result->unwrap();
            }
        }
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
     * Create a property from the give property definition.
     *
     * @param string $name
     * @param mixed[] $definition
     *
     * @return PropertyInterface
     */
    protected function createProperty($name, array $definition)
    {
        $type = $definition['type'];
        unset($definition['type']);

        switch ($type) {
            case 'scalar':
                $property = new ScalarProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'bool':
                $property = new BoolProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'string':
                $property = new StringProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'int':
                $property = new IntProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'float':
                $property = new FloatProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'any':
                $property = new Property($this, $name, $definition, $this->parentProperty);
                break;
            case 'assoc':
                $property = new AssocProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'sequence':
                $property = new SequenceProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'fqcn':
                $property = new FqcnProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'enum':
                $property = new EnumProperty($this, $name, $definition, $this->parentProperty);
                break;
            case 'choice':
                $property = new ChoiceProperty($this, $name, $definition, $this->parentProperty);
                break;
            default:
                throw new Exception("Unsupported property-type '$type' given.");
        }

        return $property;
    }
}
