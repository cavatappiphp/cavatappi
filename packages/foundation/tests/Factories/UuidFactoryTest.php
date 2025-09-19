<?php

namespace Cavatappi\Foundation\Factories;

use Cavatappi\Test\TestCase;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestDox;
use Ramsey\Uuid\UuidFactory as RamseyUuidFactory;
use Ramsey\Uuid\UuidInterface;
use Throwable;

final class UuidFactoryTest extends TestCase {
	#[TestDox('::random gives a random identifier')]
	public function testRandom() {
		$actual = UuidFactory::random();

		$this->assertInstanceOf(UuidInterface::class, $actual);
	}

	#[TestDox('::named gives a deterministic identifier')]
	public function testNamedEqual() {
		$id1 = UuidFactory::named(UuidFactory::NAMESPACE_URL, 'https://smol.blog/post/123');
		$id2 = UuidFactory::named(UuidFactory::NAMESPACE_URL, 'https://smol.blog/post/123');
		$this->assertEquals($id1, $id2);
	}

	#[TestDox('The namespace for ::named must be a UUID string')]
	public function testNamedBad() {
		$this->expectException(InvalidArgumentException::class);

		UuidFactory::named('not-a-uuid', 'https://smol.blog/post/123');
	}

	#[TestDox('::date can be called with a specfic date')]
	public function testDateString() {
		$date = new DateTimeImmutable('2022-02-22 22:22:22');
		$this->assertInstanceOf(UuidInterface::class, UuidFactory::date($date));
	}

	#[TestDox('DateIdentifier will be created with the current date by default')]
	public function testDateDefault() {
		$this->assertInstanceOf(UuidInterface::class, UuidFactory::date());
	}

	#[TestDox('Identifiers will serialize to and deserialize from a string')]
	public function testSerialization() {
		$idString = '10a353e4-0ccf-5f74-a77b-067262bfc588';
		$idObject = UuidFactory::named(UuidFactory::NAMESPACE_URL, 'https://smol.blog/post/123');

		$this->assertEquals($idString, $idObject->toString());
		$this->assertEquals(strval($idObject), strval(UuidFactory::fromString($idString)));
		$this->assertTrue($idObject->equals(UuidFactory::fromString($idString)));
	}

	#[TestDox('Identifiers will serialize to and deserialize from a byte-compressed string')]
	public function testByteSerialization() {
		$idObject = UuidFactory::named(UuidFactory::NAMESPACE_URL, 'https://smol.blog/post/123');
		$byteString = hex2bin('10a353e40ccf5f74a77b067262bfc588');

		$this->assertEquals($byteString, $idObject->getBytes());
		$this->assertEquals(strval($idObject), strval(UuidFactory::fromByteString($byteString)));
		$this->assertTrue($idObject->equals(UuidFactory::fromByteString($byteString)));
	}

	#[TestDox('It will throw an exception if it can\'t deserialize the string')]
	public function testStringException() {
		$this->expectException(Throwable::class);

		UuidFactory::fromString('not-an-id');
	}

	#[TestDox('It will throw an exception if it can\'t deserialize the byte-compressed string')]
	public function testByteException() {
		$this->expectException(Throwable::class);

		UuidFactory::fromByteString(hex2bin('10a353e40ccf5f74a77b067262bfc58888'));
	}

	#[TestDox('Identifier::nil() will return the nil UUID')]
	public function testNil() {
		$this->assertEquals('00000000-0000-0000-0000-000000000000', strval(UuidFactory::nil()));
	}

	public function testReplacement() {
		$mock = $this->createMock(RamseyUuidFactory::class);
		$mock
			->expects($this->once())
			->method('uuid5')
			->willReturn(new RamseyUuidFactory()->fromString('00000000-0000-0000-0000-000000000000'));

		UuidFactory::setSource($mock);

		$mockActual = UuidFactory::named(UuidFactory::NAMESPACE_URL, 'https://smol.blog/post/123');
		$this->assertEquals('00000000-0000-0000-0000-000000000000', strval($mockActual));

		UuidFactory::setSource(null);

		$actual = UuidFactory::named(UuidFactory::NAMESPACE_URL, 'https://smol.blog/post/123');
		$this->assertEquals('10a353e4-0ccf-5f74-a77b-067262bfc588', strval($actual));
	}
}