<?php

/**
 * This file is part of the Object Graph package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\Test\Resolver;

use ObjectGraph\Context;
use ObjectGraph\Exception\ObjectGraphException;
use ObjectGraph\GraphNode;
use ObjectGraph\Resolver;
use ObjectGraph\Test\Schema\UserSchemaV1;
use ObjectGraph\Test\Schema\UserSchemaV2;
use stdClass;

/**
 * Class UserResolver
 *
 * @package ObjectGraph\Test\Resolver
 */
class UserResolver extends Resolver
{
    /**
     * Represent given $data as an instance of GraphNode
     *
     * This method is a default resolver and an entry point, where you can inspect $data, determine its type and
     * if it is an object then attempt to determine the correct Schema to use and finally pass it further
     * to the corresponding ObjectGraph::resolveXXX() method of this class.
     *
     * Note: this method can be called recursively by the Schema
     *
     * @param stdClass|null $data
     * @param string|null   $schemaClassName
     * @param Context|null  $context
     *
     * @return GraphNode|null
     */
    public function resolveObject(
        stdClass $data = null,
        string $schemaClassName = null,
        Context $context = null
    ): GraphNode {
        if (empty($data)) {
            return null;
        }

        switch (true) {
            case (
                (isset($data->firstName) && isset($data->lastName)) ||
                isset($data->emailName) ||
                isset($data->dateOfBirth)
            ):
                $schemaClassName = UserSchemaV2::class;
                break;

            case (isset($data->fullName) || isset($data->dob) || isset($data->emailAddress)):
                $schemaClassName = UserSchemaV1::class;
                break;

            default:
                throw new ObjectGraphException('Unable to detect schema from the user data object');
        }

        return parent::resolveObject($data, $schemaClassName, $context);
    }
}
