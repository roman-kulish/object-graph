<?php

/**
 * This file is part of the Object Graph package.
 *
 * Copyright (c) 2018 Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph;

/**
 * Class Memoize stores resolved values of GraphNode fields
 *
 * @package ObjectGraph
 */
class Memoize
{
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * Return cached value or resolve, cache and return a value for this property
     * using provided function
     *
     * @param string   $property
     * @param callable $func
     *
     * @return mixed
     */
    public function memoize(string $property, callable $func)
    {
        if (! $this->isCached($property)) {
            $this->cache[$property] = $func();
        }

        return $this->cache[$property];
    }

    /**
     * Clear cached value for this property
     *
     * @param string $property
     */
    public function clear(string $property)
    {
        unset($this->cache[$property]);
    }

    /**
     * Whether there is a cached value associated with this property
     *
     * @param string $property
     *
     * @return bool
     */
    public function isCached(string $property): bool
    {
        return array_key_exists($property, $this->cache);
    }
}
