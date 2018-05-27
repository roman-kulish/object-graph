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
use ObjectGraph\Schema\Field\ScalarType;
use ObjectGraph\Test\GraphNode\Student;
use ObjectGraph\Test\GraphNode\User;
use ObjectGraph\Test\Resolver\UserResolver;
use ObjectGraph\Test\Schema\ScalarArraySchema;
use ObjectGraph\Test\Schema\StudentsArraySchema;
use PHPUnit\Framework\Constraint\IsType;
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
        $this->assertInstanceOf(DateTime::class, $graphNode->dateOfBirth);
        $this->assertEquals($expectedDateOfBirth, $graphNode->dateOfBirth);
        $this->assertEquals($expectedEmail, $graphNode->email);
        $this->assertEquals($schema, $graphNode->schema);
    }

    public function testObjectResolverWithNullData()
    {
        $resolver = new Resolver();

        $this->assertNull($resolver->resolveObject(null));
    }

    public function testArrayResolverWithNullData()
    {
        $resolver = new Resolver();

        $this->assertInternalType(IsType::TYPE_ARRAY, $resolver->resolveArray(null));
        $this->assertEmpty($resolver->resolveArray(null));
    }

    public function testScalarArray()
    {
        $data     = $this->loadJson('scalar-array.json');
        $resolver = new Resolver();

        $graphNode = $resolver->resolveObject($data, ScalarArraySchema::class);
        $this->assertArrayHasKey('field', $graphNode);

        $received = $graphNode->field;
        $this->assertInternalType(IsType::TYPE_ARRAY, $received);

        foreach ($received as $value) {
            $this->assertSame(1, $value);
        }
    }

    public function testGraphNodeArray()
    {
        $data     = $this->loadJson('students.json');
        $resolver = new Resolver();

        $graphNode = $resolver->resolveObject($data, StudentsArraySchema::class);
        $this->assertArrayHasKey('students', $graphNode);

        foreach ($graphNode->students as $student) {
            $this->assertInstanceOf(Student::class, $student);
        }
    }

    public function testNestedScalarArray()
    {
        $data = [
            [
                1,
                2,
                3,
            ],
        ];

        $resolver = new Resolver();
        $received = $resolver->resolveArray($data, Kind::SCALAR_ARRAY, ScalarType::INTEGER);

        $this->assertCount(1, $received);
        $this->assertInternalType(IsType::TYPE_ARRAY, $received[0]);

        foreach ($received[0] as $value) {
            $expectedValue = current($data[0]);

            $this->assertEquals($expectedValue, $value);

            next($data[0]);
        }
    }

    public function testNestedGraphNodeArray()
    {
        $data = [
            [
                (object)[
                    'field' => 1,
                ],
                (object)[
                    'field' => 2,
                ],
                (object)[
                    'field' => 3,
                ],
            ],
        ];

        $resolver = new Resolver();
        $received = $resolver->resolveArray($data, Kind::GRAPH_NODE_ARRAY);

        $this->assertCount(1, $received);
        $this->assertInternalType(IsType::TYPE_ARRAY, $received[0]);

        foreach ($received[0] as $value) {
            $expectedValue = current($data[0]);

            $this->assertInstanceOf(GraphNode::class, $value);
            $this->assertEquals($expectedValue->field, $value->field);

            next($data[0]);
        }
    }

    public function testNestedArray()
    {
        $data = [
            [
                (object)[
                    'field' => 1,
                ],
                'dummy',
            ],
        ];

        $resolver = new Resolver();
        $received = $resolver->resolveArray($data, Kind::ARRAY);

        $this->assertCount(1, $received);
        $this->assertInternalType(IsType::TYPE_ARRAY, $received[0]);
        $this->assertCount(2, $received[0]);

        $this->assertInstanceOf(GraphNode::class, $received[0][0]);
        $this->assertEquals($data[0][0]->field, $received[0][0]->field);
        $this->assertEquals($data[0][1], $received[0][1]);
    }

    public function testRawArray()
    {
        $expectedData = [
            'field1' => false,
            'field2' => 'dummy',
            'field3' => 1,
        ];

        $resolver = new Resolver();
        $received = $resolver->resolveArray($expectedData, Kind::RAW);

        $this->assertSame($expectedData, $received);
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

    /**
     * @expectedException \ObjectGraph\Exception\ObjectGraphException
     */
    public function testArrayResolverUnsupportedKindException()
    {
        $resolver = new Resolver();
        $resolver->resolveArray([1, 2, 3], 'dummy');
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
