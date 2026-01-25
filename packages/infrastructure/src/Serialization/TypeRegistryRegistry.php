<?php

namespace Cavatappi\Infrastructure\Serialization;

use Cavatappi\Foundation\Reflection\TypeRegistry;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;

class TypeRegistryRegistry implements Registry, Service {
	/**
	 * Store the available TypeRegistries.
	 *
	 * @var class-string<TypeRegistry>[]
	 */
	private array $library = [];

	public static function getInterfaceToRegister(): string {
		return TypeRegistry::class;
	}

	public function __construct(private ContainerInterface $container) {
	}

	public function configure(array $configuration): void {
		$this->library = $configuration;
	}

	public function getSerdeTypeMap(): array {
		$maps = [];
		foreach ($this->library as $className) {
			$supertype = $className::getTypeToRegister();
			$maps[$supertype] = $this->container->get($className);
		}

		return $maps;
	}
}
