<?php

namespace Shrink0r\Configr;

/**
 * Indicates the successfull execution of an operation; may wrap return data.
 */
class Ok implements ResultInterface
{
    /**
     *  @var array $data
     */
    private $data;

    /**
     * Creates a new Ok instance, which holds the given return data.
     *
     * @param array $data
     *
     * @return Ok
     */
    public static function unit($data = null)
    {
        $class = static::class;

        return new $class($data ?: []);
    }

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Unwrap the return data, held by an Ok instance.
     *
     * @return array
     */
    public function unwrap()
    {
        return $this->data;
    }
}
