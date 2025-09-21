<?php

namespace Cavatappi\Foundation\Reflection;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Test\TestCase;

final class ValuePropertyTest extends TestCase {
	public function testItWillInferADisplayNameIfNoneIsGiven() {
		$nameless = new ValueProperty(
			name: 'testValue',
			type: 'string',
		);
		$named = new ValueProperty(
			name: 'somethingSomethingId',
			type: 'string',
			displayName: 'Easy',
		);
		$errored = new ValueProperty(
			name: '',
			type: 'string',
		);

		$this->assertEquals('Test Value', $nameless->displayName);
		$this->assertEquals('Easy', $named->displayName);
		$this->assertEquals('', $errored->displayName);
	}

	public function testItRequiresAnItemsPropertyForArrays() {
		$this->expectException(InvalidValueProperties::class);

		new ValueProperty(
			name: 'testValue',
			type: 'list',
		);
	}
}
