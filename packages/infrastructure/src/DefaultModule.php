<?php

namespace Cavatappi\Infrastructure;

use Cavatappi\Foundation\Command\CommandBus;
use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;
use Cavatappi\Infrastructure\Registries\ServiceRegistry;
use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Declared dependencies for the default infrastructure.
 *
 * You may override a few of these or omit this model entirely and add the services to your application's model.
 */
class DefaultModule implements Module {
	use FileDiscoveryKit;
	use ModuleKit;

	private static function serviceMapOverrides(): array {
		return [
			Registries\ServiceRegistry::class => [], // Don't try to divide by zero or auto-map the container.

			LoggerInterface::class => NullLogger::class,
			ListenerProviderInterface::class => Registries\EventListenerRegistry::class,
			EventDispatcherInterface::class => Dispatcher::class,
			CommandBus::class => Registries\CommandHandlerRegistry::class,

			NullLogger::class => [],
			Dispatcher::class => [
				ListenerProviderInterface::class,
				LoggerInterface::class,
			]
		];
	}
}
