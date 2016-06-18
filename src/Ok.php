<?php

namespace Shrink0r\Configr;

class Ok implements ResultInterface
{
    private $config;

    public static function unit($value = null)
    {
        $class = static::class;

        return new $class($value ?: []);
    }

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function unwrap()
    {
        return $this->config;
    }
}
