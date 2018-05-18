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
 * Class MemoizeTest
 *
 * @package ObjectGraph
 */
class MemoizeTest extends TestCase
{
    public function testMemoize()
    {
        $expected = 'dummy';
        $memo     = new Memoize();

        /**
         * Test saving the value
         */

        $value = $memo->memoize('test', function () use ($expected) {
            return $expected;
        });

        $this->assertEquals($expected, $value);
        $this->assertTrue($memo->isCached('test'));

        /**
         * Test no function hit for cached value
         */

        $test = $this;

        $value = $memo->memoize('test', function () use ($test) {
            $test->fail('Resolver called for the cached property value');
        });

        $this->assertEquals($expected, $value);

        /**
         * Test deleting the property
         */

        $memo->clear('test');

        $this->assertFalse($memo->isCached('test'));
    }
}
