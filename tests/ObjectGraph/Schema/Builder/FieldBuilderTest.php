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
use ObjectGraph\GraphNode;
use ObjectGraph\Schema\Field\Definition;
use ObjectGraph\Schema\Field\Kind;
use ObjectGraph\Schema\Field\ScalarType;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class FieldBuilderTest
 *
 * @package ObjectGraph\Schema\Builder
 */
class FieldBuilderTest extends TestCase
{
    /**
     * @var FieldBuilder
     */
    protected $fieldBuilder;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->fieldBuilder = new FieldBuilder();

        parent::setUp();
    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::getDefault()
     * @covers \ObjectGraph\Schema\Field\Definition::getResolver()
     * @covers \ObjectGraph\Schema\Field\Definition::getKind()
     * @covers \ObjectGraph\Schema\Field\Definition::getType()
     */
    public function testDefaultDefinition()
    {
        $definition = $this->fieldBuilder->getDefinition();

        $this->assertInstanceOf(Definition::class, $definition);
        $this->assertNull($definition->getDefault());
        $this->assertNull($definition->getResolver());
        $this->assertEquals($definition->getKind(), Kind::RAW);
        $this->assertNull($definition->getType());
    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::setDefault()
     */
    public function testDefaultValue()
    {
        $definition           = $this->fieldBuilder->getDefinition();
        $expectedDefaultValue = 1;

        $this->fieldBuilder->withDefaultValue($expectedDefaultValue);
        $this->assertSame($expectedDefaultValue, $definition->getDefault());
    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::setResolver()
     */
    public function testResolver()
    {
        $definition       = $this->fieldBuilder->getDefinition();
        $expectedResolver = Closure::fromCallable('strtoupper');

        $this->fieldBuilder->withResolver($expectedResolver);
        $this->assertInstanceOf(Closure::class, $definition->getResolver());

        $this->fieldBuilder->withResolver(null); // disable resolver
        $this->assertNull($definition->getResolver());
    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::setKind()
     * @covers \ObjectGraph\Schema\Field\Definition::setType()
     */
    public function testGraphNode()
    {
        $definition        = $this->fieldBuilder->getDefinition();
        $expectedClassName = stdClass::class;

        $this->fieldBuilder->asGraphNode();
        $this->assertEquals(Kind::GRAPH_NODE, $definition->getKind());
        $this->assertEquals(GraphNode::class, $definition->getType());

        $this->fieldBuilder->asGraphNode($expectedClassName);
        $this->assertEquals($expectedClassName, $definition->getType());
    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::setKind()
     * @covers \ObjectGraph\Schema\Field\Definition::setType()
     */
    public function testGraphNodeArray()
    {
        $definition        = $this->fieldBuilder->getDefinition();
        $expectedClassName = stdClass::class;

        $this->fieldBuilder->asGraphNodeArray();
        $this->assertEquals(Kind::ARRAY, $definition->getKind());
        $this->assertEquals(GraphNode::class, $definition->getType());

        $this->fieldBuilder->asGraphNodeArray($expectedClassName);
        $this->assertEquals($expectedClassName, $definition->getType());
    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::setKind()
     * @covers \ObjectGraph\Schema\Field\Definition::setType()
     */
    public function testScalarValue()
    {
        $definition   = $this->fieldBuilder->getDefinition();
        $expectedType = ScalarType::TIMESTAMP;

        $this->fieldBuilder->asScalarValue();
        $this->assertEquals(Kind::SCALAR, $definition->getKind());
        $this->assertNull($definition->getType());

        $this->fieldBuilder->asScalarValue($expectedType);
        $this->assertEquals($expectedType, $definition->getType());

    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::setKind()
     * @covers \ObjectGraph\Schema\Field\Definition::setType()
     */
    public function testScalarArray()
    {
        $definition   = $this->fieldBuilder->getDefinition();
        $expectedType = ScalarType::TIMESTAMP;

        $this->fieldBuilder->asScalarArray();
        $this->assertEquals(Kind::ARRAY, $definition->getKind());
        $this->assertNull($definition->getType());

        $this->fieldBuilder->asScalarArray($expectedType);
        $this->assertEquals($expectedType, $definition->getType());
    }

    /**
     * @covers \ObjectGraph\Schema\Field\Definition::setKind()
     * @covers \ObjectGraph\Schema\Field\Definition::setType()
     */
    public function testRaw()
    {
        $definition = $this->fieldBuilder->getDefinition();

        $this->fieldBuilder->raw();

        $this->assertEquals(Kind::RAW, $definition->getKind());
        $this->assertNull($definition->getType());
    }
}
