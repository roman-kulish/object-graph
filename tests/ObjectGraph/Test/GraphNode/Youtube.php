<?php

/**
 * This file is part of the Object Graph package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\Test\GraphNode;

use ObjectGraph\GraphNode;
use ObjectGraph\Test\GraphNode\Youtube\Item;
use ObjectGraph\Test\GraphNode\Youtube\PageInfo;

/**
 * Class Youtube
 *
 * @property string   $kind
 * @property string   $etag
 * @property string   $nextPageToken
 * @property string   $regionCode
 * @property PageInfo $pageInfo
 * @property Item[]   $items
 *
 * @package ObjectGraph\Test\GraphNode
 */
class Youtube extends GraphNode
{

}
