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
use ObjectGraph\Schema\Field\ScalarType;
use ObjectGraph\Test\GraphNode\User;
use stdClass;

/**
 * Class UserSchemaV1
 *
 * @package ObjectGraph\Test\Schema
 */
class UserSchemaV1 extends Schema
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
        return User::class;
    }

    /**
     * Whether this schema is strict or not
     *
     * This changes behaviour when iterating or serializing GraphNode instance. If schema is strict,
     * then only fields defined on schema will be iterated over or serialized.
     *
     * Otherwise, object fields which are not defined on the schema will be used as well.
     *
     * @return bool
     */
    public function isStrict(): bool
    {
        return true;
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
        $schema->addField('firstName')->withResolver(function (stdClass $data) {
            if (empty($data->userName)) {
                return null;
            }

            $name = preg_split('/\s+/', $data->userName, 2);

            return $name[0];
        });

        $schema->addField('lastName')->withResolver(function (stdClass $data) {
            if (empty($data->userName)) {
                return null;
            }

            $name = preg_split('/\s+/', $data->userName, 2);

            return (sizeof($name) === 2 ? $name[1] : null);
        });

        $schema->addField('fullName')->asAliasOf('userName');
        $schema->addField('dateOfBirth')->asAliasOf('dob')->asScalarValue(ScalarType::DATE_TIME);
        $schema->addField('email')->asAliasOf('emailAddress');
        $schema->addField('schema')->withDefaultValue(User::SCHEMA_V1);
    }
}
