<?php

namespace Shrink0r\Configr;

class SequenceProperty extends Property
{
    protected $allowedSchemes;

    public function __construct(Scheme $scheme, $name, array $definition, PropertyInterface $parentProperty = null)
    {
        $customTypes = $scheme->getCustomTypes();

        $this->allowedSchemes = [];
        foreach ($definition['one_of'] as $allowedType) {
            if (preg_match('/^&/', $allowedType)) {
                $allowedType = ltrim($allowedType, '&');
                if (!isset($customTypes[$allowedType])) {
                    throw new \Exception("Unable to resolve '$allowedType' to a custom type-definition.");
                }
            }
            $this->allowedSchemes[] = $customTypes[$allowedType];
        }
        unset($definition['one_of']);

        parent::__construct($scheme, $name, $definition, $parentProperty);
    }

    protected function validateValue($value)
    {
        $errors = [];
        foreach ($value as $item) {
            foreach ($this->allowedSchemes as $scheme) {
                $errors = $scheme->validate($item);
                if (empty($errors)) {
                    break;
                }
            }
        }

        return $errors;
    }
}
