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

/**
 * Class ContextTest
 *
 * @package ObjectGraph
 */
class ContextTest extends TestCase
{
    public function testContextWithDefaultData()
    {
        $data = [
            'a' => 1,
            'b' => 'dummy',
        ];

        $context = new Context($data);

        /**
         * Test initial data access
         */

        foreach ($data as $property => $value) {
            $this->assertArrayHasKey($property, $context);
            $this->assertEquals($value, $context[$property]);
        }
    }

    public function testContextPropertyOperations()
    {
        $value   = 'dummy';
        $context = new Context();

        /**
         * Test non existing property
         */

        $this->assertArrayNotHasKey('property', $context);
        $this->assertNull($context['property']);

        /**
         * Test assignment
         */

        $context['property'] = $value;

        $this->assertArrayHasKey('property', $context);
        $this->assertEquals($value, $context['property']);

        /**
         * Test deleting property
         */

        unset($context['property']);

        $this->assertArrayNotHasKey('property', $context);
        $this->assertNull($context['property']);
    }

    /**
     * @expectedException \ObjectGraph\Exception\EmptyNameException
     */
    public function testEmptyPropertyException()
    {
        $context   = new Context();
        $context[] = 'dummy';
    }
}
