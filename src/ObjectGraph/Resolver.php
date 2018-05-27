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

use ObjectGraph\Exception\ObjectGraphException;
use ObjectGraph\Schema\Field\Kind;
use ObjectGraph\Schema\Field\ScalarType;
use stdClass;

/**
 * Class Resolver
 *
 * @package ObjectGraph
 */
class Resolver
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
     * @var Schema[]
     */
    private $schemaCache = [];

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
     * Resolve scalar $data according to the given $type
     *
     * Note: this method can be called recursively by the Schema
     *
     * @param mixed       $data
     * @param string|null $type
     *
     * @return mixed
     */
    public function resolveScalar($data, string $type = null)
    {
        return $this->scalarType->cast($data, $type);
    }

    /**
     * Represent given $data as an instance of GraphNode
     *
     * This method is a default resolver and an entry point, where you can inspect $data, determine its type and
     * if it is an object then attempt to determine the correct Schema to use and finally pass it further
     * to the corresponding ObjectGraph::resolveXXX() method of this class.
     *
     * Note: this method can be called recursively by the Schema
     *
     * @param stdClass|null $data
     * @param string|null   $schemaClassName
     * @param Context|null  $context
     *
     * @return GraphNode|null
     */
    public function resolveObject(
        stdClass $data = null,
        string $schemaClassName = null,
        Context $context = null
    ): GraphNode {
        if (empty($data)) {
            return null;
        }

        if (!empty($schemaClassName)) {
            $this->assertSameClassOrSubclassOf($schemaClassName, Schema::class);
        } else {
            $schemaClassName = Schema::class;
        }

        if (!isset($this->schemaCache[$schemaClassName])) {
            $this->schemaCache[$schemaClassName] = new $schemaClassName($this);
        }

        $schema             = $this->schemaCache[$schemaClassName]->withContext(($context ?: $this->context));
        $graphNodeClassName = $schema->getGraphNodeClassName();

        $this->assertSameClassOrSubclassOf($graphNodeClassName, GraphNode::class);

        return new $graphNodeClassName($data, $schema);
    }

    /**
     * Return an array with each element represented according to the provided $kind and $type
     *
     * The given $data array is assumed to contain homogeneous elements: all of the same kind and
     * type. In case of a mixed array, it should be resolved inside a field resolver.
     *
     * Note: this method can be called recursively by the Schema
     *
     * @param array|null   $data
     * @param string|null  $kind
     * @param string|null  $type
     * @param Context|null $context
     *
     * @return array
     */
    public function resolveArray(
        array $data = null,
        string $kind = Kind::RAW,
        string $type = null,
        Context $context = null
    ): array {
        if (empty($data)) {
            return [];
        }

        $resolver = $this;
        $context  = ($context ?: $this->context);

        return array_map(function ($data) use ($resolver, $kind, $type, $context) {
            switch ($kind ?: $resolver->kindOf($data)) {
                case Kind::SCALAR:
                    return $resolver->resolveScalar($data, $type);

                case Kind::GRAPH_NODE:
                    return $resolver->resolveObject($data, $type, $context);

                /**
                 * These 3 below are array-of-array scenarios:
                 * - array of array of unknown elements, this will be resolved based on their PHP type;
                 * - array of array of scalar values; and
                 * - array of array of GraphNode(s)
                 */

                case Kind::ARRAY:
                    return $resolver->resolveArray($data, null, $type, $context);

                case Kind::SCALAR_ARRAY:
                    return $resolver->resolveArray($data, Kind::SCALAR, $type, $context);
                    break;

                case Kind::GRAPH_NODE_ARRAY:
                    return $resolver->resolveArray($data, Kind::GRAPH_NODE, $type, $context);
                    break;

                /**
                 * Pass through element
                 */

                case Kind::RAW:
                    return $data;

                default:
                    throw new ObjectGraphException(sprintf('Unsupported data kind "%s"', $kind));
            }
        }, $data);
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

            /**
             * Arrays-of-arrays are not detected here, Resolver::resolveArray() should take care of them
             */

            default:
                return Kind::RAW; // no idea what kind you are ...
        }
    }

    /**
     * @param string $className
     * @param string $baseClassName
     *
     * @return bool
     */
    protected function assertSameClassOrSubclassOf(string $className, string $baseClassName): bool
    {
        if (!class_exists($className)) {
            throw new ObjectGraphException(sprintf('Class %s does not exist', $className));
        } elseif (!is_a($className, $baseClassName, true)) {
            throw new ObjectGraphException(sprintf('Class %s must extend %s', $className, $baseClassName));
        }

        return true;
    }
}
