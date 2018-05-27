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

use ObjectGraph\Schema\Builder\SchemaBuilder;
use ObjectGraph\Test\Schema\SchemaSerializationTest;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaTest
 *
 * @package ObjectGraph
 */
class SchemaTest extends TestCase
{
    public function testDefaultGetGraphNodeClassName()
    {
        $schema = new Schema(new Resolver());

        $this->assertEquals(GraphNode::class, $schema->getGraphNodeClassName());
    }

    public function testDefaultIsStrict()
    {
        $schema = new Schema(new Resolver());

        $this->assertFalse($schema->isStrict());
    }

    public function testWithContext()
    {
        $context = new Context();

        $schema1 = new class(new Resolver()) extends Schema
        {
            public function getContext(): Context
            {
                return $this->context;
            }
        };

        $schema2 = $schema1->withContext($context);

        $this->assertNotSame($schema1, $schema2);
        $this->assertSame($context, $schema2->getContext());
    }

    public function testGetFields()
    {
        $expectedFields = [
            'test1',
            'test2',
            'test3',
        ];

        $schema = new class(new Resolver()) extends Schema
        {
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
                $schema->addField('test1');
                $schema->addField('test2');
                $schema->addField('test3');
            }
        };

        $this->assertEquals($expectedFields, $schema->getFields());
    }

    public function testSerialization()
    {
        $expected = [
            'test1' => 1,
            'test2' => 'dummy',
            'test3' => false,
        ];

        $data = (object)[
            'field1' => $expected['test1'],
            'field2' => $expected['test2'],
            'field3' => $expected['test3'],
        ];

        $schema     = new SchemaSerializationTest(new Resolver());
        $serialized = serialize($schema);

        /** @var SchemaSerializationTest $schema */
        $schema = unserialize($serialized);

        $this->assertInstanceOf(SchemaSerializationTest::class, $schema);
        $this->assertEquals(array_keys($expected), $schema->getFields());

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $schema->resolve($key, $data));
        }
    }
}
