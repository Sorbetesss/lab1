<?php

namespace ContainerUWVxv1z;

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
class Symfony_Component_AssetMapper_Tests_fixtures_AssetMapperTestAppKernelTestDebugContainer extends Container
{
    protected $targetDir;
    protected $parameters = [];
    protected \Closure $getService;

    public function __construct(private array $buildParameters = [], protected string $containerDir = __DIR__)
    {
        $this->targetDir = \dirname($containerDir);
        $this->parameters = $this->getDefaultParameters();

        $this->services = $this->privates = [];
        $this->syntheticIds = [
            'kernel' => true,
        ];
        $this->methodMap = [
            'event_dispatcher' => 'getEventDispatcherService',
            'http_kernel' => 'getHttpKernelService',
            'request_stack' => 'getRequestStackService',
        ];
        $this->fileMap = [
            'cache.app' => 'getCache_AppService',
            'cache.app_clearer' => 'getCache_AppClearerService',
            'cache.global_clearer' => 'getCache_GlobalClearerService',
            'cache.system' => 'getCache_SystemService',
            'cache.system_clearer' => 'getCache_SystemClearerService',
            'cache_warmer' => 'getCacheWarmerService',
            'console.command_loader' => 'getConsole_CommandLoaderService',
            'container.env_var_processors_locator' => 'getContainer_EnvVarProcessorsLocatorService',
            'container.get_routing_condition_service' => 'getContainer_GetRoutingConditionServiceService',
            'debug.error_handler_configurator' => 'getDebug_ErrorHandlerConfiguratorService',
            'error_controller' => 'getErrorControllerService',
            'public.assets.packages' => 'getPublic_Assets_PackagesService',
            'services_resetter' => 'getServicesResetterService',
            'test.client' => 'getTest_ClientService',
            'test.private_services_locator' => 'getTest_PrivateServicesLocatorService',
            'test.service_container' => 'getTest_ServiceContainerService',
        ];

        $this->aliases = [];

        $this->privates['service_container'] = static function ($container) {
            include_once \dirname(__DIR__, 6).'/AssetMapperInterface.php';
            include_once \dirname(__DIR__, 6).'/AssetMapper.php';
            include_once \dirname(__DIR__, 6).'/Factory/MappedAssetFactoryInterface.php';
            include_once \dirname(__DIR__, 6).'/Factory/CachedMappedAssetFactory.php';
            include_once \dirname(__DIR__, 6).'/AssetMapperRepository.php';
            include_once \dirname(__DIR__, 6).'/Path/PublicAssetsPathResolverInterface.php';
            include_once \dirname(__DIR__, 6).'/Path/PublicAssetsPathResolver.php';
            include_once \dirname(__DIR__, 6).'/AssetMapperDevServerSubscriber.php';
            include_once \dirname(__DIR__, 6).'/AssetMapperCompiler.php';
            include_once \dirname(__DIR__, 6).'/Factory/MappedAssetFactory.php';
        };
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
        return require $this->containerDir.\DIRECTORY_SEPARATOR.'removed-ids.php';
    }

    protected function load($file, $lazyLoad = true): mixed
    {
        if (class_exists($class = __NAMESPACE__.'\\'.$file, false)) {
            return $class::do($this, $lazyLoad);
        }

        if ('.' === $file[-4]) {
            $class = substr($class, 0, -4);
        } else {
            $file .= '.php';
        }

        $service = require $this->containerDir.\DIRECTORY_SEPARATOR.$file;

        return class_exists($class, false) ? $class::do($this, $lazyLoad) : $service;
    }

    protected function createProxy($class, \Closure $factory)
    {
        class_exists($class, false) || require __DIR__.'/'.$class.'.php';

        return $factory();
    }

