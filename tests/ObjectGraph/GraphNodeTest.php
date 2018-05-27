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

use PHPUnit\Framework\TestCase;
use stdClass;

class GraphNodeTest extends TestCase
{
    public function testAccessor()
    {
        $data = (object)[
            'field' => 1,
        ];

        $graphNode = new GraphNode($data, new Schema(new Resolver()));

        $this->assertFalse(isset($graphNode->dummy));
        $this->assertFalse(isset($graphNode['dummy']));

        $this->assertTrue(isset($graphNode->field));
        $this->assertTrue(isset($graphNode['field']));

        $this->assertSame(1, $graphNode->field);
        $this->assertSame(1, $graphNode['field']);
    }

    /**
     * @expectedException \ObjectGraph\Exception\ImmutableObjectException
     */
    public function testSetException()
    {
        $graphNode       = new GraphNode(new stdClass(), new Schema(new Resolver()));
        $graphNode->boom = 1;
    }

    /**
     * @expectedException \ObjectGraph\Exception\ImmutableObjectException
     */
    public function testUnsetException()
    {
        $graphNode = new GraphNode(new stdClass(), new Schema(new Resolver()));

        unset($graphNode->boom);
    }

    /**
     * @expectedException \ObjectGraph\Exception\ImmutableObjectException
     */
    public function testOffsetSetException()
    {
        $graphNode         = new GraphNode(new stdClass(), new Schema(new Resolver()));
        $graphNode['boom'] = 1;
    }

    /**
     * @expectedException \ObjectGraph\Exception\ImmutableObjectException
     */
    public function testOffsetUnsetException()
    {
        $graphNode = new GraphNode(new stdClass(), new Schema(new Resolver()));

        unset($graphNode['boom']);
    }

    public function testSerialization()
    {
        $expected = [
            'test1' => 1,
            'test2' => 'dummy',
            'test3' => false,
        ];

        $resolver  = new Resolver();
        $graphNode = $resolver->resolveObject((object)$expected);

        $serialized = serialize($graphNode);

        /** @var GraphNode $graphNode */
        $graphNode = unserialize($serialized);

        $this->assertInstanceOf(GraphNode::class, $graphNode);
        $this->assertEquals(array_keys($expected), array_keys($graphNode->asArray()));

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $graphNode->$key);
        }
    }
}
