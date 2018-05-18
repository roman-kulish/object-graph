<?php

/**
 * This file is part of the Object Graph package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph;

use ArrayAccess;
use ObjectGraph\Exception\EmptyNameException;

/**
 * Class Context is used to pass variables to the field resolvers.
 *
 * Root object will receive a copy of the global context and its nested objects
 * will receive a copy of the parent's Context.
 *
 * Context can be used to pass variables to the nested object field resolvers.
 *
 * Note that a copy of the Context is created using cloning. Context should be used
 * to store scalar variables and it does not support deep cloning.
 *
 * If there is an object stored in the Context, then cloned Context will retain the reference
 * to the original object.
 *
 * @package ObjectGraph
 */
class Context implements ArrayAccess
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data Initial context data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string $offset An offset to check for
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string $offset The offset to retrieve
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset) ? $this->data[$offset] : null);
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param string $offset The offset to assign the value to
     * @param mixed $value  The value to set
     */
    public function offsetSet($offset, $value)
    {

        /**
         * Storing a value inside the Context like $context[] = 'dummy' is OK,
         * but is confusing and the variable name is not obvious.
         */

        if (empty($offset)) {
            throw new EmptyNameException('Context variable name must not be empty');
        }

        $this->data[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
