<?php

namespace Cavatappi\Foundation\Value\Jobs;

use Cavatappi\Foundation\Value\Messages\DomainEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * A job that dispatches a DomainEvent.
 */
readonly class AsyncDispatchJob extends Job {
	/**
	 * Create a job that dispatches an event.
	 *
	 * @param DomainEvent $event Event to dispatch.
	 */
	public function __construct(public DomainEvent $event) {
		parent::__construct(
			service: EventDispatcherInterface::class,
			method: 'dispatch',
		);
	}
}
