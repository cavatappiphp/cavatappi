<?php

namespace Cavatappi\Foundation\Value;

use Cavatappi\Foundation\Exceptions\CodePathNotSupported;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Reflection\DisplayName;
use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Reflection\Target;
use Cavatappi\Foundation\Reflection\ValueProperty;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value;
use Cavatappi\Test\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Ramsey\Uuid\UuidInterface;

class ValueKitTestDefault implements Value {
	use ValueKit;
}

final class ValueKitTest extends TestCase {
	public function testItImplementsAllValueInterfaceFunctions() {
		$actual = new ValueKitTestDefault();
		$this->assertInstanceOf(Value::class, $actual);
	}

	#[TestDox('with() creates a new object')]
	public function testWithCreatesNew() {
		$first = new class('world') extends ValueKitTestDefault {
			public function __construct(public string $hello) {}
		};
		$second = $first->with();

		$this->assertInstanceOf(get_class($first), $second);
		$this->assertEquals($second->hello, $first->hello);
		$this->assertNotSame($second, $first);
	}

	#[TestDox('with() will replace the given fields')]
	public function testWithReplacesGiven() {
		$first = new class('one', 'five') extends ValueKitTestDefault {
			public function __construct(public string $one, public string $three) {}
		};
		$second = $first->with(three: 'three');

		$this->assertEquals('one', $first->one);
		$this->assertEquals('one', $second->one);
		$this->assertEquals('five', $first->three);
		$this->assertEquals('three', $second->three);
	}

	#[TestDox('with() will ignore private values')]
	public function testWithIgnoresPrivate() {
		$first = new class('given', 'given') extends ValueKitTestDefault {
			public function __construct(public string $public = 'default', private string $private = 'default') {}
			public function getPrivate() { return $this->private; }
		};
		$second = $first->with();

		$this->assertEquals('given', $first->public);
		$this->assertEquals('given', $second->public);
		$this->assertEquals('given', $first->getPrivate());
		$this->assertEquals('default', $second->getPrivate());
	}

	#[TestDox('with() will ignore virtual properties')]
	public function testWithIgnoresVirtual() {
		$first = new class('given') extends ValueKitTestDefault {
			public function __construct(public string $public = 'default') {}
			public string $publicInCaps { get => strtoupper($this->public); }
		};
		$second = $first->with();

		$this->assertEquals('given', $first->public);
		$this->assertEquals('given', $second->public);
	}

	#[TestDox('with() will throw an exception on error')]
	public function testWithThrowsException() {
		$this->expectException(InvalidValueProperties::class);

		$first = new class('camelot') extends ValueKitTestDefault {
			public function __construct(public string $camelot) {}
		};
		$first->with(itIsOnly: 'a model');
	}

	#[TestDox('with() will validate the new object')]
	public function testWithValidates() {
		$this->expectException(InvalidValueProperties::class);

		$first = new class('camelot') extends ValueKitTestDefault implements Validated {
			public function __construct(public string $camelot) { $this->validate(); }
			public function validate(): void {
				if ($this->camelot !== 'camelot') { throw new InvalidValueProperties(); }
			}
		};
		$first->with(camelot: 'a model');
	}

	#[TestDox('equals() will return true if the objects\' class and values match')]
	public function testEqualsClassAndValuesMatch() {
		$first = new class('camelot') extends ValueKitTestDefault {
			public function __construct(public string $destination) {}
		};
		$second = new (\get_class($first))('camelot');

		$this->assertEquals($first->destination, $second->destination);
		$this->assertTrue($first->equals($second));
	}

	#[TestDox('equals() will return true if values are stringable and string values match')]
	public function testEqualsStringableMatch() {
		$first = new class($this->randomId()) extends ValueKitTestDefault {
			public function __construct(public UuidInterface $id) {}
		};
		$second = new (\get_class($first))(UuidFactory::fromString($first->id->__toString()));
		$third = new (\get_class($first))($this->randomId());

		$this->assertNotEquals($first->id, $second->id);
		$this->assertTrue($first->equals($second));
		$this->assertFalse($first->equals($third));
	}

