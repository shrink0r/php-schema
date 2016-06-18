<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;

class ScalarProperty extends Property
{
    protected function validateValue($value)
    {
        return is_scalar($value) ? Ok::unit() : Error::unit([ Error::NON_SCALAR ]);
    }
}
