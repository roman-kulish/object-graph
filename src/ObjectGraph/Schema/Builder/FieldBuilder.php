<?php

/**
 * This file is part of the Object Graph package.
 *
 * Copyright (c) 2018 Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\Schema\Builder;

use Closure;
use ObjectGraph\Schema;
use ObjectGraph\Schema\Field\Definition;
use ObjectGraph\Schema\Field\Kind;
use ObjectGraph\Schema\Field\Scope;

/**
 * Class FieldBuilder
 *
 * @package ObjectGraph\Schema\Builder
 */
class FieldBuilder
{
    /**
     * @var Definition
     */
    protected $definition;

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @param Scope $scope
     */
    public function __construct(Scope $scope)
    {
        $this->definition = new Definition();
        $this->scope      = $scope;
    }

    /**
     * @return Definition
     * @internal
     */
    public function getDefinition(): Definition
    {
        return $this->definition;
    }

    /**
     * When an object is cloned, PHP 5 will perform a shallow copy of all of the object's properties.
     * Any properties that are references to other variables, will remain references.
     *
     * Once the cloning is complete, if a __clone() method is defined, then the newly created object's
     * __clone() method will be called, to allow any necessary properties that need to be changed.
     *
     * NOT CALLABLE DIRECTLY.
     *
     * @link http://php.net/manual/en/language.oop5.cloning.php
     */
    public function __clone()
    {
        $this->definition = clone $this->definition;
    }

    /**
     * Set default field value
     *
     * @param mixed $value
     *
     * @return FieldBuilder
     */
    public function withDefaultValue($value): self
    {
        $this->definition->setDefault($value);

        return $this;
    }

    /**
     * Set or disable this field resolver
     *
     * @param Closure $resolver
     *
     * @return FieldBuilder
     */
    public function withResolver(Closure $resolver = null): self
    {
        if ($resolver instanceof Closure) {
            $resolver = $resolver->bindTo($this->scope);
        }

        $this->definition->setResolver($resolver);

        return $this;
    }

    /**
     * Cast the value returned from the resolver to the Graph Node of the $className
     *
     * @param string $schemaClassName
     *
     * @return FieldBuilder
     */
    public function asGraphNode(string $schemaClassName = Schema::class): self
    {
        $this->definition
            ->setKind(Kind::GRAPH_NODE)
            ->setType($schemaClassName);

        return $this;
    }

    /**
     * Cast the value returned from the resolver to the scalar of the given $type
     *
     * @param string|null $type One of the \ObjectGraph\Schema\Field\ScalarType constants
     *
     * @return FieldBuilder
     */
    public function asScalarValue(string $type = null): self
    {
        $this->definition
            ->setKind(Kind::SCALAR)
            ->setType($type);

        return $this;
    }

    /**
     * Cast each array value returned from the resolver to the scalar of the given $type
     *
     * @param string|null $type One of the \ObjectGraph\Schema\Field\ScalarType constants
     *
     * @return FieldBuilder
     */
    public function asScalarArray(string $type = null): self
    {
        $this->definition
            ->setKind(Kind::SCALAR_ARRAY)
            ->setType($type);

        return $this;
    }

    /**
     * Cast each array value returned from the resolver to the Graph Node of the $className
     *
     * @param string $schemaClassName
     *
     * @return FieldBuilder
     */
    public function asGraphNodeArray(string $schemaClassName = Schema::class): self
    {
        $this->definition
            ->setKind(Kind::GRAPH_NODE_ARRAY)
            ->setType($schemaClassName);

        return $this;
    }

    /**
     * Cast each array value returned from the resolver according to each PHP type
     * detected by the root resolver
     *
     * @param string|null $type
     *
     * @return FieldBuilder
     */
    public function asArray(string $type = null): self
    {
        $this->definition
            ->setKind(Kind::ARRAY)
            ->setType($type);

        return $this;
    }

    /**
     * Return raw value from the field resolver, as it is
     *
     * @return FieldBuilder
     */
    public function asRawData(): self
    {
        $this->definition
            ->setKind(Kind::RAW)
            ->setType(null);

        return $this;
    }
}
