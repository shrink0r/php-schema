<?php

namespace Shrink0r\Configr\Property;

interface PropertyInterface
{
    public function validate($value);

    public function getName();

    public function getParent();

    public function hasParent();

    public function isRequired();

    public function getSchema();
}
