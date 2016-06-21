<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\BoolProperty;
use Shrink0r\Configr\Property\FloatProperty;
use Shrink0r\Configr\Property\IntProperty;
use Shrink0r\Configr\Property\StringProperty;
use Shrink0r\Configr\SchemaInterface;

class EnumProperty extends Property
{
    protected $allowedTypes;

    public function __construct(
        SchemaInterface $schema,
        $name,
        array $definition,
        PropertyInterface $parentProperty = null
    ) {
        $this->allowedTypes = isset($definition['one_of']) ? $definition['one_of'] : [];
        unset($definition['one_of']);

        parent::__construct($schema, $name, $definition, $parentProperty);
    }

    public function validate($value)
    {
        foreach ($this->allowedTypes as $type) {
            $subject = preg_match('/^&/', $type)
                ? $this->getCustomType($type)
                : $this->createProperty(
                    $this->getName().'_item',
                    [ 'type' => $type, 'required' => true ]
                );
            $result = $subject->validate($value);
            if ($result instanceof Ok) {
                return $result;
            }
        }

        return $result;
    }

    protected function getCustomType($typeName)
    {
        $customTypes = $this->schema->getCustomTypes();
        $typeName = ltrim($typeName, '&');

        if (isset($customTypes[$typeName])) {
            return $customTypes[$typeName];
        }

        throw new Exception("Unable to resolve '$typeName' to a custom type-definition.");
    }

    protected function createProperty($name, array $definition)
    {
        $type = $definition['type'];
        unset($definition['type']);

        switch ($type) {
            case 'scalar':
                $property = new ScalarProperty($this->schema, $name, $definition, $this);
                break;
            case 'bool':
                $property = new BoolProperty($this->schema, $name, $definition, $this);
                break;
            case 'string':
                $property = new StringProperty($this->schema, $name, $definition, $this);
                break;
            case 'int':
                $property = new IntProperty($this->schema, $name, $definition, $this);
                break;
            case 'float':
                $property = new FloatProperty($this->schema, $name, $definition, $this);
                break;
            case 'any':
                $property = new Property($this->schema, $name, $definition, $this);
                break;
            case 'fqcn':
                $property = new FqcnProperty($this->schema, $name, $definition, $this);
                break;
            default:
                throw new Exception("Unsupported property-type '$type' given.");
        }

        return $property;
    }
}
