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

use Closure;
use ObjectGraph\Schema\Builder\SchemaBuilder;
use ObjectGraph\Schema\Field\Definition;
use ObjectGraph\Schema\Field\Kind;
use ObjectGraph\Schema\Field\Scope;
use stdClass;

/**
 * Class Schema
 *
 * @package ObjectGraph
 */
class Schema
{
    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Definition[]
     */
    protected $fields = [];

    /**
     * @param Resolver $resolver
     */
    final public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
        $this->context  = new Context();

        $this->init();
    }

    /**
     * Internal method called from {@see Schema::__construct} and {@see Schema::__wakeup()} and
     * used to prepare internals and initialize schema by passing the call to {@see Schema::build()}
     */
    private function init()
    {
        $scope         = new Scope($this->resolver);
        $schemaBuilder = new SchemaBuilder($scope);

        $this->build($schemaBuilder);

        $this->fields = $schemaBuilder->getFields();
    }

    /**
     * Factory method which returns a new instance of the Schema with shared root $resolver and
     * provided $context
     *
     * @param Context $context
     *
     * @return Schema
     */
    public function withContext(Context $context): self
    {
        $schema          = clone $this;
        $schema->context = $context;

        return $schema;
    }

    /**
     * Get GraphNode class name to instantiate
     *
     * Override this method to change the class name
     *
     * @return string
     */
    public function getGraphNodeClassName(): string
    {
        return GraphNode::class;
    }

    /**
     * Whether this schema is strict or not
     *
     * This changes behaviour when iterating or serializing GraphNode instance. If schema is strict,
     * then only fields defined on schema will be iterated over or serialized.
     *
     * Otherwise, object fields which are not defined on the schema will be used as well.
     *
     * @return bool
     */
    public function isStrict(): bool
    {
        return false;
    }

    /**
     * Get a list of defined schema fields
     *
     * @return array
     */
    public function getFields(): array
    {
        return array_keys($this->fields);
    }

    /**
     * Resolve return the $field value
     *
     * @param string   $field
     * @param stdClass $data
     *
     * @return mixed
     */
    public function resolve(string $field, stdClass $data)
    {
        if (isset($this->fields[$field])) {
            $definition = $this->fields[$field];
            $resolver   = $definition->getResolver();
            $default    = $definition->getDefault();
            $kind       = $definition->getKind();
            $type       = $definition->getType();
        } else {
            $resolver = null;
            $default  = null;
            $kind     = Kind::RAW;
            $type     = null;
        }

        $context = clone $this->context;

        switch (true) {
            case ($resolver instanceof Closure):
                $value = call_user_func($resolver, $data, $context);
                break;

            case isset($data->$field):
                $value = $data->$field;
                break;

            default:
                $value = null;
        }

        if (empty($value)) {
            $value = $default;
        }

        switch ($kind ?: $this->resolver->kindOf($value)) {
            case Kind::SCALAR:
                return $this->resolver->resolveScalar($value, $type);

            case Kind::GRAPH_NODE:
                return $this->resolver->resolveObject($value, $type, $context);

            case Kind::ARRAY:
                return $this->resolver->resolveArray($value, null, $type, $context);

            case Kind::SCALAR_ARRAY:
                return $this->resolver->resolveArray($value, Kind::SCALAR, $type, $context);
                break;

            case Kind::GRAPH_NODE_ARRAY:
                return $this->resolver->resolveArray($value, Kind::GRAPH_NODE, $type, $context);
                break;

            default:
                return $value;
        }
    }

    /**
     * Initialise schema
     *
     * This is a sub-constructor, which is invoked by the {@see Schema::__construct()} and
     * it should contain all the schema initialization logic
     *
     * @param SchemaBuilder $schema
     */
    protected function build(SchemaBuilder $schema)
    {
        // define your fields here ...
    }

    /**
     * serialize() checks if your class has a function with the magic name __sleep.
     * If so, that function is executed prior to any serialization.
     * It can clean up the object and is supposed to return an array with the names of all variables of that object
     * that should be serialized.
     *
     * If the method doesn't return anything then NULL is serialized and E_NOTICE is issued.
     * The intended use of __sleep is to commit pending data or perform similar cleanup tasks.
     * Also, the function is useful if you have very large objects which do not need to be saved completely.
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.sleep
     *
     * @return string[]
     */
    public function __sleep()
    {
        return [
            'resolver',
            'context',
        ];
    }

    /**
     * unserialize() checks for the presence of a function with the magic name __wakeup.
     * If present, this function can reconstruct any resources that the object may have.
     * The intended use of __wakeup is to reestablish any database connections that may have been lost during
     * serialization and perform other reinitialization tasks.
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.sleep
     */
    public function __wakeup()
    {
        $this->init();
    }
}
