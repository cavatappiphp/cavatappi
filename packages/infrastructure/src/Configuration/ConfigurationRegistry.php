<?php

namespace Cavatappi\Infrastructure\Configuration;

use Cavatappi\Foundation\Exceptions\ServiceNotRegistered;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Service;
use Crell\EnvMapper\EnvMapper;

class ConfigurationRegistry implements Service, Registry {
	public static function getInterfaceToRegister(): string {
		return Configuration::class;
	}

	private array $availableClasses;
	private array $library = [];

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
