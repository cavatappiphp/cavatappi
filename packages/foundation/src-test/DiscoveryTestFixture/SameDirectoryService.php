<?php

namespace Cavatappi\Foundation\Test\DiscoveryTestFixture;

use Cavatappi\Foundation\Command\CommandBus;
use Cavatappi\Foundation\Service;

final class SameDirectoryService implements Service {
	public function __construct(private CommandBus $commandBus)
	{

	}
}
