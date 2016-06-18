<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;

class FqcnProperty extends Property
{
    protected function validateValue($value)
    {
        return class_exists($value) || interface_exists($value) ? Ok::unit() : Error::unit([ "class_not_exists" ]);
    }
}
