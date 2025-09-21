<?php

namespace Cavatappi\Foundation\Jobs;

use Cavatappi\Test\TestCase;

final class BasicJobTest extends TestCase {
	public function testItUsesPropsAsMethodParameters() {
		$props = ['one' => 'two', 'three' => 'four'];
		$actual = new BasicJob(service: self::class, method: 'test', props: $props);

		$this->assertInstanceOf(Job::class, $actual);
		$this->assertEquals($props, $actual->getParameters());
	}

	public function testItDeserializesExtraPropertiesToProps() {
		$props = ['one' => 'two', 'three' => 'four'];
		$data = ['service' => self::class, 'method' => 'test', 'one' => 'two', 'three' => 'four'];
		$actual = BasicJob::deserializeValue($data);

		$this->assertEquals($props, $actual->getParameters());
	}
}
