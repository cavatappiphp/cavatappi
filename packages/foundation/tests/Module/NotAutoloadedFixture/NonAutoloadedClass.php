<?php

namespace Cavatappi\Foundation\Test\NotAutoloadedFixture;

use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Test\DiscoveryTestFixture\SameDirectoryService;
use Cavatappi\Foundation\Test\DiscoveryTestFixture\SomeFolder\{SomeAbstractServiceInterfaceClass, SomeInterface};

final class NonAutoloadedClass implements Service{
	public function __construct(
		private SomeInterface $one,
		private SomeAbstractServiceInterfaceClass $two,
		private SameDirectoryService $three) {
	}
}
