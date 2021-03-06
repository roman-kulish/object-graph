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
use ObjectGraph\Exception\ImmutableObjectException;
use ObjectGraph\GraphNode\Memoize;
use ObjectGraph\GraphNode\Transformer;
use stdClass;

/**
 * Class GraphNode
 *
 * @package ObjectGraph
 */
class GraphNode implements ArrayAccess
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
     * Return this GraphNode underlying source data
     *
     * @return stdClass
     */
    public function getData(): stdClass
    {
        return $this->data;
    }

    /**
     * Return GraphNode representation as an object
     *
     * @return stdClass
     */
    public function asObject(): stdClass
    {
        return (new Transformer($this, $this->getFields()))->asObject();
    }

    /**
     * Return GraphNode representation as an array
     *
     * @return array
     */
    public function asArray(): array
    {
        return (new Transformer($this, $this->getFields()))->asArray();
    }

    /**
     * Get a list of fields
     *
     * For a strict schema, only a list of defined fields is returned. Otherwise, the result is a
     * combination of schema and source object fields
     *
     * @return array
     */
    private function getFields(): array
    {
        $fields = $this->schema->getFields();

        if (!$this->schema->isStrict()) {
            $objectVars = get_object_vars($this->getData());
            $fields     = array_merge(array_keys($objectVars), $fields);
        }

        return $fields;
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
        $data   = $this->data;
        $schema = $this->schema;

        return $this->memo->memoize($name, function () use ($data, $schema, $name) {
            return $schema->resolve($name, $data);
        });
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
        throw new ImmutableObjectException('Attempt to set a new property on immutable object');
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
        return (null !== $this->$name);
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
        throw new ImmutableObjectException('Attempt to unset a property on immutable object');
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
        return $this->asArray();
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
        return isset($this->$offset);
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
        return $this->$offset;
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
        throw new ImmutableObjectException('Attempt to set a new property on immutable object');
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
        throw new ImmutableObjectException('Attempt to unset a property on immutable object');
    }
}
