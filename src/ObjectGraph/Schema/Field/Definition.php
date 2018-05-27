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

use Closure;
use ObjectGraph\Exception\InvalidArgumentException;

/**
 * Class Definition
 *
 * @package ObjectGraph\Schema\Field
 */
class Definition
{
    /**
     * @var null|mixed
     */
    protected $default;

    /**
     * @var null|Closure
     */
    protected $resolver;

    /**
     * @var null|string
     */
    protected $kind = Kind::RAW;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed|null $default
     *
     * @return Definition
     */
    public function setDefault($default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return callable|null
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * @param Closure $resolver
     *
     * @return Definition
     */
    public function setResolver(Closure $resolver = null): self
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * @param string|null $kind
     *
     * @return Definition
     */
    public function setKind(string $kind = null): self
    {
        switch ($kind) {
            case Kind::SCALAR:
            case Kind::GRAPH_NODE:
            case Kind::ARRAY:
            case Kind::RAW:
            case Kind::SCALAR_ARRAY:
            case Kind::GRAPH_NODE_ARRAY:
            case null:
                $this->kind = $kind;
                break;

            default:
                throw new InvalidArgumentException(sprintf(
                    'Invalid argument $kind, must be one of the %s constants',
                    Kind::class
                ));
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     *
     * @return Definition
     */
    public function setType(string $type = null): self
    {
        $this->type = $type;

        return $this;
    }
}
