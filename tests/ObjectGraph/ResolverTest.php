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

use ArrayAccess;
use ObjectGraph\Schema\Field\Kind;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class ResolverTest
 *
 * @package ObjectGraph
 */
class ResolverTest extends TestCase
{
    /**
     * @param $value
     * @param $expectedKind
     *
     * @dataProvider dataProviderKindOf
     */
    public function testKindOf($value, $expectedKind)
    {
        $resolver = new Resolver();

        $this->assertEquals($expectedKind, $resolver->kindOf($value));
    }

    /**
     * @expectedException \ObjectGraph\Exception\ObjectGraphException
     */
    public function testSchemaClassNotExistsException() {
        $resolver = new Resolver();
        $resolver->resolveObject(new stdClass(), 'dummy');
    }

    /**
     * @expectedException \ObjectGraph\Exception\ObjectGraphException
     */
    public function testSchemaNotValidException() {
        $resolver = new Resolver();
        $resolver->resolveObject(new stdClass(), ArrayAccess::class);
    }

    public function dataProviderKindOf()
    {
        return [
            [null, Kind::SCALAR],
            [1, Kind::SCALAR],
            [1.0, Kind::SCALAR],
            ['dummy', Kind::SCALAR],
            [false, Kind::SCALAR],
            [[], Kind::ARRAY],
            [new stdClass(), Kind::GRAPH_NODE],
            [tmpfile(), Kind::RAW],
        ];
    }
}
