<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;

class FqcnProperty extends Property
{
    /**
     * Tells whether a given value is a fully qualified class name of an existing class.
     *
     * @param mixed $value
     *
     * @return ResultInterface
     */
    protected function validateValue($value)
    {
        return class_exists($value) || interface_exists($value) ? Ok::unit() : Error::unit([ Error::CLASS_NOT_EXISTS ]);
    }
}
