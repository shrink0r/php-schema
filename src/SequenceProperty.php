<?php

namespace Shrink0r\Configr;

class SequenceProperty extends Property
{
    protected $allowedSchemas;

    public function __construct(
        SchemaInterface $schema,
        $name,
        array $definition,
        PropertyInterface $parentProperty = null
    ) {
        $customTypes = $schema->getCustomTypes();

        $this->allowedSchemas = [];
        foreach ($definition['one_of'] as $allowedType) {
            if (preg_match('/^&/', $allowedType)) {
                $allowedType = ltrim($allowedType, '&');
                if (!isset($customTypes[$allowedType])) {
                    throw new \Exception("Unable to resolve '$allowedType' to a custom type-definition.");
                }
            }
            $this->allowedSchemas[] = $customTypes[$allowedType];
        }
        unset($definition['one_of']);

        parent::__construct($schema, $name, $definition, $parentProperty);
    }

    protected function validateValue($value)
    {
        $errors = [];
        foreach ($value as $item) {
            foreach ($this->allowedSchemas as $schema) {
                $errors = $schema->validate($item);
                if (empty($errors)) {
                    break;
                }
            }
        }

        return $errors;
    }
}
