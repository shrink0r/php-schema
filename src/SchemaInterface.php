<?php

namespace Shrink0r\Configr;

interface SchemaInterface
{
    public function validate(array $config);

    public function getType();

    public function getCustomTypes();

    public function getProperties();
}
