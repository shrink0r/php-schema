<?php

namespace Shrink0r\Configr;

class ScalarProperty extends Property
{
    protected function validateValue($value)
    {
        $errors = [];

        if (!is_scalar($value)) {
            $errors[] = "non_scalar";
        }

        return $errors;
    }
}
