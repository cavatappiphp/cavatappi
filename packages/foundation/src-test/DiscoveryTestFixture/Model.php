<?php

namespace Cavatappi\Foundation\Test\DiscoveryTestFixture;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;

final class Model implements Module {
	use ModuleKit;
	use FileDiscoveryKit;

	private static function serviceMapOverrides(): array {
		return [
			Others\ServiceClassThatCannotBeAutoRegistered::class => [
				'one' => SomeFolder\SomeInterface::class,
				'two' => SomeFolder\SomeAbstractServiceInterfaceClass::class,
				'three' => SameDirectoryService::class,
				'dsn' => 'sqlite:///db.sqlite',
			]
		];
	}

	public static function listClassesPublic(): array {
		return self::listClasses();
	}

	public const FOLDER = __DIR__;

	public const EXPECTED_CLASSES = [
		self::class,
		Others\NonServiceClass::class,
		Others\ServiceClassThatCannotBeAutoRegistered::class,
		Others\ServiceClassWithDependencies::class,
		SameDirectoryService::class,
		SomeFolder\SomeClass::class,
		SomeFolder\SomeConcreteServiceInterfaceClass::class,
		SomeFolder\SomeInterfaceClass::class,
		SomeFolder\SomeServiceClass::class,
		SomeFolder\SomeServiceInterfaceClass::class,
	];
}
