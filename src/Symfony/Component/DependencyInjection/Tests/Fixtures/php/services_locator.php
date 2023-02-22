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

    public function __construct()
    {
        $this->ref = \WeakReference::create($this);
        $this->services = $this->privates = [];
        $this->methodMap = [
            'bar_service' => 'getBarServiceService',
            'foo_service' => 'getFooServiceService',
            'translator.loader_1' => 'getTranslator_Loader1Service',
            'translator.loader_2' => 'getTranslator_Loader2Service',
            'translator.loader_3' => 'getTranslator_Loader3Service',
            'translator_1' => 'getTranslator1Service',
            'translator_2' => 'getTranslator2Service',
            'translator_3' => 'getTranslator3Service',
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
            'baz_service' => true,
            'translator.loader_1_locator' => true,
            'translator.loader_2_locator' => true,
            'translator.loader_3_locator' => true,
        ];
    }

    /**
     * Gets the public 'bar_service' shared service.
     */
    protected static function getBarServiceService($container): \stdClass
    {
        return $container->services['bar_service'] = new \stdClass(($container->privates['baz_service'] ??= new \stdClass()));
    }

    /**
     * Gets the public 'foo_service' shared service.
     */
    protected static function getFooServiceService($container): \Symfony\Component\DependencyInjection\ServiceLocator
    {
        $containerRef = $container->ref;

        return $container->services['foo_service'] = new \Symfony\Component\DependencyInjection\ServiceLocator(['bar' => #[\Closure(name: 'bar_service', class: 'stdClass')] function () use ($containerRef) {
            $container = $containerRef->get();

            return ($container->services['bar_service'] ?? self::getBarServiceService($container));
        }, 'baz' => #[\Closure(name: 'baz_service', class: 'stdClass')] function () use ($containerRef): \stdClass {
            $container = $containerRef->get();

            return ($container->privates['baz_service'] ??= new \stdClass());
        }, 'nil' => fn () => NULL]);
    }

    /**
     * Gets the public 'translator.loader_1' shared service.
     */
    protected static function getTranslator_Loader1Service($container): \stdClass
    {
        return $container->services['translator.loader_1'] = new \stdClass();
    }

    /**
     * Gets the public 'translator.loader_2' shared service.
     */
    protected static function getTranslator_Loader2Service($container): \stdClass
    {
        return $container->services['translator.loader_2'] = new \stdClass();
    }

    /**
     * Gets the public 'translator.loader_3' shared service.
     */
    protected static function getTranslator_Loader3Service($container): \stdClass
    {
        return $container->services['translator.loader_3'] = new \stdClass();
    }

    /**
     * Gets the public 'translator_1' shared service.
     */
    protected static function getTranslator1Service($container): \Symfony\Component\DependencyInjection\Tests\Fixtures\StubbedTranslator
    {
        $containerRef = $container->ref;

        return $container->services['translator_1'] = new \Symfony\Component\DependencyInjection\Tests\Fixtures\StubbedTranslator(new \Symfony\Component\DependencyInjection\ServiceLocator(['translator.loader_1' => #[\Closure(name: 'translator.loader_1', class: 'stdClass')] function () use ($containerRef) {
            $container = $containerRef->get();

            return ($container->services['translator.loader_1'] ??= new \stdClass());
        }]));
    }

    /**
     * Gets the public 'translator_2' shared service.
     */
    protected static function getTranslator2Service($container): \Symfony\Component\DependencyInjection\Tests\Fixtures\StubbedTranslator
    {
        $containerRef = $container->ref;

        $container->services['translator_2'] = $instance = new \Symfony\Component\DependencyInjection\Tests\Fixtures\StubbedTranslator(new \Symfony\Component\DependencyInjection\ServiceLocator(['translator.loader_2' => #[\Closure(name: 'translator.loader_2', class: 'stdClass')] function () use ($containerRef) {
            $container = $containerRef->get();

            return ($container->services['translator.loader_2'] ??= new \stdClass());
        }]));

        $instance->addResource('db', ($container->services['translator.loader_2'] ??= new \stdClass()), 'nl');

        return $instance;
    }

    /**
     * Gets the public 'translator_3' shared service.
     */
    protected static function getTranslator3Service($container): \Symfony\Component\DependencyInjection\Tests\Fixtures\StubbedTranslator
    {
        $containerRef = $container->ref;

        $container->services['translator_3'] = $instance = new \Symfony\Component\DependencyInjection\Tests\Fixtures\StubbedTranslator(new \Symfony\Component\DependencyInjection\ServiceLocator(['translator.loader_3' => #[\Closure(name: 'translator.loader_3', class: 'stdClass')] function () use ($containerRef) {
            $container = $containerRef->get();

            return ($container->services['translator.loader_3'] ??= new \stdClass());
        }]));

        $a = ($container->services['translator.loader_3'] ??= new \stdClass());

        $instance->addResource('db', $a, 'nl');
        $instance->addResource('db', $a, 'en');

        return $instance;
    }
}
