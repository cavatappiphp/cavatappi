<?php

namespace Cavatappi\Foundation\Test\DiscoveryTestFixture;

use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Service\Command\CommandBus;

final class SameDirectoryService implements Service {
	public function __construct(private CommandBus $commandBus)
	{

	}
}