    /**
     * Gets the public 'event_dispatcher' shared service.
     *
     * @return \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected static function getEventDispatcherService($container)
    {
        $container->services['event_dispatcher'] = $instance = new \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher(($container->privates['debug.event_dispatcher.inner'] ??= new \Symfony\Component\EventDispatcher\EventDispatcher()), ($container->privates['debug.stopwatch'] ??= new \Symfony\Component\Stopwatch\Stopwatch(true)), ($container->privates['logger'] ??= new \Psr\Log\NullLogger()), ($container->services['request_stack'] ??= new \Symfony\Component\HttpFoundation\RequestStack()));

        $instance->addListener('kernel.response', [#[\Closure(name: 'response_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\ResponseListener')] fn () => ($container->privates['response_listener'] ??= new \Symfony\Component\HttpKernel\EventListener\ResponseListener('UTF-8', false)), 'onKernelResponse'], 0);
        $instance->addListener('kernel.request', [#[\Closure(name: 'locale_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener')] fn () => ($container->privates['locale_listener'] ?? self::getLocaleListenerService($container)), 'setDefaultLocale'], 100);
        $instance->addListener('kernel.request', [#[\Closure(name: 'locale_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener')] fn () => ($container->privates['locale_listener'] ?? self::getLocaleListenerService($container)), 'onKernelRequest'], 16);
        $instance->addListener('kernel.finish_request', [#[\Closure(name: 'locale_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener')] fn () => ($container->privates['locale_listener'] ?? self::getLocaleListenerService($container)), 'onKernelFinishRequest'], 0);
        $instance->addListener('kernel.request', [#[\Closure(name: 'validate_request_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\ValidateRequestListener')] fn () => ($container->privates['validate_request_listener'] ??= new \Symfony\Component\HttpKernel\EventListener\ValidateRequestListener()), 'onKernelRequest'], 256);
        $instance->addListener('kernel.response', [#[\Closure(name: 'disallow_search_engine_index_response_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\DisallowRobotsIndexingListener')] fn () => ($container->privates['disallow_search_engine_index_response_listener'] ??= new \Symfony\Component\HttpKernel\EventListener\DisallowRobotsIndexingListener()), 'onResponse'], -255);
        $instance->addListener('kernel.controller_arguments', [#[\Closure(name: 'exception_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\ErrorListener')] fn () => ($container->privates['exception_listener'] ?? self::getExceptionListenerService($container)), 'onControllerArguments'], 0);
        $instance->addListener('kernel.exception', [#[\Closure(name: 'exception_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\ErrorListener')] fn () => ($container->privates['exception_listener'] ?? self::getExceptionListenerService($container)), 'logKernelException'], 0);
        $instance->addListener('kernel.exception', [#[\Closure(name: 'exception_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\ErrorListener')] fn () => ($container->privates['exception_listener'] ?? self::getExceptionListenerService($container)), 'onKernelException'], -128);
        $instance->addListener('kernel.response', [#[\Closure(name: 'exception_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\ErrorListener')] fn () => ($container->privates['exception_listener'] ?? self::getExceptionListenerService($container)), 'removeCspHeader'], -128);
        $instance->addListener('kernel.controller_arguments', [#[\Closure(name: 'controller.cache_attribute_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\CacheAttributeListener')] fn () => ($container->privates['controller.cache_attribute_listener'] ??= new \Symfony\Component\HttpKernel\EventListener\CacheAttributeListener()), 'onKernelControllerArguments'], 10);
        $instance->addListener('kernel.response', [#[\Closure(name: 'controller.cache_attribute_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\CacheAttributeListener')] fn () => ($container->privates['controller.cache_attribute_listener'] ??= new \Symfony\Component\HttpKernel\EventListener\CacheAttributeListener()), 'onKernelResponse'], -10);
        $instance->addListener('kernel.request', [#[\Closure(name: 'locale_aware_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleAwareListener')] fn () => ($container->privates['locale_aware_listener'] ?? self::getLocaleAwareListenerService($container)), 'onKernelRequest'], 15);
        $instance->addListener('kernel.finish_request', [#[\Closure(name: 'locale_aware_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleAwareListener')] fn () => ($container->privates['locale_aware_listener'] ?? self::getLocaleAwareListenerService($container)), 'onKernelFinishRequest'], -15);
        $instance->addListener('console.error', [#[\Closure(name: 'console.error_listener', class: 'Symfony\\Component\\Console\\EventListener\\ErrorListener')] fn () => ($container->privates['console.error_listener'] ?? $container->load('getConsole_ErrorListenerService')), 'onConsoleError'], -128);
        $instance->addListener('console.terminate', [#[\Closure(name: 'console.error_listener', class: 'Symfony\\Component\\Console\\EventListener\\ErrorListener')] fn () => ($container->privates['console.error_listener'] ?? $container->load('getConsole_ErrorListenerService')), 'onConsoleTerminate'], -128);
        $instance->addListener('console.error', [#[\Closure(name: 'console.suggest_missing_package_subscriber', class: 'Symfony\\Bundle\\FrameworkBundle\\EventListener\\SuggestMissingPackageSubscriber')] fn () => ($container->privates['console.suggest_missing_package_subscriber'] ??= new \Symfony\Bundle\FrameworkBundle\EventListener\SuggestMissingPackageSubscriber()), 'onConsoleError'], 0);
        $instance->addListener('kernel.request', [#[\Closure(name: 'asset_mapper.dev_server_subscriber', class: 'Symfony\\Component\\AssetMapper\\AssetMapperDevServerSubscriber')] fn () => ($container->privates['asset_mapper.dev_server_subscriber'] ?? self::getAssetMapper_DevServerSubscriberService($container)), 'onKernelRequest'], 35);
        $instance->addListener('kernel.request', [#[\Closure(name: 'debug.debug_handlers_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\DebugHandlersListener')] fn () => ($container->privates['debug.debug_handlers_listener'] ??= new \Symfony\Component\HttpKernel\EventListener\DebugHandlersListener()), 'configure'], 2048);
        $instance->addListener('console.command', [#[\Closure(name: 'debug.debug_handlers_listener', class: 'Symfony\\Component\\HttpKernel\\EventListener\\DebugHandlersListener')] fn () => ($container->privates['debug.debug_handlers_listener'] ??= new \Symfony\Component\HttpKernel\EventListener\DebugHandlersListener()), 'configure'], 2048);

        return $instance;
    }

    /**
     * Gets the public 'http_kernel' shared service.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernel
     */
    protected static function getHttpKernelService($container)
    {
        return $container->services['http_kernel'] = new \Symfony\Component\HttpKernel\HttpKernel(($container->services['event_dispatcher'] ?? self::getEventDispatcherService($container)), ($container->privates['debug.controller_resolver'] ?? self::getDebug_ControllerResolverService($container)), ($container->services['request_stack'] ??= new \Symfony\Component\HttpFoundation\RequestStack()), ($container->privates['debug.argument_resolver'] ?? self::getDebug_ArgumentResolverService($container)), true);
    }

