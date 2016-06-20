<?php

namespace Shrink0r\Configr;

class Ok implements ResultInterface
{
    private $data;

    public static function unit($value = null)
    {
        $class = static::class;

        return new $class($value ?: []);
    }

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function unwrap()
    {
        return $this->data;
    }
}
