<?php

namespace Cavatappi\Infrastructure;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Registry\RegistryUtils;
use Cavatappi\Infrastructure\Registries\ServiceRegistry;

/**
 * Useful functions for building an App from Modules.
 */
trait AppKit {
	/**
	 * @param class-string<Module>[] $modules
	 * @return array
	 */
	private function buildDiscoveredClassList(array $modules = []): array {
		return \array_reduce(
			\array_map(
				fn($module) => $module::discoverableClasses(),
				$modules
			),
			fn($carry, $item) => \array_merge($carry, $item),
			[]
		);
	}

	/**
	 * Build a ServiceRegistry with the Default Module and any other supplied DomainModels. Registers services and
	 * provides Registry configurations.
	 *
	 * @param class-string<Module>[] $modules Class names of additional DomainModels to load.
	 * @return ServiceRegistry
	 */
	private function buildContainerFromModels(array $modules = []): ServiceRegistry {
		$classes = $this->buildDiscoveredClassList($modules);
		$map = $this->buildDependencyMap($modules);

		return new ServiceRegistry(
			configuration: $map,
			supplements: $this->buildSupplementsForRegistries($classes),
		);
	}

	/**
	 * Build the dependency map for the given DomainModels.
	 *
	 * @param class-string<Module>[] $modules DomainModel class names.
	 * @return array
	 */
	private function buildDependencyMap(array $modules): array {
		return \array_reduce(
			\array_map(
				fn($module) => $module::serviceDependencyMap(),
				$modules
			),
			fn($carry, $item) => \array_merge($carry, $item),
			[]
		);
	}

	/**
	 * Translates the configs from RegistryHelper into the format needed by ServiceRegistry.
	 *
	 * @param array $classes List of classes to check.
	 * @return array Supplements array for ServiceRegistry
	 */
	private function buildSupplementsForRegistries(array $classes): array {
		return \array_map(
			fn($conf) => ['configure' => ['configuration' => $conf]],
			RegistryUtils::makeRegistryConfigs($classes),
		);
	}
}