    /**
     * Gets the public 'request_stack' shared service.
     *
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    protected static function getRequestStackService($container)
    {
        return $container->services['request_stack'] = new \Symfony\Component\HttpFoundation\RequestStack();
    }

    /**
     * Gets the private 'argument_metadata_factory' shared service.
     *
     * @return \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory
     */
    protected static function getArgumentMetadataFactoryService($container)
    {
        return $container->privates['argument_metadata_factory'] = new \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory();
    }

    /**
     * Gets the private 'asset_mapper' shared service.
     *
     * @return \Symfony\Component\AssetMapper\AssetMapper
     */
    protected static function getAssetMapperService($container)
    {
        $a = ($container->privates['asset_mapper.cached_mapped_asset_factory'] ?? self::getAssetMapper_CachedMappedAssetFactoryService($container));

        if (isset($container->privates['asset_mapper'])) {
            return $container->privates['asset_mapper'];
        }

        return $container->privates['asset_mapper'] = new \Symfony\Component\AssetMapper\AssetMapper(($container->privates['asset_mapper.repository'] ??= new \Symfony\Component\AssetMapper\AssetMapperRepository(['dir1' => '', 'dir2' => '', 'assets/vendor' => ''], \dirname(__DIR__, 4), [])), $a, ($container->privates['asset_mapper.public_assets_path_resolver'] ??= new \Symfony\Component\AssetMapper\Path\PublicAssetsPathResolver(\dirname(__DIR__, 4), '/assets/', 'public')));
    }

