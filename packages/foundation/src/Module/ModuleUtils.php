<?php

namespace Cavatappi\Foundation\Module;

use Cavatappi\Foundation\Exceptions\CodePathNotSupported;
use League\ConstructFinder\ConstructFinder;
use ReflectionClass;
use ReflectionNamedType;

/**
 * Useful functions for Modules that are not class-specific.
 */
final class ModuleUtils {
	/**
	 * Get fully-qualified class names from files in the given folders.
	 *
	 * **This will not load the classes.** They must be loaded by other code or configured to correctly autoload.
	 *
	 * @see https://github.com/thephpleague/construct-finder
	 *
	 * @param  string $folder                Folder to search. Pass __DIR__ to search the current file's folder.
	 * @param  array  $excludingFilePatterns Array of patterns to exclude (e.g. *.view.php).
	 * @return class-string[]
	 */
	public static function getClassNamesFromFolder(
		string $folder,
		array $excludingFilePatterns = [],
	): array {
		$foundClasses = ConstructFinder::locatedIn($folder)->exclude(...$excludingFilePatterns)->findClassNames();
		return \array_values(\array_filter(
			$foundClasses,
			static function ($found) {
				// If we already know it doesn't exist, filter out.
				if (!\class_exists($found)) {
					return false;
				}

				$reflection = new ReflectionClass($found);
				// If it's abstract, filter out.
				if ($reflection->isAbstract()) {
					return false;
				}

				return true;
			}
		));
	}

	/**
	 * Map the given classes to the interfaces they implement.
	 *
	 * @param  class-string[] $classNames Classes to parse.
	 * @return array<class-string, class-string[]>
	 */
	public static function analyzeClasses(array $classNames): array {
		$map = [];
		foreach ($classNames as $className) {
			$implements = \class_implements($className);
			$map[$className] = \array_values($implements ?: []);
		}
		return $map;
	}

	/**
	 * Reflect the given service and return its dependency array.
	 *
	 * @throws CodePathNotSupported When service's constructor takes untyped or union/intersection typed arguments.
	 *
	 * @param  class-string $service Service to reflect.
	 * @return array<string, mixed>
	 */
	public static function reflectService(string $service): array {
		$reflect = (new ReflectionClass($service))->getConstructor();
		if (!isset($reflect)) {
			return [];
		}

		$params = [];
		foreach ($reflect->getParameters() as $param) {
			$type = $param->getType();
			if (!isset($type) || \get_class($type) !== ReflectionNamedType::class || $type->isBuiltin()) {
				throw new CodePathNotSupported(
					"Dependencies for {$service} cannot be generated; parameter {$param->getName()} is not a class.",
				);
			}

			$params[$param->getName()] = $type->getName();
		}

		return $params;
	}
}
