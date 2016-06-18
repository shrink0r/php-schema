<?php

namespace Shrink0r\Configr;

class Error implements ResultInterface
{
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
