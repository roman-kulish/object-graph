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

use DateTime;
use DateTimeZone;
use Exception;
use ObjectGraph\Exception\InvalidArgumentException;

/**
 * Class ScalarType
 *
 * @package ObjectGraph\Schema\Field
 */
class ScalarType
{
    const BOOLEAN = 'bool';
    const INTEGER = 'int';
    const FLOAT = 'float';
    const STRING = 'string';
    const DATE_TIME = 'datetime';
    const TIMESTAMP = 'timestamp';

    /**
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * @param DateTimeZone|null $timezone
     */
    public function __construct(DateTimeZone $timezone = null)
    {
        $this->timezone = ($timezone ?: new DateTimeZone(date_default_timezone_get()));
    }

    /**
     * Cast given scalar $value to the given $type
     *
     * @param mixed       $value
     * @param string|null $type
     *
     * @return mixed
     */
    public function cast($value, string $type = null)
    {
        switch ($type) {
            case self::BOOLEAN:
                return $this->asBoolean($value);

            case self::INTEGER:
            case self::FLOAT:
            case self::STRING:
                settype($value, $type);

                return $value;

            case self::DATE_TIME:
            case self::TIMESTAMP:
                return $this->asDateTime($value, (self::TIMESTAMP === $type));

            case null:
                return $value;

            default:
                throw new InvalidArgumentException(sprintf(
                    'Invalid argument $type, must be one of the %s constants',
                    ScalarType::class
                ));
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function asBoolean($value)
    {
        if (is_string($value)) {
            switch (strtolower($value)) {
                case 'true':
                    return true;

                case 'false':
                    return false;
            }
        }

        return (bool)$value;
    }

    /**
     * @param mixed $value
     * @param bool  $asTimestamp
     *
     * @return DateTime|int|null
     */
    protected function asDateTime($value, $asTimestamp = false)
    {
        if (null === $value) {
            return null;
        }

        try {
            $datetime = new DateTime($value);
            $datetime->setTimezone($this->timezone);

            return ($asTimestamp ? $datetime->getTimestamp() : $datetime);
        } catch (Exception $exception) {
            throw new InvalidArgumentException(
                sprintf('Cannot cast "%s" into a %s instance', $value, DateTime::class),
                0,
                $exception
            );
        }
    }
}
