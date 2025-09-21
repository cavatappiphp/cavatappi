<?php

namespace Cavatappi\Foundation\Registry;

/**
 * Functions for adding classes to Registries.
 */
class RegistryUtils {
	/**
	 * Get the configuration for each Registry in the array.
	 *
	 * @param  array<class-string, class-string[]> $discoveredClasses List of available classes and their interfaces.
	 * @return array<class-string, class-string[]> List of Registries and their registered classes.
	 */
	public static function makeRegistryConfigs(array $discoveredClasses): array {
		$registryList = \array_keys(\array_filter($discoveredClasses, fn($imp) => \in_array(Registry::class, $imp)));
		return \array_combine(
			$registryList,
			\array_map(
				fn($reg) => self::getImplementingClassesForRegistry($discoveredClasses, $reg),
				$registryList,
			)
		);
	}

	/**
	 * Filter the map of implemented interfaces for the given service.
	 *
	 * @param  array<class-string, class-string[]> $map      Map of classes and the interfaces they implement.
	 * @param  class-string                        $registry Registry being filtered for.
	 * @return class-string[] Classes to be registerd by $registry.
	 */
	public static function getImplementingClassesForRegistry(array $map, string $registry): array {
		$search = $registry::getInterfaceToRegister();
		$filtered = \array_filter(
			$map,
			fn($imp) => \in_array($search, $imp, strict: true)
		);

		return \array_keys($filtered);
	}
}
