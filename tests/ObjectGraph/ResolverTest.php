<?php

/**
 * This file is part of the Object Graph package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph;

use DateTime;
use InvalidArgumentException;
use ObjectGraph\Schema\Field\Kind;
use ObjectGraph\Test\GraphNode\User;
use ObjectGraph\Test\Resolver\UserResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

/**
 * Class ResolverTest
 *
 * @package ObjectGraph
 */
class ResolverTest extends TestCase
{
    /**
     * @param stdClass $data
     * @param string   $schema
     *
     * @dataProvider dataProviderUserResolver
     */
    public function testUserResolver(stdClass $data, string $schema)
    {
        $expectedFirstName   = 'Arnold';
        $expectedLastName    = 'Schwarzenegger';
        $expectedFullName    = 'Arnold Schwarzenegger';
        $expectedDateOfBirth = new DateTime('1947-07-30');
        $expectedEmail       = 'arnold.schwarzenegger@gov.ca.gov';

        $resolver = new UserResolver();

        /** @var User $graphNode */
        $graphNode = $resolver->resolveObject($data);

        $this->assertInstanceOf(User::class, $graphNode);
        $this->assertEquals($expectedFirstName, $graphNode->firstName);
        $this->assertEquals($expectedLastName, $graphNode->lastName);
        $this->assertEquals($expectedFullName, $graphNode->fullName);
        $this->assertEquals($expectedDateOfBirth, $graphNode->dateOfBirth);
        $this->assertEquals($expectedEmail, $graphNode->email);
        $this->assertEquals($schema, $graphNode->schema);
    }

    /**
     * @param $value
     * @param $expectedKind
     *
     * @dataProvider dataProviderKindOf
     */
    public function testKindOf($value, $expectedKind)
    {
        $resolver = new Resolver();

        $this->assertEquals($expectedKind, $resolver->kindOf($value));
    }

    /**
     * @expectedException \ObjectGraph\Exception\ObjectGraphException
     */
    public function testSchemaClassNotExistsException()
    {
        $resolver = new Resolver();
        $resolver->resolveObject(new stdClass(), 'dummy');
    }

    /**
     * @expectedException \ObjectGraph\Exception\ObjectGraphException
     */
    public function testSchemaNotValidException()
    {
        $resolver = new Resolver();
        $resolver->resolveObject(new stdClass(), stdClass::class);
    }

    public function dataProviderUserResolver()
    {
        return [
            [$this->loadJson('user-v1.json'), User::SCHEMA_V1],
            [$this->loadJson('user-v2.json'), User::SCHEMA_V2],
        ];
    }

    public function dataProviderKindOf()
    {
        return [
            [null, Kind::SCALAR],
            [1, Kind::SCALAR],
            [1.0, Kind::SCALAR],
            ['dummy', Kind::SCALAR],
            [false, Kind::SCALAR],
            [[], Kind::ARRAY],
            [new stdClass(), Kind::GRAPH_NODE],
            [tmpfile(), Kind::RAW],
        ];
    }

    /**
     * @param string $filename
     *
     * @return stdClass
     */
    protected function loadJson(string $filename): stdClass
    {
        $path = __DIR__ . '/Test/Data/' . $filename;

        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist', $filename));
        }

        $data = json_decode(file_get_contents($path));

        if (!($data instanceof stdClass)) {
            throw new RuntimeException(sprintf('Unable to decode JSON file %s', $path));
        }

        return $data;
    }
}
