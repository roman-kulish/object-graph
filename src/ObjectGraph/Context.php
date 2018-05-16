<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph;

use ArrayAccess;
use ObjectGraph\Exception\EmptyPropertyNameException;

/**
 * Class Context
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
     * @param mixed $offset An offset to check for
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
     * @param mixed $offset The offset to retrieve
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
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value  The value to set
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new EmptyPropertyNameException('Property must have a name');
        }

        $this->data[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
