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

use ObjectGraph\Schema\Field\Definition;

/**
 * Class SchemaBuilder
 *
 * @package ObjectGraph\Schema\Builder
 */
class SchemaBuilder
{
    /**
     * @var Definition[]
     */
    protected $fields = [];

    /**
     * @var FieldBuilder
     */
    protected $fieldBuilder;

    public function __construct()
    {
        $this->fieldBuilder = new FieldBuilder();
    }

    /**
     * Add a new field
     *
     * @param string $name
     *
     * @return FieldBuilder
     */
    public function addField(string $name)
    {
        $fieldBuilder        = clone $this->fieldBuilder;
        $this->fields[$name] = $fieldBuilder->getDefinition();

        return $fieldBuilder;
    }

    /**
     * @return Definition[]
     * @internal
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