    /**
     * Gets the private 'asset_mapper.cached_mapped_asset_factory' shared service.
     *
     * @return \Symfony\Component\AssetMapper\Factory\CachedMappedAssetFactory
     */
    protected static function getAssetMapper_CachedMappedAssetFactoryService($container)
    {
        $a = ($container->privates['asset_mapper.cached_mapped_asset_factory.inner'] ?? self::getAssetMapper_CachedMappedAssetFactory_InnerService($container));

        if (isset($container->privates['asset_mapper.cached_mapped_asset_factory'])) {
            return $container->privates['asset_mapper.cached_mapped_asset_factory'];
        }

        return $container->privates['asset_mapper.cached_mapped_asset_factory'] = new \Symfony\Component\AssetMapper\Factory\CachedMappedAssetFactory($a, ($container->targetDir.''.'/asset_mapper'), true);
    }

    /**
     * Gets the private 'asset_mapper.cached_mapped_asset_factory.inner' shared service.
     *
     * @return \Symfony\Component\AssetMapper\Factory\MappedAssetFactory
     */
    protected static function getAssetMapper_CachedMappedAssetFactory_InnerService($container)
    {
        $a = ($container->privates['asset_mapper_compiler'] ?? self::getAssetMapperCompilerService($container));

        if (isset($container->privates['asset_mapper.cached_mapped_asset_factory.inner'])) {
            return $container->privates['asset_mapper.cached_mapped_asset_factory.inner'];
        }

        return $container->privates['asset_mapper.cached_mapped_asset_factory.inner'] = new \Symfony\Component\AssetMapper\Factory\MappedAssetFactory(($container->privates['asset_mapper.public_assets_path_resolver'] ??= new \Symfony\Component\AssetMapper\Path\PublicAssetsPathResolver(\dirname(__DIR__, 4), '/assets/', 'public')), $a, (\dirname(__DIR__, 4).'/assets/vendor'));
    }

    /**
     * Gets the private 'asset_mapper.dev_server_subscriber' shared service.
     *
     * @return \Symfony\Component\AssetMapper\AssetMapperDevServerSubscriber
     */
    protected static function getAssetMapper_DevServerSubscriberService($container)
    {
        return $container->privates['asset_mapper.dev_server_subscriber'] = new \Symfony\Component\AssetMapper\AssetMapperDevServerSubscriber(($container->privates['asset_mapper'] ?? self::getAssetMapperService($container)), '/assets/', [], ($container->privates['cache.asset_mapper'] ?? self::getCache_AssetMapperService($container)));
    }

    /**
     * Gets the private 'asset_mapper.public_assets_path_resolver' shared service.
     *
     * @return \Symfony\Component\AssetMapper\Path\PublicAssetsPathResolver
     */
    protected static function getAssetMapper_PublicAssetsPathResolverService($container)
    {
        return $container->privates['asset_mapper.public_assets_path_resolver'] = new \Symfony\Component\AssetMapper\Path\PublicAssetsPathResolver(\dirname(__DIR__, 4), '/assets/', 'public');
    }

    /**
     * Gets the private 'asset_mapper.repository' shared service.
     *
     * @return \Symfony\Component\AssetMapper\AssetMapperRepository
     */
    protected static function getAssetMapper_RepositoryService($container)
    {
        return $container->privates['asset_mapper.repository'] = new \Symfony\Component\AssetMapper\AssetMapperRepository(['dir1' => '', 'dir2' => '', 'assets/vendor' => ''], \dirname(__DIR__, 4), []);
    }

