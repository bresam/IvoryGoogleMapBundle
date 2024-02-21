<?php

/*
 * This file is part of the Ivory Google Map bundle package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterHelperListenerPass implements CompilerPassInterface
{
    /** @var string[] */
    private static array $helpers = [
        'api',
        'map',
        'map.static',
        'place_autocomplete',
    ];

    /** {@inheritdoc}
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        foreach (self::$helpers as $helper) {
            if (!$container->hasDefinition('ivory.google_map.helper.'.$helper.'.event_dispatcher')) {
                return;
            }

            $definition = $container->findDefinition('ivory.google_map.helper.'.$helper.'.event_dispatcher');

            foreach ($container->findTaggedServiceIds('ivory.google_map.helper.'.$helper.'.listener', true) as $id => $events) {
                foreach ($events as $event) {
                    $priority = $event['priority'] ?? 0;

                    if (!isset($event['event'])) {
                        if ($container->getDefinition($id)->hasTag('ivory.google_map.helper.'.$helper.'.subscriber')) {
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

                    $definition->addMethodCall('addListener', [$event['event'], [new ServiceClosureArgument(new Reference($id)), $event['method']], $priority]);
                }
            }

            $extractingDispatcher = new ExtractingEventDispatcher();

            foreach ($container->findTaggedServiceIds('ivory.google_map.helper.'.$helper.'.subscriber', true) as $id => $attributes) {
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

                ExtractingEventDispatcher::$aliases = [];
                ExtractingEventDispatcher::$subscriber = $class;
                $extractingDispatcher->addSubscriber($extractingDispatcher);
                foreach ($extractingDispatcher->listeners as $args) {
                    $args[1] = [new ServiceClosureArgument(new Reference($id)), $args[1]];
                    $definition->addMethodCall('addListener', $args);
                }
                $extractingDispatcher->listeners = [];
                ExtractingEventDispatcher::$aliases = [];
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function getEventFromTypeDeclaration(ContainerBuilder $container, string $id, string $method): string
    {
        if (
            null === ($class = $container->getDefinition($id)->getClass())
            || !($r = $container->getReflectionClass($class, false))
            || !$r->hasMethod($method)
            || 1 > ($m = $r->getMethod($method))->getNumberOfParameters()
            || !($type = $m->getParameters()[0]->getType()) instanceof \ReflectionNamedType
            || $type->isBuiltin()
            || Event::class === ($name = $type->getName())
        ) {
            throw new InvalidArgumentException(sprintf('Service "%s" must define the "event" attribute on "%s" tags.', $id, 'ivory.google_map.helper.???.listener'));
        }

        return $name;
    }
}

/**
 * @internal
 */
class ExtractingEventDispatcher extends EventDispatcher implements EventSubscriberInterface
{
    public array $listeners = [];

    public static array $aliases = [];
    public static $subscriber;

    public function addListener(string $eventName, callable|array $listener, int $priority = 0): void
    {
        $this->listeners[] = [$eventName, $listener[1], $priority];
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        $events = [];

        foreach ([self::$subscriber, 'getSubscribedEvents']() as $eventName => $params) {
            $events[self::$aliases[$eventName] ?? $eventName] = $params;
        }

        return $events;
    }
}
