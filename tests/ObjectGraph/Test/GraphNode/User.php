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

use DateTime;
use ObjectGraph\GraphNode;

/**
 * Class User
 *
 * @property string   $firstName
 * @property string   $lastName
 * @property string   $fullName
 * @property DateTime $dateOfBirth
 * @property string   $email
 * @property string   $schema
 *
 * @package ObjectGraph\Test\GraphNode
 */
class User extends GraphNode
{
    const SCHEMA_V1 = 'v1';
    const SCHEMA_V2 = 'v2';
}
