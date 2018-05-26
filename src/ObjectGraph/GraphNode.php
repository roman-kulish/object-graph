<?php

/**
 * This file is part of the Object Graph package.
 *
 * Copyright (c) 2018 Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph;

use ArrayAccess;
use JsonSerializable;
use ObjectGraph\GraphNode\Memoize;
use stdClass;

/**
 * Class GraphNode
 *
 * @package ObjectGraph
 */
class GraphNode implements ArrayAccess, JsonSerializable
{
    /**
     * @var stdClass
     */
    protected $data;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var Memoize
     */
    protected $memo;

    /**
     * @param stdClass    $data
     * @param Schema|null $schema
     */
    public function __construct(stdClass $data, Schema $schema)
    {
        $this->data   = $data;
        $this->schema = $schema;
        $this->memo   = new Memoize();
    }

    /**
     * Return this GraphNode underlying data
     *
     * @return stdClass
     */
    public function getData(): stdClass
    {
        return $this->data;
    }

    /**
     * __get() is utilized for reading data from inaccessible members.
     *
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     *
     * @param $name string
     *
     * @return mixed
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
    }

    /**
     * __set() run when writing data to inaccessible members.
     *
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     *
     * @param $name  string
     * @param $value mixed
     */
    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
    }

    /**
     * __isset() is triggered by calling isset() or empty() on inaccessible members.
     *
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     *
     * @param $name string
     *
     * @return bool
     */
    public function __isset($name)
    {
        // TODO: Implement __isset() method.
    }

    /**
     * __unset() is invoked when unset() is used on inaccessible members.
     *
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     *
     * @param $name string
     */
    public function __unset($name)
    {
        // TODO: Implement __unset() method.
    }

    /**
     * This method is called by var_dump() when dumping an object to get the properties that should be shown.
     * If the method isn't defined on an object, then all public, protected and private properties will be shown.
     *
     * @link  http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     *
     * @return array
     */
    public function __debugInfo()
    {
        return (array)$this->getData();
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
        // TODO: Implement offsetExists() method.
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
        // TODO: Implement offsetGet() method.
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
        // TODO: Implement offsetSet() method.
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
        // TODO: Implement offsetUnset() method.
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->getData();
    }
}
