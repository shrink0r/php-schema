<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\BoolProperty;
use Shrink0r\Configr\Property\FloatProperty;
use Shrink0r\Configr\Property\IntProperty;
use Shrink0r\Configr\Property\StringProperty;
use Shrink0r\Configr\SchemaInterface;

class SequenceProperty extends EnumProperty
{
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