    /**
     * Gets the private 'asset_mapper_compiler' shared service.
     *
     * @return \Symfony\Component\AssetMapper\AssetMapperCompiler
     */
    protected static function getAssetMapperCompilerService($container)
    {
        return $container->privates['asset_mapper_compiler'] = new \Symfony\Component\AssetMapper\AssetMapperCompiler(new RewindableGenerator(function () use ($container) {
            yield 0 => ($container->privates['asset_mapper.compiler.css_asset_url_compiler'] ?? $container->load('getAssetMapper_Compiler_CssAssetUrlCompilerService'));
            yield 1 => ($container->privates['asset_mapper.compiler.source_mapping_urls_compiler'] ??= new \Symfony\Component\AssetMapper\Compiler\SourceMappingUrlsCompiler());
            yield 2 => ($container->privates['asset_mapper.compiler.javascript_import_path_compiler'] ?? $container->load('getAssetMapper_Compiler_JavascriptImportPathCompilerService'));
        }, 3), #[\Closure(name: 'asset_mapper', class: 'Symfony\\Component\\AssetMapper\\AssetMapper')] fn () => ($container->privates['asset_mapper'] ?? self::getAssetMapperService($container)));
    }

    /**
     * Gets the private 'cache.asset_mapper' shared service.
     *
     * @return \Symfony\Component\Cache\Adapter\AdapterInterface
     */
    protected static function getCache_AssetMapperService($container)
    {
        return $container->privates['cache.asset_mapper'] = \Symfony\Component\Cache\Adapter\AbstractAdapter::createSystemCache('zmqU2283Bj', 0, $container->getParameter('container.build_id'), ($container->targetDir.''.'/pools/system'), ($container->privates['logger'] ??= new \Psr\Log\NullLogger()));
    }

    /**
     * Gets the private 'controller.cache_attribute_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\CacheAttributeListener
     */
    protected static function getController_CacheAttributeListenerService($container)
    {
        return $container->privates['controller.cache_attribute_listener'] = new \Symfony\Component\HttpKernel\EventListener\CacheAttributeListener();
    }

    /**
     * Gets the private 'debug.argument_resolver' shared service.
     *
     * @return \Symfony\Component\HttpKernel\Controller\TraceableArgumentResolver
     */
    protected static function getDebug_ArgumentResolverService($container)
    {
        return $container->privates['debug.argument_resolver'] = new \Symfony\Component\HttpKernel\Controller\TraceableArgumentResolver(($container->privates['debug.argument_resolver.inner'] ?? self::getDebug_ArgumentResolver_InnerService($container)), ($container->privates['debug.stopwatch'] ??= new \Symfony\Component\Stopwatch\Stopwatch(true)));
    }

    /**
     * Gets the private 'debug.argument_resolver.inner' shared service.
     *
     * @return \Symfony\Component\HttpKernel\Controller\ArgumentResolver
     */
    protected static function getDebug_ArgumentResolver_InnerService($container)
    {
        return $container->privates['debug.argument_resolver.inner'] = new \Symfony\Component\HttpKernel\Controller\ArgumentResolver(($container->privates['argument_metadata_factory'] ??= new \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory()), new RewindableGenerator(function () use ($container) {
            yield 0 => ($container->privates['.debug.value_resolver.argument_resolver.backed_enum_resolver'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_BackedEnumResolverService'));
            yield 1 => ($container->privates['.debug.value_resolver.argument_resolver.datetime'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_DatetimeService'));
            yield 2 => ($container->privates['.debug.value_resolver.argument_resolver.request_attribute'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_RequestAttributeService'));
            yield 3 => ($container->privates['.debug.value_resolver.argument_resolver.request'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_RequestService'));
            yield 4 => ($container->privates['.debug.value_resolver.argument_resolver.session'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_SessionService'));
            yield 5 => ($container->privates['.debug.value_resolver.argument_resolver.service'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_ServiceService'));
            yield 6 => ($container->privates['.debug.value_resolver.argument_resolver.default'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_DefaultService'));
            yield 7 => ($container->privates['.debug.value_resolver.argument_resolver.variadic'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_VariadicService'));
            yield 8 => ($container->privates['.debug.value_resolver.argument_resolver.not_tagged_controller'] ?? $container->load('get_Debug_ValueResolver_ArgumentResolver_NotTaggedControllerService'));
        }, 9), new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\BackedEnumValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.backed_enum_resolver', 'get_Debug_ValueResolver_ArgumentResolver_BackedEnumResolverService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\DateTimeValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.datetime', 'get_Debug_ValueResolver_ArgumentResolver_DatetimeService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\DefaultValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.default', 'get_Debug_ValueResolver_ArgumentResolver_DefaultService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\QueryParameterValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.query_parameter_value_resolver', 'get_Debug_ValueResolver_ArgumentResolver_QueryParameterValueResolverService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\RequestAttributeValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.request_attribute', 'get_Debug_ValueResolver_ArgumentResolver_RequestAttributeService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\RequestPayloadValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.request_payload', 'get_Debug_ValueResolver_ArgumentResolver_RequestPayloadService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\RequestValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.request', 'get_Debug_ValueResolver_ArgumentResolver_RequestService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\ServiceValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.service', 'get_Debug_ValueResolver_ArgumentResolver_ServiceService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\SessionValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.session', 'get_Debug_ValueResolver_ArgumentResolver_SessionService', true],
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\VariadicValueResolver' => ['privates', '.debug.value_resolver.argument_resolver.variadic', 'get_Debug_ValueResolver_ArgumentResolver_VariadicService', true],
            'argument_resolver.not_tagged_controller' => ['privates', '.debug.value_resolver.argument_resolver.not_tagged_controller', 'get_Debug_ValueResolver_ArgumentResolver_NotTaggedControllerService', true],
        ], [
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\BackedEnumValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\DateTimeValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\DefaultValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\QueryParameterValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\RequestAttributeValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\RequestPayloadValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\RequestValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\ServiceValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\SessionValueResolver' => '?',
            'Symfony\\Component\\HttpKernel\\Controller\\ArgumentResolver\\VariadicValueResolver' => '?',
            'argument_resolver.not_tagged_controller' => '?',
        ]));
    }

    /**
     * Gets the private 'debug.controller_resolver' shared service.
     *
     * @return \Symfony\Component\HttpKernel\Controller\TraceableControllerResolver
     */
    protected static function getDebug_ControllerResolverService($container)
    {
        return $container->privates['debug.controller_resolver'] = new \Symfony\Component\HttpKernel\Controller\TraceableControllerResolver(($container->privates['debug.controller_resolver.inner'] ?? self::getDebug_ControllerResolver_InnerService($container)), ($container->privates['debug.stopwatch'] ??= new \Symfony\Component\Stopwatch\Stopwatch(true)));
    }

    /**
     * Gets the private 'debug.controller_resolver.inner' shared service.
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver
     */
    protected static function getDebug_ControllerResolver_InnerService($container)
    {
        return $container->privates['debug.controller_resolver.inner'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver($container, ($container->privates['logger'] ??= new \Psr\Log\NullLogger()));
    }

    /**
     * Gets the private 'debug.debug_handlers_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\DebugHandlersListener
     */
    protected static function getDebug_DebugHandlersListenerService($container)
    {
        return $container->privates['debug.debug_handlers_listener'] = new \Symfony\Component\HttpKernel\EventListener\DebugHandlersListener();
    }

    /**
     * Gets the private 'debug.event_dispatcher.inner' shared service.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected static function getDebug_EventDispatcher_InnerService($container)
    {
        return $container->privates['debug.event_dispatcher.inner'] = new \Symfony\Component\EventDispatcher\EventDispatcher();
    }

    /**
     * Gets the private 'debug.stopwatch' shared service.
     *
     * @return \Symfony\Component\Stopwatch\Stopwatch
     */
    protected static function getDebug_StopwatchService($container)
    {
        return $container->privates['debug.stopwatch'] = new \Symfony\Component\Stopwatch\Stopwatch(true);
    }

    /**
     * Gets the private 'disallow_search_engine_index_response_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\DisallowRobotsIndexingListener
     */
    protected static function getDisallowSearchEngineIndexResponseListenerService($container)
    {
        return $container->privates['disallow_search_engine_index_response_listener'] = new \Symfony\Component\HttpKernel\EventListener\DisallowRobotsIndexingListener();
    }

    /**
     * Gets the private 'exception_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\ErrorListener
     */
    protected static function getExceptionListenerService($container)
    {
        return $container->privates['exception_listener'] = new \Symfony\Component\HttpKernel\EventListener\ErrorListener('error_controller', ($container->privates['logger'] ??= new \Psr\Log\NullLogger()), true, []);
    }

    /**
     * Gets the private 'locale_aware_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\LocaleAwareListener
     */
    protected static function getLocaleAwareListenerService($container)
    {
        return $container->privates['locale_aware_listener'] = new \Symfony\Component\HttpKernel\EventListener\LocaleAwareListener(new RewindableGenerator(function () use ($container) {
            yield 0 => ($container->privates['slugger'] ??= new \Symfony\Component\String\Slugger\AsciiSlugger('en'));
        }, 1), ($container->services['request_stack'] ??= new \Symfony\Component\HttpFoundation\RequestStack()));
    }

    /**
     * Gets the private 'locale_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\LocaleListener
     */
    protected static function getLocaleListenerService($container)
    {
        return $container->privates['locale_listener'] = new \Symfony\Component\HttpKernel\EventListener\LocaleListener(($container->services['request_stack'] ??= new \Symfony\Component\HttpFoundation\RequestStack()), 'en', NULL, false, []);
    }

    /**
     * Gets the private 'logger' shared service.
     *
     * @return \Psr\Log\NullLogger
     */
    protected static function getLoggerService($container)
    {
        return $container->privates['logger'] = new \Psr\Log\NullLogger();
    }

    /**
     * Gets the private 'response_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\ResponseListener
     */
    protected static function getResponseListenerService($container)
    {
        return $container->privates['response_listener'] = new \Symfony\Component\HttpKernel\EventListener\ResponseListener('UTF-8', false);
    }

    /**
     * Gets the private 'validate_request_listener' shared service.
     *
     * @return \Symfony\Component\HttpKernel\EventListener\ValidateRequestListener
     */
    protected static function getValidateRequestListenerService($container)
    {
        return $container->privates['validate_request_listener'] = new \Symfony\Component\HttpKernel\EventListener\ValidateRequestListener();
    }

    public function getParameter(string $name): array|bool|string|int|float|\UnitEnum|null
    {
        if (isset($this->buildParameters[$name])) {
            return $this->buildParameters[$name];
        }

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
        if (isset($this->buildParameters[$name])) {
            return true;
        }

        return isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || \array_key_exists($name, $this->parameters);
    }

    public function setParameter(string $name, $value): void
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }

    public function getParameterBag(): ParameterBagInterface
    {
        if (!isset($this->parameterBag)) {
            $parameters = $this->parameters;
            foreach ($this->loadedDynamicParameters as $name => $loaded) {
                $parameters[$name] = $loaded ? $this->dynamicParameters[$name] : $this->getDynamicParameter($name);
            }
            foreach ($this->buildParameters as $name => $value) {
                $parameters[$name] = $value;
            }
            $this->parameterBag = new FrozenParameterBag($parameters);
        }

        return $this->parameterBag;
    }

    private $loadedDynamicParameters = [
        'kernel.runtime_environment' => false,
        'kernel.build_dir' => false,
        'kernel.cache_dir' => false,
        'debug.file_link_format' => false,
        'debug.container.dump' => false,
    ];
    private $dynamicParameters = [];

    private function getDynamicParameter(string $name)
    {
        $container = $this;
        $value = match ($name) {
            'kernel.runtime_environment' => $container->getEnv('default:kernel.environment:APP_RUNTIME_ENV'),
            'kernel.build_dir' => $container->targetDir.'',
            'kernel.cache_dir' => $container->targetDir.'',
            'debug.file_link_format' => $container->getEnv('default::SYMFONY_IDE'),
            'debug.container.dump' => ($container->targetDir.''.'/Symfony_Component_AssetMapper_Tests_fixtures_AssetMapperTestAppKernelTestDebugContainer.xml'),
            default => throw new ParameterNotFoundException($name),
        };
        $this->loadedDynamicParameters[$name] = true;

        return $this->dynamicParameters[$name] = $value;
    }

    protected function getDefaultParameters(): array
    {
        return [
            'kernel.project_dir' => \dirname(__DIR__, 4),
            'kernel.environment' => 'test',
            'kernel.debug' => true,
            'kernel.logs_dir' => (\dirname(__DIR__, 3).'/log'),
            'kernel.bundles' => [
                'FrameworkBundle' => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
            ],
            'kernel.bundles_metadata' => [
                'FrameworkBundle' => [
                    'path' => '/Applications/XAMPP/xamppfiles/htdocs/symfony/src/Symfony/Bundle/FrameworkBundle',
                    'namespace' => 'Symfony\\Bundle\\FrameworkBundle',
                ],
            ],
            'kernel.charset' => 'UTF-8',
            'kernel.container_class' => 'Symfony_Component_AssetMapper_Tests_fixtures_AssetMapperTestAppKernelTestDebugContainer',
            'event_dispatcher.event_aliases' => [
                'Symfony\\Component\\Console\\Event\\ConsoleCommandEvent' => 'console.command',
                'Symfony\\Component\\Console\\Event\\ConsoleErrorEvent' => 'console.error',
                'Symfony\\Component\\Console\\Event\\ConsoleSignalEvent' => 'console.signal',
                'Symfony\\Component\\Console\\Event\\ConsoleTerminateEvent' => 'console.terminate',
                'Symfony\\Component\\Form\\Event\\PreSubmitEvent' => 'form.pre_submit',
                'Symfony\\Component\\Form\\Event\\SubmitEvent' => 'form.submit',
                'Symfony\\Component\\Form\\Event\\PostSubmitEvent' => 'form.post_submit',
                'Symfony\\Component\\Form\\Event\\PreSetDataEvent' => 'form.pre_set_data',
                'Symfony\\Component\\Form\\Event\\PostSetDataEvent' => 'form.post_set_data',
                'Symfony\\Component\\HttpKernel\\Event\\ControllerArgumentsEvent' => 'kernel.controller_arguments',
                'Symfony\\Component\\HttpKernel\\Event\\ControllerEvent' => 'kernel.controller',
                'Symfony\\Component\\HttpKernel\\Event\\ResponseEvent' => 'kernel.response',
                'Symfony\\Component\\HttpKernel\\Event\\FinishRequestEvent' => 'kernel.finish_request',
                'Symfony\\Component\\HttpKernel\\Event\\RequestEvent' => 'kernel.request',
                'Symfony\\Component\\HttpKernel\\Event\\ViewEvent' => 'kernel.view',
                'Symfony\\Component\\HttpKernel\\Event\\ExceptionEvent' => 'kernel.exception',
                'Symfony\\Component\\HttpKernel\\Event\\TerminateEvent' => 'kernel.terminate',
                'Symfony\\Component\\Workflow\\Event\\GuardEvent' => 'workflow.guard',
                'Symfony\\Component\\Workflow\\Event\\LeaveEvent' => 'workflow.leave',
                'Symfony\\Component\\Workflow\\Event\\TransitionEvent' => 'workflow.transition',
                'Symfony\\Component\\Workflow\\Event\\EnterEvent' => 'workflow.enter',
                'Symfony\\Component\\Workflow\\Event\\EnteredEvent' => 'workflow.entered',
                'Symfony\\Component\\Workflow\\Event\\CompletedEvent' => 'workflow.completed',
                'Symfony\\Component\\Workflow\\Event\\AnnounceEvent' => 'workflow.announce',
            ],
            'fragment.renderer.hinclude.global_template' => NULL,
            'fragment.path' => '/_fragment',
            'kernel.http_method_override' => false,
            'kernel.trust_x_sendfile_type_header' => false,
            'kernel.trusted_hosts' => [

            ],
            'kernel.default_locale' => 'en',
            'kernel.enabled_locales' => [

            ],
            'kernel.error_controller' => 'error_controller',
            'test.client.parameters' => [

            ],
            'asset.request_context.base_path' => '',
            'asset.request_context.secure' => false,
            'debug.error_handler.throw_at' => -1,
            'data_collector.templates' => [

            ],
            'console.command.ids' => [

            ],
        ];
    }
}
