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

use Flow\JSONPath\JSONPath;
use Flow\JSONPath\JSONPathException;
use ObjectGraph\Exception\ObjectGraphException;
use ObjectGraph\Resolver;
use stdClass;
use Traversable;

/**
 * Class Scope is shared between all fields resolvers and provides an isolation, because all resolvers
 * are bound to the Scope.
 *
 * Scope also provides a few useful features: an integration with JSONPath library to run XPath like queries
 * on a object; and access to the root resolver.
 *
 * @package ObjectGraph\Schema\Field
 */
class Scope
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Evaluate JSONPath expression on the given $data and return result
     *
     * @see https://github.com/FlowCommunications/JSONPath
     *
     * @param stdClass $data
     * @param string   $expression
     *
     * @return Traversable
     */
    public function query(stdClass $data, string $expression): Traversable
    {
        try {
            return (new JSONPath($data))->find($expression);
        } catch (JSONPathException $exception) {
            throw new ObjectGraphException(
                sprintf('Cannot evaluate JSONPath expression "%s", error: %s', $expression, $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    /**
     * @return Resolver
     */
    public function getRootResolver(): Resolver
    {
        return $this->resolver;
    }
}
