<?php

namespace Shrink0r\Configr;

/**
 * A result indicates the state resulting from a specific operation.
 */
interface ResultInterface
{
    /**
     * Unwrap an operation's return data, that is held by the result.
     *
     * @return mixed
     */
    public function unwrap();

    /**
     * Crate a new result, which holds the given return data.
     *
     * @param mixed $data
     *
     * @return ResultInterface
     */
    public static function unit($data = null);
}
