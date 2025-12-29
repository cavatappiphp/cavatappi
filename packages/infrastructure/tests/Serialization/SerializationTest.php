<?php

namespace Cavatappi\Infrastructure\Serialization;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Value;
use Cavatappi\Infrastructure\DefaultModule;
use Cavatappi\Infrastructure\Test\Serialization\ComplexValue;
use Cavatappi\Infrastructure\Test\Serialization\ExternalFields;
use Cavatappi\Infrastructure\Test\Serialization\FieldValue;
use Cavatappi\Infrastructure\Test\Serialization\HasSupertype;
use Cavatappi\Infrastructure\Test\Serialization\SimpleValue;
use Cavatappi\Infrastructure\Test\Serialization\SupertypeOne;
use Cavatappi\Infrastructure\Test\Serialization\SupertypeTwo;
use Cavatappi\Infrastructure\Test\Serialization\ValidatedValue;
use Cavatappi\Infrastructure\Test\TestModule;
use Cavatappi\Test\AppTest;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

final class SerializationTest extends AppTest {
	const INCLUDED_MODELS = [DefaultModule::class, TestModule::class];

	public static function objects() {
		return [
			'a simple object' => [
				'object' => new SimpleValue(one: 'one', two: 2),
				'array' => ['one' => 'one', 'two' => 2],
				'json' => '{"one": "one", "two": 2}',
				'yaml' => <<<'YAML'
				one: one
				two: 2

				YAML,
			],
			'a complex value' => [
				'object' => new ComplexValue(
					name: 'larry',
					object: new SimpleValue(one: 'bob', two: 5),
					list: ['this', 'is', 'a', 'list'],
					map: ['this' => 'is', 'a' => 'map'],
					field: new FieldValue(tags: ['builtDifferent', 'butNotReally']),
				),
				'array' => [
					'name' => 'larry',
					'object' => ['one' => 'bob', 'two' => 5],
					'list' => ['this', 'is', 'a', 'list'],
					'map' => ['this' => 'is', 'a' => 'map'],
					'field' => 'builtDifferent, butNotReally',
				],
				'json' => <<<'JSON'
				{
					"name": "larry",
					"object": {"one": "bob", "two": 5},
					"list": ["this", "is", "a", "list"],
					"map": {"this": "is", "a": "map"},
					"field": "builtDifferent, butNotReally"
				}
				JSON,
				'yaml' => <<<'YAML'
name: larry
object:
    one: bob
    two: 5
list:
    - this
    - is
    - a
    - list
map:
    this: is
    a: map
field: 'builtDifferent, butNotReally'

YAML,
			],
			'an object with externally-defined fields' => [
				'object' => new ExternalFields(
					uri: HttpMessageFactory::uri('https://smol.blog/'),
					uuid: UuidFactory::fromString('bb09f0b4-3fc8-4e76-8590-142df99460e2'),
					date: new DateTimeImmutable('2025-12-25 12:34:56.789'),
				),
				'array' => [
					'uri' => 'https://smol.blog/',
					'uuid' => 'bb09f0b4-3fc8-4e76-8590-142df99460e2',
					'date' => '2025-12-25T12:34:56.789+00:00',
				],
				'json' => <<<'JSON'
				{
					"uri": "https://smol.blog/",
					"uuid": "bb09f0b4-3fc8-4e76-8590-142df99460e2",
					"date": "2025-12-25T12:34:56.789+00:00"
				}
				JSON,
				'yaml' => <<<'YAML'
				uri: 'https://smol.blog/'
				uuid: bb09f0b4-3fc8-4e76-8590-142df99460e2
				date: '2025-12-25T12:34:56.789+00:00'

				YAML
			],
			'an object with an interface typehint (with SupertypeOne)' => [
				'object' => new HasSupertype(
					name: 'test one',
					super: new SupertypeOne(
						oneValue: 'alpha',
						superValue: 'beta',
					),
				),
				'array' => [
					'name' => 'test one',
					'super' => [
						'type' => 'one',
						'oneValue' => 'alpha',
						'superValue' => 'beta',
					],
				],
				'json' => <<<'JSON'
				{
					"name": "test one",
					"super": {
						"type": "one",
						"oneValue": "alpha",
						"superValue": "beta"
					}
				}
				JSON,
				'yaml' => <<<'YAML'
name: 'test one'
super:
    type: one
    oneValue: alpha
    superValue: beta

YAML,
			],
			'an object with an interface typehint (with SupertypeTwo)' => [
				'object' => new HasSupertype(
					name: 'test two',
					super: new SupertypeTwo(
						twoValue: 'gamma',
						superValue: 'theta',
					),
				),
				'array' => [
					'name' => 'test two',
					'super' => [
						'type' => 'two',
						'twoValue' => 'gamma',
						'superValue' => 'theta',
					],
				],
				'json' => <<<'JSON'
				{
					"name": "test two",
					"super": {
						"type": "two",
						"twoValue": "gamma",
						"superValue": "theta"
					}
				}
				JSON,
				'yaml' => <<<'YAML'
name: 'test two'
super:
    type: two
    twoValue: gamma
    superValue: theta

YAML,
			],
		];
	}

	#[DataProvider('objects')]
	#[TestDox('It will serialize $_dataName to a PHP array')]
	public function testToArray(Value $object, array $array, string $json, string $yaml) {
		self::assertEquals($array, $this->app->container->get(SerializationService::class)->toArray($object));
	}

	#[DataProvider('objects')]
	#[TestDox('It will serialize $_dataName to a JSON string')]
	public function testToJson(Value $object, array $array, string $json, string $yaml) {
		self::assertJsonStringEqualsJsonString($json, $this->app->container->get(SerializationService::class)->toJson($object));
	}

	#[DataProvider('objects')]
	#[TestDox('It will serialize $_dataName to a YAML string')]
	public function testToYaml(Value $object, array $array, string $json, string $yaml) {
		self::assertEquals($yaml, $this->app->container->get(SerializationService::class)->toYaml($object));
	}

	#[DataProvider('objects')]
	#[TestDox('It will deserialize $_dataName from a PHP array')]
	public function testFromArray(Value $object, array $array, string $json, string $yaml) {
		self::assertValueObjectEquals($object, $this->app->container->get(SerializationService::class)->fromArray($array, as: get_class($object)));
	}

	#[DataProvider('objects')]
	#[TestDox('It will deserialize $_dataName from a JSON string')]
	public function testFromJson(Value $object, array $array, string $json, string $yaml) {
		self::assertValueObjectEquals($object, $this->app->container->get(SerializationService::class)->fromJson($json, as: get_class($object)));
	}

	#[DataProvider('objects')]
	#[TestDox('It will deserialize $_dataName from a YAML string')]
	public function testFromYaml(Value $object, array $array, string $json, string $yaml) {
		self::assertValueObjectEquals($object, $this->app->container->get(SerializationService::class)->fromYaml($yaml, as: get_class($object)));
	}

	public function testItValidatesAfterDeserialization() {
		$this->expectException(InvalidValueProperties::class);

		$invalid = [
			'atLeastOne' => 'one',
			'atLeastTwo' => null,
			'atLeastThree' => null,
			'onlyOne' => 'one',
			'onlyTwo' => 'two',
			'onlyThree' => null,
		];

		$actual = $this->app->container->get(SerializationService::class)->fromArray($invalid, as: ValidatedValue::class);
	}
}