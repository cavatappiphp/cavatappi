<?php

namespace Cavatappi\Foundation\Reflection;

use Cavatappi\Test\TestCase;
use Crell\Serde\ValueType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

final class ArrayTypeUtilsTest extends TestCase {
	public static function knownTypes(): array {
		return [
			'string primitive type' => ['string', ValueType::String],
			'int primitive type' => ['int', ValueType::Int],
			'float primitive type' => ['float', ValueType::Float],
			'array primitive type' => ['array', ValueType::Array],
			'class name' => [self::class, self::class],
		];
	}

	#[DataProvider('knownTypes')]
	#[TestDox('It accepts a $_dataName and returns a ValueType or class-string.')]
	public function testItConvertsStringToValueType(string $string, string|ValueType $typed) {
		$this->assertEquals($typed, ArrayTypeUtils::checkPrimitive($string));
	}

	#[DataProvider('knownTypes')]
	#[TestDox('It accepts a $_dataName ValueType or class-string and returns a string.')]
	public function testItConvertsValueTypeToString(string $string, string|ValueType $typed) {
		$this->assertEquals($string, ArrayTypeUtils::checkValueType($typed));
	}

	public function testItReturnsNullForNull() {
		$this->assertNull(ArrayTypeUtils::checkValueType(null));
	}
}