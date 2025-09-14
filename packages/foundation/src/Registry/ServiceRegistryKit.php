<?php

namespace Cavatappi\Foundation\Registry;

use Cavatappi\Foundation\Exceptions\ServiceNotRegistered;
use Psr\Container\ContainerInterface;

trait ServiceRegistryKit {
	use RegistryKit;

	/**
	 * Dependency injection container to retrieve the objects from.
	 *
	 * @var ContainerInterface
	 */
	private ContainerInterface $container;

	/**
	 * Check if this Registry has a class registered to the given key.
	 *
	 * @param  string $key Key for class to check for.
	 * @return boolean false if $this->get will return null.
	 */
	public function has(string $key): bool {
		return \array_key_exists($key, $this->library) && $this->container->has($this->library[$key]);
	}

	/**
	 * Get an instance of the class indicated by the given key.
	 *
	 * Will throw a ServiceNotRegistered exception if the key does not exist; check with has($key) to avoid this.
	 *
	 * @throws ServiceNotRegistered When no service is registered with the given key.
	 *
	 * @param  string $key Key for class to instantiate and get.
	 * @return mixed Instance of the requested class.
	 */
	public function getService(string $key): mixed {
		if (!$this->has($key)) {
			throw new ServiceNotRegistered(service: $key, registry: static::class);
		}
		return $this->container->get($this->library[$key]);
	}
}
