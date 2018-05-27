<?php

/**
 * This file is part of the Object Graph package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\GraphNode;

use InvalidArgumentException;
use ObjectGraph\GraphNode;
use ObjectGraph\Resolver;
use ObjectGraph\Schema;
use ObjectGraph\Test\Schema\StudentsArraySchema;
use ObjectGraph\Test\Schema\YoutubeSchema;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

/**
 * Class TransformerTest
 *
 * @package ObjectGraph\GraphNode
 */
class TransformerTest extends TestCase
{
    protected $expectedFirstName = 'Bob';
    protected $expectedLastName = 'Marley';

    protected $expectedInfo;

    protected $expectedAlbums = [
        'The Wailing Wailers',
        'Soul Rebels',
        'Soul Revolution',
    ];

    /**
     * @var GraphNode
     */
    protected $graphNode;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->expectedInfo = (object)[
            'religion' => 'Rastafari',
            'born'     => '6 February 1945',
        ];

        $data = (object)[
            'firstName' => $this->expectedFirstName,
            'lastName'  => $this->expectedLastName,
            'info'      => $this->expectedInfo,
            'albums'    => $this->expectedAlbums,
        ];

        $this->graphNode = new GraphNode($data, new Schema(new Resolver()));

        parent::setUp();
    }

    public function testAsObject()
    {
        $received = $this->graphNode->asObject();

        $this->assertInstanceOf(stdClass::class, $received);
        $this->assertObjectHasAttribute('firstName', $received);
        $this->assertObjectHasAttribute('lastName', $received);
        $this->assertObjectHasAttribute('info', $received);
        $this->assertObjectHasAttribute('albums', $received);

        $this->assertEquals($this->expectedFirstName, $received->firstName);
        $this->assertEquals($this->expectedLastName, $received->lastName);
        $this->assertEquals($this->expectedInfo, $received->info);
        $this->assertEquals($this->expectedAlbums, $received->albums);
    }

    public function testAsArray()
    {
        $received = $this->graphNode->asArray();

        $this->assertInternalType(IsType::TYPE_ARRAY, $received);
        $this->assertArrayHasKey('firstName', $received);
        $this->assertArrayHasKey('lastName', $received);
        $this->assertArrayHasKey('info', $received);
        $this->assertArrayHasKey('albums', $received);

        $this->assertEquals($this->expectedFirstName, $received['firstName']);
        $this->assertEquals($this->expectedLastName, $received['lastName']);
        $this->assertEquals($this->expectedInfo, $received['info']);
        $this->assertEquals($this->expectedAlbums, $received['albums']);
    }

    public function testNestedArrayObject()
    {
        $data     = $this->loadJson('students.json');
        $resolver = new Resolver();

        $graphNode = $resolver->resolveObject($data, StudentsArraySchema::class);
        $data      = $graphNode->asArray();

        foreach ($data['students'] as $student) {
            $this->assertInternalType(IsType::TYPE_ARRAY, $student);
        }
    }

    public function testNestedArray()
    {
        $data     = $this->loadJson('students.json');
        $resolver = new Resolver();

        $graphNode = $resolver->resolveObject($data, StudentsArraySchema::class);
        $data      = $graphNode->asObject();

        foreach ($data->students as $student) {
            $this->assertInstanceOf(stdClass::class, $student);
        }
    }

    public function testComplexGraphNodeAsObject()
    {
        $data     = $this->loadJson('youtube.json');
        $resolver = new Resolver();

        $graphNode = $resolver->resolveObject($data, YoutubeSchema::class);
        $data      = $graphNode->asObject();

        $this->assertObjectHasAttribute('kind', $data);
        $this->assertObjectHasAttribute('etag', $data);
        $this->assertObjectHasAttribute('nextPageToken', $data);
        $this->assertObjectHasAttribute('regionCode', $data);
        $this->assertObjectHasAttribute('pageInfo', $data);
        $this->assertObjectHasAttribute('items', $data);

        $this->assertInstanceOf(stdClass::class, $data->pageInfo);

        $pageInfo = $data->pageInfo;

        $this->assertObjectHasAttribute('totalResults', $pageInfo);
        $this->assertObjectHasAttribute('resultsPerPage', $pageInfo);

        $this->assertInternalType(IsType::TYPE_ARRAY, $data->items);

        foreach ($data->items as $item) {
            $this->assertInstanceOf(stdClass::class, $item);
            $this->assertObjectHasAttribute('kind', $item);
            $this->assertObjectHasAttribute('etag', $item);
            $this->assertObjectHasAttribute('id', $item);

            $this->assertInstanceOf(stdClass::class, $item->id);

            $id = $item->id;

            $this->assertObjectHasAttribute('kind', $id);
            $this->assertObjectHasAttribute('channelId', $id);
            $this->assertObjectHasAttribute('videoId', $id);
        }
    }

    public function testComplexGraphNodeAsArray()
    {
        $data     = $this->loadJson('youtube.json');
        $resolver = new Resolver();

        $graphNode = $resolver->resolveObject($data, YoutubeSchema::class);
        $data      = $graphNode->asArray();

        $this->assertArrayHasKey('kind', $data);
        $this->assertArrayHasKey('etag', $data);
        $this->assertArrayHasKey('nextPageToken', $data);
        $this->assertArrayHasKey('regionCode', $data);
        $this->assertArrayHasKey('pageInfo', $data);
        $this->assertArrayHasKey('items', $data);

        $this->assertInternalType(IsType::TYPE_ARRAY, $data['pageInfo']);

        $pageInfo = $data['pageInfo'];

        $this->assertArrayHasKey('totalResults', $pageInfo);
        $this->assertArrayHasKey('resultsPerPage', $pageInfo);

        $this->assertInternalType(IsType::TYPE_ARRAY, $data['items']);

        foreach ($data['items'] as $item) {
            $this->assertInternalType(IsType::TYPE_ARRAY, $item);
            $this->assertArrayHasKey('kind', $item);
            $this->assertArrayHasKey('etag', $item);
            $this->assertArrayHasKey('id', $item);

            $this->assertInternalType(IsType::TYPE_ARRAY, $item['id']);

            $id = $item['id'];

            $this->assertArrayHasKey('kind', $id);
            $this->assertArrayHasKey('channelId', $id);
            $this->assertArrayHasKey('videoId', $id);
        }
    }

    /**
     * @param string $filename
     *
     * @return stdClass
     */
    protected function loadJson(string $filename): stdClass
    {
        $path = dirname(__DIR__) . '/Test/Data/' . $filename;

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
