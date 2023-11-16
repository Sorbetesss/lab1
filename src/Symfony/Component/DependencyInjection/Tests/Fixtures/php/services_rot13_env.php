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
class Symfony_DI_PhpDumper_Test_Rot13Parameters extends Container
{
    protected $parameters = [];
    protected \Closure $getService;

    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();

        $this->services = $this->privates = [];
        $this->methodMap = [
            'Symfony\\Component\\DependencyInjection\\Tests\\Dumper\\Rot13EnvVarProcessor' => 'getRot13EnvVarProcessorService',
            'container.env_var_processors_locator' => 'getContainer_EnvVarProcessorsLocatorService',
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
            '.service_locator.LaZ1R3K' => true,
        ];
    }

    /**
     * Gets the public 'Symfony\Component\DependencyInjection\Tests\Dumper\Rot13EnvVarProcessor' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\Tests\Dumper\Rot13EnvVarProcessor
     */
    protected static function getRot13EnvVarProcessorService($container)
    {
        return $container->services['Symfony\\Component\\DependencyInjection\\Tests\\Dumper\\Rot13EnvVarProcessor'] = new \Symfony\Component\DependencyInjection\Tests\Dumper\Rot13EnvVarProcessor();
    }

    /**
     * Gets the public 'container.env_var_processors_locator' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    protected static function getContainer_EnvVarProcessorsLocatorService($container)
    {
        return $container->services['container.env_var_processors_locator'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'rot13' => ['services', 'Symfony\\Component\\DependencyInjection\\Tests\\Dumper\\Rot13EnvVarProcessor', 'getRot13EnvVarProcessorService', false],
        ], [
            'rot13' => '?',
        ]);
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
        if (!isset($this->parameterBag)) {
            $parameters = $this->parameters;
            foreach ($this->loadedDynamicParameters as $name => $loaded) {
                $parameters[$name] = $loaded ? $this->dynamicParameters[$name] : $this->getDynamicParameter($name);
            }
            $this->parameterBag = new FrozenParameterBag($parameters);
        }

        return $this->parameterBag;
    }

    private $loadedDynamicParameters = [
        'hello' => false,
    ];
    private $dynamicParameters = [];

    private function getDynamicParameter(string $name)
    {
        $container = $this;
        $value = match ($name) {
            'hello' => $container->getEnv('rot13:foo'),
            default => throw new ParameterNotFoundException($name),
        };
        $this->loadedDynamicParameters[$name] = true;

        return $this->dynamicParameters[$name] = $value;
    }

    protected function getDefaultParameters(): array
    {
        return [
            'env(foo)' => 'jbeyq',
        ];
    }
}
