<?php

namespace Shrink0r\PhpSchema\Property;

use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\ResultInterface;

class StringProperty extends Property
{
    /**
     * Tells if a given value is a valid string.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value)
    {
        return is_string($value) ? Ok::unit() : Error::unit([ Error::NON_STRING ]);
    }
}
