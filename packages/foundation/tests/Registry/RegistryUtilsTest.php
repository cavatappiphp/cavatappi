<?php

namespace Cavatappi\Foundation\Registry;

use Psr\Log\LoggerAwareInterface;
use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Foundation\Module\ModuleKit;
use Cavatappi\Foundation\Registry\Registry;
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

final class RegistryUtilsTest extends TestCase {
	use ModuleKit;

	private static function listClasses(): array {
		return [
			TestServiceCommandLogger::class,
			TestServiceCommand::class,
			TestServiceCommandEventLogger::class,
			CommandHandlerService::class,
			TestServiceEvent::class,
			TestServiceNone::class,
			EventListenerService::class,
			TestCommandRegistry::class,
			TestEventRegistry::class,
		];
	}

	private static function serviceMapOverrides(): array { return []; }

	public function testItTakesAListOfClassesAndGivesRegistryConfigurations() {
		$services = self::discoverableClasses();

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

		$this->assertEquals($expected, RegistryUtils::makeRegistryConfigs($services));
	}

	public function testItReturnsAnEmptyArrayWhenGivenAnEmptyArray() {
		$this->assertEquals([], RegistryUtils::makeRegistryConfigs([]));
	}
}
