<?php

/**
 * This file is part of the Object Graph package.
 *
 * Copyright (c) 2018 Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\GraphNode;

use ObjectGraph\GraphNode;
use stdClass;

/**
 * Class Transformer
 *
 * @package ObjectGraph\GraphNode
 */
class Transformer
{
    /**
     * @var GraphNode
     */
    protected $graphNode;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @param GraphNode $graphNode
     * @param array     $fields
     */
    public function __construct(GraphNode $graphNode, array $fields)
    {
        $this->graphNode = $graphNode;
        $this->fields    = $fields;
    }

    /**
     * Return GraphNode representation as an object
     *
     * @return stdClass
     */
    public function asObject(): stdClass
    {
        $result = new stdClass();

        foreach ($this->fields as $field) {
            $value = $this->graphNode->$field;

            switch (true) {
                case ($value instanceof GraphNode):
                    $value = $value->asObject();
                    break;

                case is_array($value):
                    $value = $this->mapArray($value, true);
                    break;
            }

            $result->$field = $value;
        }

        return $result;
    }

    /**
     * Return GraphNode representation as an array
     *
     * @return array
     */
    public function asArray(): array
    {
        $result = [];

        foreach ($this->fields as $field) {
            $value = $this->graphNode->$field;

            switch (true) {
                case ($value instanceof GraphNode):
                    $value = $value->asArray();
                    break;

                case is_array($value):
                    $value = $this->mapArray($value, false);
                    break;
            }

            $result[$field] = $value;
        }

        return $result;
    }

    /**
     * Map nested array
     *
     * @param array $source
     * @param bool  $asObject
     *
     * @return array
     */
    protected function mapArray(array $source, bool $asObject): array
    {
        $result = [];

        foreach ($source as $index => $value) {
            switch (true) {
                case ($value instanceof GraphNode):
                    $value = ($asObject ? $value->asObject() : $value->asArray());
                    break;

                case is_array($value):
                    $value = $this->mapArray($value, $asObject);
                    break;
            }

            $result[$index] = $value;
        }

        return $result;
    }
}
