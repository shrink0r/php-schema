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
            foreach ($this->allowedTypes as $allowedType) {
                if (preg_match('/^&/', $allowedType)) {
                    $schema = $this->getCustomType($allowedType);
                    $result = $schema->validate($item);
                    if ($result instanceof Error) {
                        $errors["@$pos"] = $result->unwrap();
                    }
                } else {
                    switch ($allowedType) {
                        case 'scalar':
                            if (!is_scalar($item)) {
                                $errors["@$pos"] = 'non_scalar';
                            }
                            break;
                        default:
                            throw new Exception("Unsupported type given to sequence 'one_of'.");
                    }
                }
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
}
