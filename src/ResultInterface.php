<?php

namespace Shrink0r\Configr;

interface ResultInterface
{
    public function unwrap();

    public static function unit($value = null);
}
