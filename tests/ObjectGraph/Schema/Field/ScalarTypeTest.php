<?php

/**
 * This file is part of the Object Graph package.
 *
 * (c) Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\Schema\Field;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;

/**
 * Class ScalarTypeTest
 *
 * @package ObjectGraph\Schema\Field
 */
class ScalarTypeTest extends TestCase
{
    /**
     * @var ScalarType
     */
    protected $scalarType;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->scalarType = new ScalarType();

        parent::setUp();
    }


    public function testBoolean()
    {
        $tests = [
            'TRUE'  => true,
            'False' => false,
            0       => false,
            1       => true,
            'bob'   => true,
        ];

        foreach ($tests as $value => $expected) {
            $value = $this->scalarType->cast($value, ScalarType::BOOLEAN);

            $this->assertInternalType(IsType::TYPE_BOOL, $value);
            $this->assertSame($expected, $value);
        }
    }

    public function testDateTime()
    {
        $value = $this->scalarType->cast(null, ScalarType::DATE_TIME);
        $this->assertNull($value);

        $value = $this->scalarType->cast(null, ScalarType::TIMESTAMP);
        $this->assertNull($value);

        $timezone   = new DateTimeZone('Australia/Sydney');
        $scalarType = new ScalarType($timezone);

        $now = new DateTime('@' . time()); // don't care about ms
        $now->setTimezone($timezone);

        $value = $scalarType->cast($now->format(DateTime::RFC3339), ScalarType::DATE_TIME);
        $this->assertInstanceOf(DateTime::class, $value);
        $this->assertEquals($now, $value);

        $value = $scalarType->cast($now->format(DateTime::RFC3339), ScalarType::TIMESTAMP);
        $this->assertSame($now->getTimestamp(), $value);
    }
}
