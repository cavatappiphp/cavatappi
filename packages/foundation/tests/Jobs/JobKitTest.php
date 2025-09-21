<?php

namespace Cavatappi\Foundation\Job;

use Cavatappi\Foundation\Job\JobKit;
use Cavatappi\Test\TestCase;

readonly class ExampleJob implements Job {
	use JobKit;

	public function __construct(string $service, string $method, public string $prop) {
		$this->service = $service;
		$this->method = $method;
	}
}

final class JobKitTest extends TestCase {
	public function testItCanBeInstantiated() {
		$actual = new ExampleJob(self::class, 'go', 'test');

		$this->assertInstanceOf(Job::class, $actual);
		$this->assertEquals(['prop' => 'test'], $actual->getParameters());
	}
}
