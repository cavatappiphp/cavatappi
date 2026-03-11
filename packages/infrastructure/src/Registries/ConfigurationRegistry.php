<?php

namespace Cavatappi\Infrastructure\Registries;

use Cavatappi\Foundation\Exceptions\ServiceNotRegistered;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Value\Configuration;
use Crell\EnvMapper\EnvMapper;

/**
 * A central registry for holding Configuration objects.
 * 
 * This service can be configured with an optional EnvMapper for creating config objects from environment variables.
 * No matter what, objects loaded with `loadConfig` take priority. If an object is requested that hasn't been loaded
 * and an EnvMapper has been set, the registry will attempt to create the object from environment variables.
 */
class ConfigurationRegistry implements Service, Registry {
	public static function getInterfaceToRegister(): string {
		return Configuration::class;
	}

	private array $availableClasses;

	/**
	 * Instantiated configuration objects.
	 *
	 * @var Configuration[]
	 */
	private array $library = [];

	/**
	 * Create the service.
	 *
	 * @param EnvMapper|null $mapper Optional EnvMapper.
	 */
	public function __construct(private ?EnvMapper $mapper = null) {}

	public function configure(array $configuration): void {
		$this->availableClasses = $configuration;
	}

	public function loadConfig(Configuration $newConfig): void {
		$this->library[get_class($newConfig)] = $newConfig;
	}

	public function has(string $class): bool {
		if (isset($this->mapper)) {
			return in_array($class, $this->availableClasses);
		}

		return isset($this->library[$class]);
	}

	/**
	 * Get a configuration object from the registry.
	 *
	 * @template T
	 * @param class-string<T> $class
	 * @return T
	 */
	public function get(string $class): Configuration {
		if (isset($this->mapper)) {
			$this->library[$class] ??= $this->mapper->map($class);
		}

		if (!isset($this->library[$class])) {
			throw new ServiceNotRegistered($class, registry: self::class);
		}

		return $this->library[$class];
	}
}
