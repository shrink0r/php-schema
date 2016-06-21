<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;

class BoolProperty extends Property
{
    /**
     * Tells whether a given value is a valid bool.
     *
     * @param mixed $value
     *
     * @return ResultInterface
     */
    public function validate($value)
    {
        return is_bool($value) ? Ok::unit() : Error::unit([ Error::NON_BOOL ]);
    }
}
