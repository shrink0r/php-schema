<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;

class FloatProperty extends Property
{
    /**
     * Tells whether a given value is a valid float.
     *
     * @param mixed $value
     *
     * @return ResultInterface
     */
    protected function validateValue($value)
    {
        return is_float($value) ? Ok::unit() : Error::unit([ Error::NON_FLOAT ]);
    }
}
