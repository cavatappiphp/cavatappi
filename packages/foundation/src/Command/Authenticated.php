<?php

namespace Cavatappi\Foundation\Command;

use Ramsey\Uuid\UuidInterface;

/**
 * Indicates that this Command is being performed by a given user.
 */
interface Authenticated {
	/**
	 * ID of the user issuing this command so that authorization can be checked.
	 *
	 * It is assumed that this user has been successfully authenticated.
	 *
	 * @var UuidInterface
	 */
	public UuidInterface $userId { get; }
}
