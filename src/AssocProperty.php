<?php

namespace Shrink0r\Configr;

class AssocProperty extends Property
{
    protected $childScheme;

    public function __construct(Scheme $scheme, $name, array $definition, PropertyInterface $parentProperty = null)
    {
        $this->childScheme = new Scheme(
            $name.'_type',
            [
                'type' => 'assoc',
                'properties' => $definition['properties']
            ],
            $parentProperty
        );
        unset($definition['properties']);

        parent::__construct($scheme, $name, $definition, $parentProperty);
    }

    protected function validateValue($value)
    {
        return $this->childScheme->validate($value);
    }
}
