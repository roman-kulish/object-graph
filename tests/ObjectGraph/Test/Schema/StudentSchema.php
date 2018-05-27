<?php

/**
 * This file is part of the Object Graph package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\Test\Schema;

use ObjectGraph\Schema;
use ObjectGraph\Schema\Builder\SchemaBuilder;
use ObjectGraph\Test\GraphNode\Student;

/**
 * Class StudentSchema
 *
 * @package ObjectGraph\Test\Schema
 */
class StudentSchema extends Schema
{
    /**
     * Get GraphNode class name to instantiate
     *
     * Override this method to change the class name
     *
     * @return string
     */
    public function getGraphNodeClassName(): string
    {
        return Student::class;
    }

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
        $schema->addField('firstName');
        $schema->addField('lastName');
    }
}
