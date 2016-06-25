<?php

namespace Shrink0r\PhpSchema\Property;

use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;

class FqcnProperty extends Property
{
    /**
     * Tells if a given value is a fully qualified class name of an existing php class.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value)
    {
        return class_exists($value) || interface_exists($value) ? Ok::unit() : Error::unit([ Error::CLASS_NOT_EXISTS ]);
    }
}
