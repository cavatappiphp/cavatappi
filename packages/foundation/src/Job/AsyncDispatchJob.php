<?php

namespace Cavatappi\Foundation\Job;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\Value\ValueKit;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * A job that dispatches a DomainEvent.
 */
class AsyncDispatchJob implements Job {
	use ValueKit;

	/**
	 * Service to instantiate.
	 *
	 * @var class-string $service
	 */
	public string $service { get => EventDispatcherInterface::class; }

	/**
	 * Method on $service to call.
	 *
	 * @var string
	 */
	public string $method { get => 'dispatch'; }

	/**
	 * Create a job that dispatches an event.
	 *
	 * @param DomainEvent $event Event to dispatch.
	 */
	public function __construct(public readonly DomainEvent $event) {
	}

	/**
	 * Get the parameters to be passed to $service->$method.
	 *
	 * @return array
	 */
	public function getParameters(): array {
		return ['event' => $this->event];
	}
}