	#[TestDox('equals() will return false if the objects\' values do not match')]
	public function testEqualsValueMismatch() {
		$first = new class('camelot') extends ValueKitTestDefault {
			public function __construct(public string $destination) {}
		};
		$second = new (\get_class($first))('a model');

		$this->assertNotEquals($first->destination, $second->destination);
		$this->assertFalse($first->equals($second));
	}

	#[TestDox('equals() will return false if the objects\' classes do not match')]
	public function testEqualsClassMismatch() {
		$first = new class('camelot') extends ValueKitTestDefault {
			public function __construct(public string $destination) {}
		};
		$second = new class('camelot') extends ValueKitTestDefault {
			public function __construct(public string $destination) {}
		};

		$this->assertEquals($first->destination, $second->destination);
		$this->assertFalse($first->equals($second));
	}

	#[TestDox('Default getPropertyInfo() does not work with union types')]
	public function testPropertyUnionType() {
		$this->expectException(CodePathNotSupported::class);

		$class = new class(543) extends ValueKitTestDefault {
			public function __construct(public string|int $thing) {}
		};

		get_class($class)::reflect();
	}

	#[TestDox('Default getPropertyInfo() requires typed arrays')]
	public function testPropertyNoArrayType() {
		$this->expectException(CodePathNotSupported::class);

		$class = new class([543]) extends ValueKitTestDefault {
			public function __construct(public array $thing) {}
		};

		get_class($class)::reflect();
	}

	#[TestDox('reflect() will generate an appropriate array of ValueProperty objects.')]
	public function testReflection() {
		$class = new class(
			stringVal: 'one',
			intVal: 2,
			id: $this->randomId(),
			stringList: [],
			stringMap: [],
			idList: [],
			idMap: [],
		) extends ValueKitTestDefault {
			public string $virtualPropertyThatShouldNotBeReflected { get => $this->stringVal; }

			public function __construct(
				public readonly string $stringVal,
				#[DisplayName('Something')] public readonly int $intVal,
				#[Target('\\OtherLibrary\\Entity')] public readonly UuidInterface $id,
				#[ListType('string')] public readonly array $stringList,
				#[MapType('string')] public readonly array $stringMap,
				#[ListType(UuidInterface::class), Target('\\OtherLibrary\\Entity')] public readonly array $idList,
				#[MapType(UuidInterface::class), Target('\\OtherLibrary\\Entity'), DisplayName('Something Else')] public readonly array $idMap,
			) {}
		};

		$expected = [
			'stringVal' => new ValueProperty(
				name: 'stringVal',
				type: 'string',
				displayName: 'String Val',
			),
			'intVal' => new ValueProperty(
				name: 'intVal',
				type: 'int',
				displayName: 'Something',
			),
			'id' => new ValueProperty(
				name: 'id',
				type: UuidInterface::class,
				displayName: 'Id',
				target: '\\OtherLibrary\\Entity',
			),
			'stringList' => new ValueProperty(
				name: 'stringList',
				type: 'list',
				items: 'string',
				displayName: 'String List',
			),
			'stringMap' => new ValueProperty(
				name: 'stringMap',
				type: 'map',
				items: 'string',
				displayName: 'String Map',
			),
			'idList' => new ValueProperty(
				name: 'idList',
				type: 'list',
				items: UuidInterface::class,
				displayName: 'Id List',
				target: '\\OtherLibrary\\Entity',
			),
			'idMap' => new ValueProperty(
				name: 'idMap',
				type: 'map',
				items: UuidInterface::class,
				displayName: 'Something Else',
				target: '\\OtherLibrary\\Entity',
			),
		];

		$actual = get_class($class)::reflect();

		$this->assertEquals($expected, $actual);
		$this->assertArrayNotHasKey('name', $actual);
	}

	public function testReflectionIgnoresVirtualProperties() {
		$object = new class('given') extends ValueKitTestDefault {
			public function __construct(public string $public = 'default') {}
			public string $publicInCaps { get => strtoupper($this->public); }
		};
		$actual = get_class($object)::reflect();

		$this->assertArrayNotHasKey('publicInCaps', $actual);
		$this->assertArrayHasKey('public', $actual);
	}
}