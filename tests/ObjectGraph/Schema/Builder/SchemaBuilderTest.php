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

use ObjectGraph\Resolver;
use ObjectGraph\Schema\Field\Definition;
use ObjectGraph\Schema\Field\Scope;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaBuilderTest
 *
 * @package ObjectGraph\Schema\Builder
 */
class SchemaBuilderTest extends TestCase
{
    public function testSchemaBuilder()
    {
        $expectedFieldName = 'test';
        $schemaBuilder     = new SchemaBuilder(new Scope(new Resolver()));
        $schemaBuilder->addField($expectedFieldName);
        $schema = $schemaBuilder->getFields();

        $this->assertTrue(is_array($schema));
        $this->assertArrayHasKey($expectedFieldName, $schema);
        $this->assertCount(1, $schema);
        $this->assertInstanceOf(Definition::class, $schema[$expectedFieldName]);
    }
}
