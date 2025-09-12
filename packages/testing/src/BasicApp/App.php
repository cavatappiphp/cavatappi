<?php

namespace Cavatappi\Test\BasicApp;

use Cavatappi\Foundation\Service\Command\CommandBus;
use Cavatappi\Foundation\Value\Messages\Command;
use Cavatappi\Foundation\Value\Messages\DomainEvent;
use Cavatappi\Infrastructure\AppKit;
use Cavatappi\Infrastructure\Registries\ServiceRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;

class App {
	use AppKit;

	public readonly ServiceRegistry $container;

	public function __construct(array $models, array $services) {
		$map = [
			...$this->buildDependencyMap([Model::class, ...$models]),
			...$services,
		];

		$this->container = new ServiceRegistry(
			configuration: $map,
			supplements: $this->buildSupplementsForRegistries(\array_keys($map)),
		);
	}

	public function execute(Command $command): mixed {
		// Serialize and deserialize the Command to ensure that it will successfully translate.
		// Future systems may send Commands to other services.
		$serializedCommand = $command->serializeValue();
		$retval = $this->container->get(CommandBus::class)->execute(Command::deserializeValue($serializedCommand));
		$this->container->get(TestJobManager::class)->run();
		return $retval;
	}

	public function dispatch(mixed $event): mixed {
		$processedEvent = $event;
		if (\is_a($event, DomainEvent::class)) {
			// Serialize and deserialize the DomainEvent to ensure that it will successfully translate.
			// Future systems may send DomainEvents to other services.
			$processedEvent = DomainEvent::deserializeValue($event->serializeValue());
		}

		$retval = $this->container->get(EventDispatcherInterface::class)->dispatch($processedEvent);
		$this->container->get(TestJobManager::class)->run();
		return $retval;
	}
}
