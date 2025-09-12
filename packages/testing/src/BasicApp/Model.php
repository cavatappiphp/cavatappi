<?php

namespace Cavatappi\Test\BasicApp;

use Cavatappi\Foundation\DomainModel;
use Cavatappi\Foundation\Service\Command\CommandBus;
use Cavatappi\Foundation\Service\Job\JobManager;
use Cavatappi\Infrastructure\Registries\CommandHandlerRegistry;
use Cavatappi\Infrastructure\Registries\EventListenerRegistry;
use Cavatappi\Test\BasicApp\TestJobManager;
use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Basic infrastructure used by tests.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		CommandHandlerRegistry::class,
		EventListenerRegistry::class,
		TestJobManager::class,
	];

	public const SERVICES = [
		ListenerProviderInterface::class => EventListenerRegistry::class,
		EventDispatcherInterface::class => Dispatcher::class,
		CommandBus::class => CommandHandlerRegistry::class,
		Dispatcher::class => [ListenerProviderInterface::class],
		JobManager::class => TestJobManager::class,
	];
}
