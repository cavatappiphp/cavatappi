<?php

namespace Cavatappi\Foundation\Validation;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Test\TestCase;

#[ExactlyOneOf('one', 'two', 'three')]
final readonly class ValidatedKitTestOneOf implements Validated {
	use ValidatedKit;
	public function __construct(
		public ?string $one = null,
		public ?string $two = null,
		public ?string $three = null,
	) {
		$this->validate();
	}
}

#[AtLeastOneOf('one', 'two', 'three')]
final readonly class ValidatedKitTestAtLeast implements Validated {
	use ValidatedKit;
	public function __construct(
		public ?string $one = null,
		public ?string $two = null,
		public ?string $three = null,
	) {
		$this->validate();
	}
}

final class ValidatedKitTest extends TestCase {
	public function testOnlyOneOfSucceedsWithOneValue() {
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestOneOf(one: 'one'));
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestOneOf(two: 'two'));
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestOneOf(three: 'three'));
	}

	public function testOnlyOneOfFailsIfAllAreNull() {
		$this->expectException(InvalidValueProperties::class);
		new ValidatedKitTestOneOf();
	}

	public function testOnlyOneOfFailsWithMoreThanOneValue() {
		$this->expectException(InvalidValueProperties::class);
		new ValidatedKitTestOneOf(one: 'one', three: 'three');
	}

	public function testAtLeastOneOfSucceedsWithOneValue() {
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestAtLeast(one: 'one'));
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestAtLeast(two: 'two'));
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestAtLeast(three: 'three'));
	}

	public function testAtLeastOneOfSucceedsWithMoreThanOneValue() {
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestAtLeast(one: 'one', two: 'two'));
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestAtLeast(two: 'two', three: 'three'));
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestAtLeast(three: 'three', one: 'one'));
		$this->assertInstanceOf(Validated::class, new ValidatedKitTestAtLeast(one: 'one', two: 'two', three: 'three'));
	}

	public function testAtLeastOneOfFailsIfAllAreNull() {
		$this->expectException(InvalidValueProperties::class);
		new ValidatedKitTestAtLeast();
	}
}