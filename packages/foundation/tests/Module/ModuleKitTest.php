<?php

namespace Cavatappi\Foundation\Module;

use Cavatappi\Foundation\Command\CommandBus;
use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Test\DiscoveryTestFixture as Fixture;
use Cavatappi\Test\TestCase;

final class ModuleKitTest extends TestCase {
	public function testItWorksWithFileKit() {
		$expectedDisovered = [
			Fixture\Model::class => [Module::class],
			Fixture\Others\NonServiceClass::class => [],
			Fixture\Others\ServiceClassThatCannotBeAutoRegistered::class => [Service::class],
			Fixture\Others\ServiceClassWithDependencies::class => [Service::class],
			Fixture\SameDirectoryService::class => [Service::class],
			Fixture\SomeFolder\SomeClass::class => [],
			Fixture\SomeFolder\SomeConcreteServiceInterfaceClass::class => [Service::class, Fixture\SomeFolder\SomeServiceInterface::class],
			Fixture\SomeFolder\SomeInterfaceClass::class => [Fixture\SomeFolder\SomeInterface::class],
			Fixture\SomeFolder\SomeServiceClass::class => [Service::class],
			Fixture\SomeFolder\SomeServiceInterfaceClass::class => [Service::class, Fixture\SomeFolder\SomeServiceInterface::class],
		];
		$expectedServices = [
			Fixture\SameDirectoryService::class => ['commandBus' => CommandBus::class],
			Fixture\Others\ServiceClassThatCannotBeAutoRegistered::class => [
				'one' => Fixture\SomeFolder\SomeInterface::class,
				'two' => Fixture\SomeFolder\SomeAbstractServiceInterfaceClass::class,
				'three' => Fixture\SameDirectoryService::class,
				'dsn' => 'sqlite:///db.sqlite',
			],
			Fixture\Others\ServiceClassWithDependencies::class => [
				'one' => Fixture\SomeFolder\SomeInterface::class,
				'two' => Fixture\SomeFolder\SomeAbstractServiceInterfaceClass::class,
				'three' => Fixture\SameDirectoryService::class,
			],
			Fixture\SomeFolder\SomeConcreteServiceInterfaceClass::class => [],
			Fixture\SomeFolder\SomeServiceClass::class => [],
			Fixture\SomeFolder\SomeServiceInterfaceClass::class => [],
		];

		$actualDiscovered = Fixture\Model::discoverableClasses();
		// Sort the output so that the test is consistent.
		array_walk($actualDiscovered, fn(&$arr) => sort($arr));

		$this->assertEquals($expectedDisovered, $actualDiscovered);
		$this->assertEquals($expectedServices, Fixture\Model::serviceDependencyMap());
	}
}
