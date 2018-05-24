<?php

/**
 * This file is part of the Object Graph package.
 *
 * Copyright (c) 2018 Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph;

use ObjectGraph\Schema\Field\ScalarType;

/**
 * Class ObjectGraph
 *
 * @package ObjectGraph
 */
class ObjectGraph
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ScalarType
     */
    protected $scalarType;

    /**
     * @param Context|null    $context
     * @param ScalarType|null $scalarTypeResolver
     */
    public function __construct(Context $context = null, ScalarType $scalarTypeResolver = null)
    {
        $this->context    = ($context ?: new Context());
        $this->scalarType = ($scalarTypeResolver ?: new ScalarType());
    }
}
