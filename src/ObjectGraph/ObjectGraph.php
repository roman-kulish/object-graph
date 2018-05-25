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

use ObjectGraph\Schema\Field\Kind;
use ObjectGraph\Schema\Field\ScalarType;
use stdClass;

/**
 * Class ObjectGraph
 *
 * @package ObjectGraph
 */
class ObjectGraph
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ScalarType
     */
    protected $scalarType;

    /**
     * @param Context|null    $context
     * @param ScalarType|null $scalarTypeResolver
     */
    public function __construct(Context $context = null, ScalarType $scalarTypeResolver = null)
    {
        $this->context    = ($context ?: new Context());
        $this->scalarType = ($scalarTypeResolver ?: new ScalarType());
    }

    /**
     * Resolve $data and return it in a proper format: scalar, GraphNode or an array of scalars or GraphNode(s)
     *
     * This method is a default resolver and an entry point, where you can inspect $data, determine its type and
     * if it is an object then attempt to determine the correct Schema to use and finally pass it further
     * to the corresponding ObjectGraph::resolveXXX() method of this class.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function resolve($data)
    {
        switch ($this->kindOf($data)) {
            case Kind::SCALAR:
                return $this->resolveScalar($data);

            case Kind::GRAPH_NODE:
                return $this->resolveObject($data);

            case Kind::ARRAY:
                return $this->resolveArray($data);

            default:
                return $data;
        }
    }

    public function resolveScalar($data, string $type = null)
    {
        // TODO
    }

    public function resolveObject(stdClass $data, string $schemaClassName = null, Context $context = null): GraphNode
    {
        // TODO
    }

    public function resolveArray(
        array $data,
        string $kind = Kind::RAW,
        string $type = null,
        Context $context = null
    ): array {
        // TODO
    }

    /**
     * Determine the kind of the $data from its PHP type
     *
     * @param mixed $data
     *
     * @return string One of the {@see ObjectGraph\Schema\Field\Kind} class constants
     */
    public function kindOf($data): string
    {
        switch (true) {

            /**
             * NULL is considered scalar, because ScalarType resolver may convert it to a different "zero value",
             * such as an empty string, 0 integer or boolean FALSE.
             */

            case (is_null($data) || is_scalar($data)):
                return Kind::SCALAR;

            case is_object($data):
                return Kind::GRAPH_NODE;

            case is_array($data):
                return Kind::ARRAY;

            default:
                return Kind::RAW; // no idea what are you ...
        }
    }
}
