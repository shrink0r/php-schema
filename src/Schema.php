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
     * @var array $properties An array of PropertyInterface
     */
    protected $properties = [];

    /**
     * @var array $customTypes An array of SchemaInterface
     */
    protected $customTypes = [];

    /**
     * @param string $name The name of the schema.
     * @param array $schema The schema definition.
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
                $this->properties[] = $this->createProperty($name, $definition);
            }
        } else {
            throw new Exception("Missing valid value for 'properties' key within given schema.");
        }
    }

    /**
     * {@inheritdoc}
     */
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
     * @param array $definition
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
