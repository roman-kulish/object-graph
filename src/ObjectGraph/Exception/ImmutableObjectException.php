<?php

/**
 * This file is part of the Object Graph package.
 *
 * Copyright (c) 2018 Roman Kulish <roman.kulish@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ObjectGraph\Exception;

use LogicException;

/**
 * Class ImmutableObjectException
 *
 * @package ObjectGraph\Exception
 */
class ImmutableObjectException extends LogicException implements ObjectGraphExceptionInterface
{

}
