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
use stdClass;
use Traversable;

/**
 * Class Scope is shared between all fields resolvers and provides an isolation, because all resolvers
 * are bound to the Scope.
 *
 * Scope also provides a few useful features: an integration with JSONPath library to run XPath like queries
 * on a object; and access to the root ObjectGraph resolver.
 *
 * @package ObjectGraph\Schema\Field
 */
class Scope
{
    /**
     * @see https://github.com/FlowCommunications/JSONPath
     *
     * @param stdClass $data
     * @param string   $expression
     *
     * @return Traversable
     */
    public function query(stdClass $data, string $expression): Traversable
    {
        // TODO
    }

    public function getRootResolver(): ObjectGraph
    {
        // TODO
    }
}
