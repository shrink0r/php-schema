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

    protected function validateValue($value)
    {
        $schemaMatched = false;

        for ($n = 0; $n < count($this->allowedTypes) && !$schemaMatched; $n++) {
            $allowedType = $this->allowedTypes[$n];
            $errors = [];
            if (preg_match('/^&/', $allowedType)) {
                $schema = $this->getCustomType($allowedType);
                $result = $schema->validate($value);
                if ($result instanceof Ok) {
                    $schemaMatched = true;
                } else {
                    $errors = $result->unwrap();
                }
            } else {
                $propName = $this->getName().'_item';
                $valueProperty = $this->createProperty(
                    $propName,
                    [ 'type' => $allowedType, 'required' => true ]
                );
                $result = $valueProperty->validate([ $propName => $value ]);
                if ($result instanceof Ok) {
                    $schemaMatched = true;
                } else {
                    $errors = $result->unwrap();
                }
            }
        }

        return $schemaMatched ? Ok::unit() : Error::unit($errors);
    }

    protected function getCustomType($typeName)
    {
        $customTypes = $this->schema->getCustomTypes();
        $typeName = ltrim($typeName, '&');
        if (!isset($customTypes[$typeName])) {
            throw new Exception("Unable to resolve '$typeName' to a custom type-definition.");
        }

        return $customTypes[$typeName];
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
