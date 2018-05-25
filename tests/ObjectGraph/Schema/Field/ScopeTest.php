<?php

/**
 * This file is part of the Object Graph package.
 *
 * Copyright (c) 2018 Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\Schema\Field;

use ObjectGraph\ObjectGraph;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class ScopeTest
 *
 * @package ObjectGraph\Schema\Field
 */
class ScopeTest extends TestCase
{
    public function testGetRootResolver()
    {
        $resolver = new ObjectGraph();
        $scope    = new Scope($resolver);

        $this->assertSame($resolver, $scope->getRootResolver());
    }

    /**
     * @expectedException \ObjectGraph\Exception\ObjectGraphException
     */
    public function testQuery()
    {
        $scope = new Scope(new ObjectGraph());
        $scope->query(new stdClass(), 'boom!');
    }
}
