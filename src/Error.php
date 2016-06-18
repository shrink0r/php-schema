<?php

namespace Shrink0r\Configr;

class Error implements ResultInterface
{
    const MISSING_KEY = 'missing_key';

    const MISSING_VALUE = 'missing_value';

    const NON_SCALAR = 'non_scalar';

    const CLASS_NOT_EXISTS = 'class_not_exists';

    private $errors;

    public static function unit($errors = null)
    {
        $class = static::class;

        return new $class($errors ?: []);
    }

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function unwrap()
    {
        return $this->errors;
    }
}
