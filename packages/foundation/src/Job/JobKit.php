<?php

namespace Cavatappi\Foundation\Job;

use Cavatappi\Foundation\Value\ValueKit;

/**
 * A Job represents a task that should be performed asynchronously.
 *
 * A job can be routed to any method on any service. Realistically, it should be a service in a dependency injection
 * container. Additional properties will be passed as parameters; override `getParameters` to change this.
 *
 * Extend this class and add any needed information. Jobs will likely be serialized to facilitate cross-thread or
 * cross-server communication.
 */
trait JobKit {
	use ValueKit;

	/**
	 * Service to instantiate.
	 *
	 * @var class-string $service
	 */
	public readonly string $service;

	/**
	 * Method on $service to call.
	 *
	 * @var string
	 */
	public readonly string $method;

	/**
	 * Get the parameters to be passed to $service->$method.
	 *
	 * Default implementation is any properties on the object excluding $service and $method.
	 *
	 * @return array
	 */
	public function getParameters(): array {
		$base = \get_object_vars($this);
		unset($base['service'], $base['method']);
		return $base;
	}
}
