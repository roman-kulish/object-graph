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

use PHPUnit\Framework\TestCase;

/**
 * Class DefinitionTest
 *
 * @package ObjectGraph\Schema\Field
 */
class DefinitionTest extends TestCase
{
    /**
     * @expectedException \ObjectGraph\Exception\InvalidArgumentException
     */
    public function testInvalidKindArgumentException()
    {
        $definition = new Definition();
        $definition->setKind(999);
    }
}
