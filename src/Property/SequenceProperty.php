<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class SequenceProperty extends Property
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
        $errors = [];

        foreach ($value as $pos => $item) {
            $schemaMatched = false;
            for ($n = 0; $n < count($this->allowedTypes) && !$schemaMatched; $n++) {
                $allowedType = $this->allowedTypes[$n];
                $itemErrors = [];
                if (preg_match('/^&/', $allowedType)) {
                    $schema = $this->getCustomType($allowedType);
                    $result = $schema->validate($item);
                    if ($result instanceof Error) {
                        $itemErrors = $result->unwrap();
                    } else {
                        $schemaMatched = true;
                    }
                } else {
                    $propName = $this->getName().'_item';
                    $itemProperty = $this->createProperty(
                        $propName,
                        [ 'type' => $allowedType, 'required' => true ]
                    );
                    $result = $itemProperty->validate([ $propName => $item ]);
                    if ($result instanceof Error) {
                        $itemErrors = $result->unwrap();
                    } else {
                        $schemaMatched = true;
                    }
                }
            }
            if (!$schemaMatched) {
                $errors[$pos] = $itemErrors;
            }
        }

        return empty($errors) ? Ok::unit() : Error::unit($errors);
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
            case 'any':
                $property = new Property($this->schema, $name, $definition, $this);
                break;
            case 'fqcn':
                $property = new FqcnProperty($this->schema, $name, $definition, $this);
                break;
            default:
                throw new Exception("Unsupported prop-type '$type' given.");
        }

        return $property;
    }
}
