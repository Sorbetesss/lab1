<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class ProjectServiceContainer extends Container
{
    protected $parameters = [];
    protected readonly \WeakReference $ref;
    protected \Closure $getService;

    public function __construct()
    {
        $containerRef = $this->ref = \WeakReference::create($this);
        $this->getService = static function () use ($containerRef) { return $containerRef->get()->getService(...\func_get_args()); };
        $this->services = $this->privates = [];
        $this->methodMap = [
            'bar' => 'getBarService',
            'baz' => 'getBazService',
        ];

        $this->aliases = [];
    }

    public function compile(): void
    {
        throw new LogicException('You cannot compile a dumped container that was already compiled.');
    }

    public function isCompiled(): bool
    {
        return true;
    }

    public function getRemovedIds(): array
    {
        return [
            '.service_locator.mtT6G8y' => true,
            'foo' => true,
        ];
    }

    /**
     * Gets the public 'bar' shared service.
     */
    protected static function getBarService($container): \stdClass
    {
        return $container->services['bar'] = new \stdClass((new \stdClass()), (new \stdClass()));
    }

    /**
     * Gets the public 'baz' shared service.
     */
    protected static function getBazService($container): \stdClass
    {
        return $container->services['baz'] = new \stdClass(new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'foo' => [false, 'foo', 'getFooService', false],
        ], [
            'foo' => '?',
        ]));
    }

    /**
     * Gets the private 'foo' service.
     */
    protected static function getFooService($container): \stdClass
    {
        $container->factories['service_container']['foo'] = function ($container) {
            return new \stdClass();
        };

        return $container->factories['service_container']['foo']($container);
    }
}
