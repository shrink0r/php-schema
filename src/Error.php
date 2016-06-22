<?php

namespace Shrink0r\Configr;

/**
 * Indicates that one or more errors occured during the execution of an operation.
 */
class Error implements ResultInterface
{
    /**
     * @const MISSING_KEY Indicates a missing array-key
     */
    const MISSING_KEY = 'missing_key';

    /**
     * @const MISSING_VALUE Indicates a missing array-value
     */
    const MISSING_VALUE = 'missing_value';

    /**
     * @const NON_SCALAR Indicates that an expected scalar value is something different.
     */
    const NON_SCALAR = 'non_scalar';

    /**
     * @const NON_STRING Indicates that an expected string value is something different.
     */
    const NON_STRING = 'non_string';

    /**
     * @const NON_INT Indicates that an expected int value is something different.
     */
    const NON_INT = 'non_int';

    /**
     * @const NON_FLOAT Indicates that an expected float value is something different.
     */
    const NON_FLOAT = 'non_float';

    /**
     * @const NON_BOOL Indicates that an expected bool value is something different.
     */
    const NON_BOOL = 'non_bool';

    /**
     * @const NON_ARRAY Indicates that an expected array value is something different.
     */
    const NON_ARRAY = 'non_array';

    /**
     * @const INVALID_CHOICE Indicates that a choice expectation has not been met.
     */
    const INVALID_CHOICE = 'invalid_choice';

    /**
     * @const INVALID_CHOICE Indicates that a provided class value points to a non-existant class.
     */
    const CLASS_NOT_EXISTS = 'class_not_exists';

    /**
     * @var array $errors Holds the errors returned by an operation.
     */
    private $errors;

    /**
     * Creates a new Error instance, which holds the given errors.
     *
     * @param mixed $errors An array of errors or null.
     *
     * @return Error
     */
    public static function unit($errors = null)
    {
        $class = static::class;

        return new $class($errors ?: []);
    }

    /**
     * @param mixed[] $errors An array of errors.
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Unwrap the contained errors.
     *
     * @return mixed[] The errors returned by an operation.
     */
    public function unwrap()
    {
        return $this->errors;
    }
}
