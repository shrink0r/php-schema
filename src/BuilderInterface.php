<?php

namespace Shrink0r\Configr;

/**
 * A builder is responsible for providing a convenient api to defining deeply nested array structures.
 */
interface BuilderInterface
{
    /**
     * Merges the builder's current state with the given defaults (builder state winning over default).
     *
     * @param mixed[] $defaults
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
}
