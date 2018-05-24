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

/**
 * Interface Kind
 *
 * @package ObjectGraph\Schema\Field
 */
interface Kind
{
    const SCALAR = 'scalar';
    const GRAPH_NODE = 'graph_node';
    const ARRAY = 'array';
    const RAW = 'raw';
}
