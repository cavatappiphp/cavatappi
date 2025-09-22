<?php

namespace Cavatappi\Foundation\Module;

use Cavatappi\Foundation\Test\DiscoveryTestFixture\Model;
use Cavatappi\Test\TestCase;

final class FileDiscoveryKitTest extends TestCase {
	public function testItWillDiscoverClassesInItsOwnFolder() {
		$expected = Model::EXPECTED_CLASSES;
		$this->assertEquals($expected, Model::listClassesPublic());
	}
}