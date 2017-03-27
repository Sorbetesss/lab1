<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\LazyProxy\ProxyHelper;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Guilhem Niot <guilhem.niot@gmail.com>
 */
class ResolveBindingsPass extends AbstractRecursivePass
{
    private $usedBindings = array();
    private $unusedBindings = array();

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            parent::process($container);

            foreach ($this->unusedBindings as list($key, $serviceId)) {
                throw new InvalidArgumentException(sprintf('Unused binding "%s" in service "%s".', $key, $serviceId));
            }
        } finally {
            $this->usedBindings = array();
            $this->unusedBindings = array();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function processValue($value, $isRoot = false)
    {
        if (!$value instanceof Definition || $value->isAbstract() || !$bindings = $value->getBindings()) {
            return parent::processValue($value, $isRoot);
        }

        foreach ($bindings as $key => $binding) {
            list($bindingValue, $bindingId) = $binding->getValues();
            if (!isset($this->usedBindings[$bindingId])) {
                $this->unusedBindings[$bindingId] = array($key, $this->currentId);
            }

            if (isset($key[0]) && '$' === $key[0]) {
                continue;
            }

            if (null !== $bindingValue && !$bindingValue instanceof Reference && !$bindingValue instanceof Definition) {
                throw new InvalidArgumentException(sprintf('Invalid value for binding key "%s" for service "%s": expected null, an instance of %s or an instance of %s, %s given.', $key, $this->currentId, Reference::class, Definition::class, gettype($bindingValue)));
            }
        }

        $calls = $value->getMethodCalls();

        if ($constructor = $this->getConstructor($value, false)) {
            $calls[] = array($constructor, $value->getArguments());
        }

        foreach ($calls as $i => $call) {
            list($method, $arguments) = $call;

            if ($method instanceof \ReflectionFunctionAbstract) {
                $reflectionMethod = $method;
            } else {
                $reflectionMethod = $this->getReflectionMethod($value, $method);
            }

            foreach ($reflectionMethod->getParameters() as $key => $parameter) {
                if (array_key_exists($key, $arguments) && '' !== $arguments[$key]) {
                    continue;
                }

                if (array_key_exists('$'.$parameter->name, $bindings)) {
                    list($bindingValue, $bindingId) = $bindings['$'.$parameter->name]->getValues();
                    $arguments[$key] = $bindingValue;

                    $this->usedBindings[$bindingId] = true;
                    unset($this->unusedBindings[$bindingId]);

                    continue;
                }

                $typeHint = ProxyHelper::getTypeHint($reflectionMethod, $parameter, true);

                if (!isset($bindings[$typeHint])) {
                    continue;
                }

                list($bindingValue, $bindingId) = $bindings[$typeHint]->getValues();
                $arguments[$key] = $bindingValue;

                $this->usedBindings[$bindingId] = true;
                unset($this->unusedBindings[$bindingId]);
            }

            if ($arguments !== $call[1]) {
                ksort($arguments);
                $calls[$i][1] = $arguments;
            }
        }

        if ($constructor) {
            list(, $arguments) = array_pop($calls);

            if ($arguments !== $value->getArguments()) {
                $value->setArguments($arguments);
            }
        }

        if ($calls !== $value->getMethodCalls()) {
            $value->setMethodCalls($calls);
        }

        return parent::processValue($value, $isRoot);
    }
}
