<?php

namespace Shrink0r\Configr;

use ArrayAccess;

/**
 * A builder is responsible for providing a convenient api to defining deeply nested array structures.
 */
interface BuilderInterface extends ArrayAccess
{
    /**
     * Merges the builder's current state with the given defaults (builder state winning over default).
     *
     * @param array $defaults
     *
     * @return array Returns an array reflecting the builder's current state with merged defaults.
     */
    public function build(array $defaults = []);

    /**
     * Return the value for the given key, relative to the builder's current position.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function valueOf($key);

    /**
     * Closes the current child node and moves the builder cursor one path up.
     *
     * @return BuilderInterface Returns self
     */
    public function end();

    /**
     * Resets the builder's position to root.
     *
     * @return BuilderInterface Returns self
     */
    public function rewind();

    /**
     * Navigate to the given key, creating it along the way if it does not yet exist.
     *
     * @param string $key
     *
     * @return BuilderInterface Returns self
     */
    public function __get($key);

    /**
     * Assign a given value to the given key relative to the buider's current position.
     * Rewinds the builder afterwards, so any proceeding accesses must start from root again.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value);

    /**
     * Assign a given value to the given key relative to the buider's current position.
     * Does not rewind the builder and returns self, so fluent assignment of values is possible.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return BuilderInterface Returns self
     */
    public function __call($key, array $args = []);
}
