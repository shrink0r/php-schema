<?php

namespace Shrink0r\Configr;

class AssocProperty extends Property
{
    protected $childSchema;

    public function __construct(
        SchemaInterface $schema,
        $name,
        array $definition,
        PropertyInterface $parentProperty = null
    ) {
        $this->childSchema = new Schema(
            $name.'_type',
            [
                'type' => 'assoc',
                'properties' => $definition['properties']
            ],
            $parentProperty
        );
        unset($definition['properties']);

        parent::__construct($schema, $name, $definition, $parentProperty);
    }

    protected function validateValue($value)
    {
        return $this->childSchema->validate($value);
    }
}
