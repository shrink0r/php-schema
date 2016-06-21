<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;

class IntProperty extends Property
{
    /**
     * Tells if a given value is a valid int.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value)
    {
        return is_int($value) ? Ok::unit() : Error::unit([ Error::NON_INT ]);
    }
}