<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;

class IntProperty extends Property
{
    /**
     * Tells whether a given value is a valid integer.
     *
     * @param mixed $value
     *
     * @return ResultInterface
     */
    public function validate($value)
    {
        return is_int($value) ? Ok::unit() : Error::unit([ Error::NON_INT ]);
    }
}
