<?php

namespace Cavatappi\Foundation\Job;

use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\CommandBus;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * A job that executes a Command.
 */
class AsyncExecutionJob implements Job {
	use ValueKit;

	/**
	 * Service to instantiate.
	 *
	 * @var class-string $service
	 */
	public string $service { get => CommandBus::class; }

	/**
	 * Method on $service to call.
	 *
	 * @var string
	 */
	public string $method { get => 'execute'; }

	/**
	 * Create a job that executes a command.
	 *
	 * @param Command $command Event to dispatch.
	 */
	public function __construct(public readonly Command $command) {
	}

	/**
	 * Get the parameters to be passed to $service->$method.
	 *
	 * @return array
	 */
	public function getParameters(): array {
		return ['command' => $this->command];
	}
}
