<?php

namespace Cavatappi\Infrastructure\Serialization;

use Cavatappi\Foundation\Reflection\TypeRegistry;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;

/**
 * Registry of TypeRegistry services.
 */
class TypeRegistryRegistry implements Registry, Service {
	/**
	 * Store the available TypeRegistries.
	 *
	 * @var class-string<TypeRegistry>[]
	 */
	private array $library = [];

	/**
	 * This registry registers TypeRegistry services.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return TypeRegistry::class;
	}

	/**
	 * Construct the service.
	 *
	 * @param ContainerInterface $container Dependency Injection container.
	 */
	public function __construct(private ContainerInterface $container) {
	}

	/**
	 * Load the names of available TypeRegistry classes.
	 *
	 * @param array<class-string<TypeRegistry>> $configuration List of available TypeRegistry classes.
	 * @return void
	 */
	public function configure(array $configuration): void {
		$this->library = $configuration;
	}

	/**
	 * Compile the available TypeRegistry classes into a Serde-compatible TypeMap.
	 *
	 * @return array<class-string, TypeRegistry>
	 */
	public function getSerdeTypeMap(): array {
		$maps = [];
		foreach ($this->library as $className) {
			$supertype = $className::getTypeToRegister();
			$maps[$supertype] = $this->container->get($className);
		}

		return $maps;
	}
}
