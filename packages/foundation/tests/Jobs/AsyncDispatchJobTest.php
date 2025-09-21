<?php

namespace Cavatappi\Foundation\Job;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\Job\AsyncDispatchJob;
use Cavatappi\Foundation\Job\Job;
use Psr\EventDispatcher\EventDispatcherInterface;
use Cavatappi\Test\TestCase;

final class AsyncDispatchJobTest extends TestCase {
	public function testItCreatesTheJobWithTheCorrectParameters() {
		$event = $this->createStub(DomainEvent::class);
		$actual = new AsyncDispatchJob($event);
		$this->assertInstanceOf(Job::class, $actual);
		$this->assertEquals(EventDispatcherInterface::class, $actual->service);
		$this->assertEquals('dispatch', $actual->method);
		$this->assertEquals(['event' => $event], $actual->getParameters());
	}
}
