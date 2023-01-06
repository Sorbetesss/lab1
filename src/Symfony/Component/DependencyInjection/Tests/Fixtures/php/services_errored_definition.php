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
class Symfony_DI_PhpDumper_Errored_Definition extends Container
{
    protected $parameters = [];
    protected readonly \WeakReference $ref;

    public function __construct()
    {
        $this->ref = \WeakReference::create($this);
        $this->parameters = $this->getDefaultParameters();

        $this->services = $this->privates = [];
        $this->syntheticIds = [
            'request' => true,
        ];
        $this->methodMap = [
            'BAR' => 'getBARService',
            'BAR2' => 'getBAR2Service',
            'a_service' => 'getAServiceService',
            'b_service' => 'getBServiceService',
            'bar' => 'getBar3Service',
            'bar2' => 'getBar22Service',
            'baz' => 'getBazService',
            'configured_service' => 'getConfiguredServiceService',
            'configured_service_simple' => 'getConfiguredServiceSimpleService',
            'decorator_service' => 'getDecoratorServiceService',
            'decorator_service_with_name' => 'getDecoratorServiceWithNameService',
            'deprecated_service' => 'getDeprecatedServiceService',
            'factory_service' => 'getFactoryServiceService',
            'factory_service_simple' => 'getFactoryServiceSimpleService',
            'foo' => 'getFooService',
            'foo.baz' => 'getFoo_BazService',
            'foo_bar' => 'getFooBarService',
            'foo_with_inline' => 'getFooWithInlineService',
            'lazy_context' => 'getLazyContextService',
            'lazy_context_ignore_invalid_ref' => 'getLazyContextIgnoreInvalidRefService',
            'method_call1' => 'getMethodCall1Service',
            'new_factory_service' => 'getNewFactoryServiceService',
            'preload_sidekick' => 'getPreloadSidekickService',
            'runtime_error' => 'getRuntimeErrorService',
            'service_from_static_method' => 'getServiceFromStaticMethodService',
            'tagged_iterator' => 'getTaggedIteratorService',
        ];
        $this->aliases = [
            'alias_for_alias' => 'foo',
            'alias_for_foo' => 'foo',
            'decorated' => 'decorator_service_with_name',
        ];
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
            'a_factory' => true,
            'configurator_service' => true,
            'configurator_service_simple' => true,
            'decorated.pif-pouf' => true,
            'decorator_service.inner' => true,
            'errored_definition' => true,
            'factory_simple' => true,
            'inlined' => true,
            'new_factory' => true,
            'tagged_iterator_foo' => true,
        ];
    }

    /**
     * Gets the public 'BAR' shared service.
     *
     * @return \stdClass
     */
    protected static function getBARService($container)
    {
        $container->services['BAR'] = $instance = new \stdClass();

        $instance->bar = ($container->services['bar'] ?? self::getBar3Service($container));

        return $instance;
    }

    /**
     * Gets the public 'BAR2' shared service.
     *
     * @return \stdClass
     */
    protected static function getBAR2Service($container)
    {
        return $container->services['BAR2'] = new \stdClass();
    }

    /**
     * Gets the public 'a_service' shared service.
     *
     * @return \Bar
     */
    protected static function getAServiceService($container)
    {
        return $container->services['a_service'] = ($container->privates['a_factory'] ??= new \Bar())->getBar();
    }

    /**
     * Gets the public 'b_service' shared service.
     *
     * @return \Bar
     */
    protected static function getBServiceService($container)
    {
        return $container->services['b_service'] = ($container->privates['a_factory'] ??= new \Bar())->getBar();
    }

    /**
     * Gets the public 'bar' shared service.
     *
     * @return \Bar\FooClass
     */
    protected static function getBar3Service($container)
    {
        $a = ($container->services['foo.baz'] ?? self::getFoo_BazService($container));

        $container->services['bar'] = $instance = new \Bar\FooClass('foo', $a, 'foo_bar');

        $a->configure($instance);

        return $instance;
    }

    /**
     * Gets the public 'bar2' shared service.
     *
     * @return \stdClass
     */
    protected static function getBar22Service($container)
    {
        return $container->services['bar2'] = new \stdClass();
    }

    /**
     * Gets the public 'baz' shared service.
     *
     * @return \Baz
     */
    protected static function getBazService($container)
    {
        $container->services['baz'] = $instance = new \Baz();

        $instance->setFoo(($container->services['foo_with_inline'] ?? self::getFooWithInlineService($container)));

        return $instance;
    }

    /**
     * Gets the public 'configured_service' shared service.
     *
     * @return \stdClass
     */
    protected static function getConfiguredServiceService($container)
    {
        $container->services['configured_service'] = $instance = new \stdClass();

        $a = new \ConfClass();
        $a->setFoo(($container->services['baz'] ?? self::getBazService($container)));

        $a->configureStdClass($instance);

        return $instance;
    }

    /**
     * Gets the public 'configured_service_simple' shared service.
     *
     * @return \stdClass
     */
    protected static function getConfiguredServiceSimpleService($container)
    {
        $container->services['configured_service_simple'] = $instance = new \stdClass();

        (new \ConfClass('bar'))->configureStdClass($instance);

        return $instance;
    }

    /**
     * Gets the public 'decorator_service' shared service.
     *
     * @return \stdClass
     */
    protected static function getDecoratorServiceService($container)
    {
        return $container->services['decorator_service'] = new \stdClass();
    }

    /**
     * Gets the public 'decorator_service_with_name' shared service.
     *
     * @return \stdClass
     */
    protected static function getDecoratorServiceWithNameService($container)
    {
        return $container->services['decorator_service_with_name'] = new \stdClass();
    }

    /**
     * Gets the public 'deprecated_service' shared service.
     *
     * @return \stdClass
     *
     * @deprecated Since vendor/package 1.1: The "deprecated_service" service is deprecated. You should stop using it, as it will be removed in the future.
     */
    protected static function getDeprecatedServiceService($container)
    {
        trigger_deprecation('vendor/package', '1.1', 'The "deprecated_service" service is deprecated. You should stop using it, as it will be removed in the future.');

        return $container->services['deprecated_service'] = new \stdClass();
    }

    /**
     * Gets the public 'factory_service' shared service.
     *
     * @return \Bar
     */
    protected static function getFactoryServiceService($container)
    {
        return $container->services['factory_service'] = ($container->services['foo.baz'] ?? self::getFoo_BazService($container))->getInstance();
    }

    /**
     * Gets the public 'factory_service_simple' shared service.
     *
     * @return \Bar
     */
    protected static function getFactoryServiceSimpleService($container)
    {
        return $container->services['factory_service_simple'] = self::getFactorySimpleService($container)->getInstance();
    }

    /**
     * Gets the public 'foo' shared service.
     *
     * @return \Bar\FooClass
     */
    protected static function getFooService($container)
    {
        $a = ($container->services['foo.baz'] ?? self::getFoo_BazService($container));

        $container->services['foo'] = $instance = \Bar\FooClass::getInstance('foo', $a, ['bar' => 'foo is bar', 'foobar' => 'bar'], true, $container);

        $instance->foo = 'bar';
        $instance->moo = $a;
        $instance->qux = ['bar' => 'foo is bar', 'foobar' => 'bar'];
        $instance->setBar(($container->services['bar'] ?? self::getBar3Service($container)));
        $instance->initialize();
        sc_configure($instance);

        return $instance;
    }

    /**
     * Gets the public 'foo.baz' shared service.
     *
     * @return \BazClass
     */
    protected static function getFoo_BazService($container)
    {
        $container->services['foo.baz'] = $instance = \BazClass::getInstance();

        \BazClass::configureStatic1($instance);

        return $instance;
    }

    /**
     * Gets the public 'foo_bar' service.
     *
     * @return \Bar\FooClass
     */
    protected static function getFooBarService($container)
    {
        $container->factories['foo_bar'] = static function ($container) {
            return new \Bar\FooClass(($container->services['deprecated_service'] ?? self::getDeprecatedServiceService($container)));
        };

        return $container->factories['foo_bar']($container);
    }

    /**
     * Gets the public 'foo_with_inline' shared service.
     *
     * @return \Foo
     */
    protected static function getFooWithInlineService($container)
    {
        $container->services['foo_with_inline'] = $instance = new \Foo();

        $a = new \Bar();
        $a->pub = 'pub';
        $a->setBaz(($container->services['baz'] ?? self::getBazService($container)));

        $instance->setBar($a);

        return $instance;
    }

    /**
     * Gets the public 'lazy_context' shared service.
     *
     * @return \LazyContext
     */
    protected static function getLazyContextService($container)
    {
        $containerRef = $container->ref;

        return $container->services['lazy_context'] = new \LazyContext(new RewindableGenerator(static function () use ($containerRef) {
            $container = $containerRef->get();

            yield 'k1' => ($container->services['foo.baz'] ?? self::getFoo_BazService($container));
            yield 'k2' => $container;
        }, 2), new RewindableGenerator(static function () {
            return new \EmptyIterator();
        }, 0));
    }

    /**
     * Gets the public 'lazy_context_ignore_invalid_ref' shared service.
     *
     * @return \LazyContext
     */
    protected static function getLazyContextIgnoreInvalidRefService($container)
    {
        $containerRef = $container->ref;

        return $container->services['lazy_context_ignore_invalid_ref'] = new \LazyContext(new RewindableGenerator(static function () use ($containerRef) {
            $container = $containerRef->get();

            yield 0 => ($container->services['foo.baz'] ?? self::getFoo_BazService($container));
        }, 1), new RewindableGenerator(static function () {
            return new \EmptyIterator();
        }, 0));
    }

    /**
     * Gets the public 'method_call1' shared service.
     *
     * @return \Bar\FooClass
     */
    protected static function getMethodCall1Service($container)
    {
        include_once '%path%foo.php';

        $container->services['method_call1'] = $instance = new \Bar\FooClass();

        $instance->setBar(($container->services['foo'] ?? self::getFooService($container)));
        $instance->setBar(NULL);
        $instance->setBar((($container->services['foo'] ?? self::getFooService($container))->foo() . (($container->hasParameter("foo")) ? ($container->getParameter("foo")) : ("default"))));

        return $instance;
    }

    /**
     * Gets the public 'new_factory_service' shared service.
     *
     * @return \FooBarBaz
     */
    protected static function getNewFactoryServiceService($container)
    {
        $a = new \FactoryClass();
        $a->foo = 'bar';

        $container->services['new_factory_service'] = $instance = $a->getInstance();

        $instance->foo = 'bar';

        return $instance;
    }

    /**
     * Gets the public 'preload_sidekick' shared service.
     *
     * @return \stdClass
     */
    protected static function getPreloadSidekickService($container)
    {
        return $container->services['preload_sidekick'] = new \stdClass();
    }

    /**
     * Gets the public 'runtime_error' shared service.
     *
     * @return \stdClass
     */
    protected static function getRuntimeErrorService($container)
    {
        return $container->services['runtime_error'] = new \stdClass(throw new RuntimeException('Service "errored_definition" is broken.'));
    }

    /**
     * Gets the public 'service_from_static_method' shared service.
     *
     * @return \Bar\FooClass
     */
    protected static function getServiceFromStaticMethodService($container)
    {
        return $container->services['service_from_static_method'] = \Bar\FooClass::getInstance();
    }

    /**
     * Gets the public 'tagged_iterator' shared service.
     *
     * @return \Bar
     */
    protected static function getTaggedIteratorService($container)
    {
        $containerRef = $container->ref;

        return $container->services['tagged_iterator'] = new \Bar(new RewindableGenerator(static function () use ($containerRef) {
            $container = $containerRef->get();

            yield 0 => ($container->services['foo'] ?? self::getFooService($container));
            yield 1 => ($container->privates['tagged_iterator_foo'] ??= new \Bar());
        }, 2));
    }

    /**
     * Gets the private 'factory_simple' shared service.
     *
     * @return \SimpleFactoryClass
     *
     * @deprecated Since vendor/package 1.1: The "factory_simple" service is deprecated. You should stop using it, as it will be removed in the future.
     */
    protected static function getFactorySimpleService($container)
    {
        trigger_deprecation('vendor/package', '1.1', 'The "factory_simple" service is deprecated. You should stop using it, as it will be removed in the future.');

        return new \SimpleFactoryClass('foo');
    }

    public function getParameter(string $name): array|bool|string|int|float|\UnitEnum|null
    {
        if (!(isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || \array_key_exists($name, $this->parameters))) {
            throw new ParameterNotFoundException($name);
        }
        if (isset($this->loadedDynamicParameters[$name])) {
            return $this->loadedDynamicParameters[$name] ? $this->dynamicParameters[$name] : $this->getDynamicParameter($name);
        }

        return $this->parameters[$name];
    }

    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || \array_key_exists($name, $this->parameters);
    }

    public function setParameter(string $name, $value): void
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }

    public function getParameterBag(): ParameterBagInterface
    {
        if (null === $this->parameterBag) {
            $parameters = $this->parameters;
            foreach ($this->loadedDynamicParameters as $name => $loaded) {
                $parameters[$name] = $loaded ? $this->dynamicParameters[$name] : $this->getDynamicParameter($name);
            }
            $this->parameterBag = new FrozenParameterBag($parameters);
        }

        return $this->parameterBag;
    }

    private $loadedDynamicParameters = [];
    private $dynamicParameters = [];

    private function getDynamicParameter(string $name)
    {
        throw new ParameterNotFoundException($name);
    }

    protected function getDefaultParameters(): array
    {
        return [
            'baz_class' => 'BazClass',
            'foo_class' => 'Bar\\FooClass',
            'foo' => 'bar',
            'foo_bar' => 'foo_bar',
        ];
    }
}
