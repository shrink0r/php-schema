<?php

namespace Shrink0r\Configr;

use ArrayAccess;

interface BuilderInterface extends ArrayAccess
{
    public function build(array $defaults = []);
}
