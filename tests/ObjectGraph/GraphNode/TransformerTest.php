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

use ObjectGraph\GraphNode;
use ObjectGraph\Resolver;
use ObjectGraph\Schema;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
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
}
