<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class SequenceProperty extends EnumProperty
{
    /**
     * Tells if a given array's items adhere to any of the property's allowed types.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value)
    {
        if (!is_array($value)) {
            return Error::unit([ Error::NON_ARRAY ]);
        }

        $errors = [];
        foreach ($value as $pos => $item) {
            $result = parent::validate($item);
            if ($result instanceof Error) {
                $errors[$pos] = $result->unwrap();
            }
        }

        return empty($errors) ? Ok::unit() : Error::unit($errors);
    }
}
