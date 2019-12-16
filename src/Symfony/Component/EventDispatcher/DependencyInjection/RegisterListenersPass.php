<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\DependencyInjection;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ListenerProvider\LazyListenerProvider;
use Symfony\Component\EventDispatcher\ListenerProvider\SimpleListenerProvider;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Compiler pass to register tagged services for an event dispatcher.
 */
class RegisterListenersPass implements CompilerPassInterface
{
    protected $dispatcherService;
    protected $listenerTag;
    protected $subscriberTag;
    protected $eventAliasesParameter;
    protected $freezeEventsParameter;

    private $hotPathEvents = [];
    private $hotPathTagName;

    public function __construct(string $dispatcherService = 'event_dispatcher', string $listenerTag = 'kernel.event_listener', string $subscriberTag = 'kernel.event_subscriber', string $eventAliasesParameter = 'event_dispatcher.event_aliases', string $freezeEventsParameter = 'event_dispatcher.freeze_events')
    {
        $this->dispatcherService = $dispatcherService;
        $this->listenerTag = $listenerTag;
        $this->subscriberTag = $subscriberTag;
        $this->eventAliasesParameter = $eventAliasesParameter;
        $this->freezeEventsParameter = $freezeEventsParameter;
    }

    public function setHotPathEvents(array $hotPathEvents, $tagName = 'container.hot_path')
    {
        $this->hotPathEvents = array_flip($hotPathEvents);
        $this->hotPathTagName = $tagName;

        return $this;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->dispatcherService) && !$container->hasAlias($this->dispatcherService)) {
            return;
        }

        if ($container->hasParameter($this->eventAliasesParameter)) {
            $aliases = $container->getParameter($this->eventAliasesParameter);
            $container->getParameterBag()->remove($this->eventAliasesParameter);
        } else {
            $aliases = [];
        }
        $definition = $container->findDefinition($this->dispatcherService);

        $frozenEvents = [];
        if ($container->hasParameter($this->freezeEventsParameter)) {
            foreach ($container->getParameter($this->freezeEventsParameter) as $event) {
                $frozenEvents[$event] = [];
            }
        }

        foreach ($container->findTaggedServiceIds($this->listenerTag, true) as $id => $events) {
            foreach ($events as $event) {
                $priority = isset($event['priority']) ? $event['priority'] : 0;

                if (!isset($event['event'])) {
                    if ($container->getDefinition($id)->hasTag($this->subscriberTag)) {
                        continue;
                    }

                    $event['method'] = $event['method'] ?? '__invoke';
                    $event['event'] = $this->getEventFromTypeDeclaration($container, $id, $event['method']);
                }

                $event['event'] = $aliases[$event['event']] ?? $event['event'];

                if (!isset($event['method'])) {
                    $event['method'] = 'on'.preg_replace_callback([
                        '/(?<=\b)[a-z]/i',
                        '/[^a-z0-9]/i',
                    ], function ($matches) { return strtoupper($matches[0]); }, $event['event']);
                    $event['method'] = preg_replace('/[^a-z0-9]/i', '', $event['method']);

                    if (null !== ($class = $container->getDefinition($id)->getClass()) && ($r = $container->getReflectionClass($class, false)) && !$r->hasMethod($event['method']) && $r->hasMethod('__invoke')) {
                        $event['method'] = '__invoke';
                    }
                }

                if (isset($frozenEvents[$event['event']])) {
                    $frozenEvents[$event['event']][$priority][] = [new Reference($id), $event['method']];
                } else {
                    $definition->addMethodCall('addListener', [$event['event'], [new ServiceClosureArgument(new Reference($id)), $event['method']], $priority]);
                }

                if (isset($this->hotPathEvents[$event['event']])) {
                    $container->getDefinition($id)->addTag($this->hotPathTagName);
                }
            }
        }

        $extractingDispatcher = new ExtractingEventDispatcher();

        foreach ($container->findTaggedServiceIds($this->subscriberTag, true) as $id => $attributes) {
            $def = $container->getDefinition($id);

            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            $class = $def->getClass();

            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            if (!$r->isSubclassOf(EventSubscriberInterface::class)) {
                throw new InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, EventSubscriberInterface::class));
            }
            $class = $r->name;

            ExtractingEventDispatcher::$aliases = $aliases;
            ExtractingEventDispatcher::$subscriber = $class;
            $extractingDispatcher->addSubscriber($extractingDispatcher);
            foreach ($extractingDispatcher->listeners as $args) {
                if (isset($frozenEvents[$args[0]])) {
                    $frozenEvents[$args[0]][$args[2]][] = [new Reference($id), $args[1]];
                } else {
                    $args[1] = [new ServiceClosureArgument(new Reference($id)), $args[1]];
                    $definition->addMethodCall('addListener', $args);
                }

                if (isset($this->hotPathEvents[$args[0]])) {
                    $container->getDefinition($id)->addTag($this->hotPathTagName);
                }
            }
            $extractingDispatcher->listeners = [];
            ExtractingEventDispatcher::$aliases = [];
        }

        foreach ($frozenEvents as $event => $listeners) {
            krsort($listeners, SORT_NUMERIC);
            $providerId = sprintf('__%s.listener_provider.%s', $this->dispatcherService, $event);
            $container->register($providerId, SimpleListenerProvider::class)
                ->setArguments([array_merge(...array_values($listeners))]);

            $definition->addMethodCall('setListenerProvider', [$event, new Definition(LazyListenerProvider::class, [new ServiceClosureArgument(new Reference($providerId))])]);
        }
    }

    private function getEventFromTypeDeclaration(ContainerBuilder $container, string $id, string $method): string
    {
        if (
            null === ($class = $container->getDefinition($id)->getClass())
            || !($r = $container->getReflectionClass($class, false))
            || !$r->hasMethod($method)
            || 1 > ($m = $r->getMethod($method))->getNumberOfParameters()
            || !($type = $m->getParameters()[0]->getType())
            || $type->isBuiltin()
            || Event::class === ($name = $type->getName())
            || LegacyEvent::class === $name
        ) {
            throw new InvalidArgumentException(sprintf('Service "%s" must define the "event" attribute on "%s" tags.', $id, $this->listenerTag));
        }

        return $name;
    }
}

/**
 * @internal
 */
class ExtractingEventDispatcher extends EventDispatcher implements EventSubscriberInterface
{
    public $listeners = [];

    public static $aliases = [];
    public static $subscriber;

    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        $this->listeners[] = [$eventName, $listener[1], $priority];
    }

    public static function getSubscribedEvents(): array
    {
        $events = [];

        foreach ([self::$subscriber, 'getSubscribedEvents']() as $eventName => $params) {
            $events[self::$aliases[$eventName] ?? $eventName] = $params;
        }

        return $events;
    }
}
