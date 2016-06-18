<?php

namespace Shrink0r\Configr;

class FqcnProperty extends Property
{
    protected function validateValue($value)
    {
        $errors = [];

        if (!class_exists($value)) {
            $errors[] = "class_not_exists";
        }

        return $errors;
    }
}
