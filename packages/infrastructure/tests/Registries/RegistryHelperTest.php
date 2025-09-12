<?php

namespace Cavatappi\Infrastructure\Registries;

use Psr\Log\LoggerAwareInterface;
use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Service\Command\CommandHandlerService;
use Cavatappi\Foundation\Service\Event\EventListenerService;
use Cavatappi\Foundation\Service\Registry\Registry;
use Cavatappi\Test\TestCase;

abstract class TestServiceCommandLogger implements CommandHandlerService, LoggerAwareInterface {}
abstract class TestServiceCommand implements CommandHandlerService {}
abstract class TestServiceCommandEventLogger implements CommandHandlerService, EventListenerService, LoggerAwareInterface {}
abstract class TestServiceEvent implements EventListenerService {}
abstract class TestServiceNone implements Service {}

interface ExtendedRegistry extends Registry {}

abstract class TestCommandRegistry implements Registry {
	public static function getInterfaceToRegister(): string { return CommandHandlerService::class; }
}
abstract class TestEventRegistry implements ExtendedRegistry {
	public static function getInterfaceToRegister(): string { return EventListenerService::class; }
}

final class RegistryHelperTest extends TestCase {
	public function testItTakesAListOfClassesAndGivesRegistryConfigurations() {
		$services = [
			TestServiceCommandLogger::class,
			TestServiceCommand::class,
			TestServiceCommandEventLogger::class,
			CommandHandlerService::class,
			__NAMESPACE__ . '\\NonexistantClass',
			TestServiceEvent::class,
			TestServiceNone::class,
			EventListenerService::class,
			TestCommandRegistry::class,
			TestEventRegistry::class,
		];

		$expected = [
			TestCommandRegistry::class => [
				TestServiceCommandLogger::class,
				TestServiceCommand::class,
				TestServiceCommandEventLogger::class,
			],
			TestEventRegistry::class => [
				TestServiceCommandEventLogger::class,
				TestServiceEvent::class,
			],
		];

		$this->assertEquals($expected, RegistryHelper::getRegistryConfigs($services));
	}

	public function testItReturnsAnEmptyArrayWhenGivenAnEmptyArray() {
		$this->assertEquals([], RegistryHelper::getRegistryConfigs([]));
	}
}
