<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\DependencyInjection\Compiler;

use Symphony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symphony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symphony\Component\DependencyInjection\ContainerInterface;
use Symphony\Component\DependencyInjection\Reference;

/**
 * Checks that all references are pointing to a valid service.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CheckExceptionOnInvalidReferenceBehaviorPass extends AbstractRecursivePass
{
    protected function processValue($value, $isRoot = false)
    {
        if (!$value instanceof Reference) {
            return parent::processValue($value, $isRoot);
        }
        if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE >= $value->getInvalidBehavior() && !$this->container->has($id = (string) $value)) {
            throw new ServiceNotFoundException($id, $this->currentId);
        }
        if (ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE === $value->getInvalidBehavior() && $this->container->has($id = (string) $value) && !$this->container->findDefinition($id)->isShared()) {
            throw new InvalidArgumentException(sprintf('Invalid ignore-on-uninitialized reference found in service "%s": target service "%s" is not shared.', $this->currentId, $id));
        }

        return $value;
    }
}
