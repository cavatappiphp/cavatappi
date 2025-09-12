<?php

namespace Cavatappi\Foundation\Test\DiscoveryTestFixture\Others;

use Cavatappi\Foundation\Test\DiscoveryTestFixture\SameDirectoryService;
use Cavatappi\Foundation\Test\DiscoveryTestFixture\SomeFolder\{SomeAbstractServiceInterfaceClass, SomeInterface};

final class NonServiceClass{
	public function __construct(
		private SomeInterface $one,
		private SomeAbstractServiceInterfaceClass $two,
		private SameDirectoryService $three) {
	}
}
