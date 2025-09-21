<?php

namespace Cavatappi\Foundation\Job;

use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\CommandBus;
use Cavatappi\Foundation\Job\AsyncExecutionJob;
use Cavatappi\Foundation\Job\Job;
use Cavatappi\Test\TestCase;

final class AsyncExecutionJobTest extends TestCase {
	public function testItCreatesTheJobWithTheCorrectParameters() {
		$command = $this->createStub(Command::class);
		$actual = new AsyncExecutionJob($command);
		$this->assertInstanceOf(Job::class, $actual);
		$this->assertEquals(CommandBus::class, $actual->service);
		$this->assertEquals('execute', $actual->method);
		$this->assertEquals(['command' => $command], $actual->getParameters());
	}
}
