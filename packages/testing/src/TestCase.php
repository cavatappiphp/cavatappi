<?php

namespace Cavatappi\Test;

use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Test\Constraints\UuidChecker;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Ramsey\Uuid\UuidInterface;

class TestCase extends PHPUnitTestCase {
	protected mixed $subject;

	protected function randomId(): UuidInterface {
		return UuidFactory::random();
	}

	public static function uuidEquals(UuidInterface $expected): UuidChecker {
		return new UuidChecker($expected);
	}

	public static function assertUuidEquals(UuidInterface $expected, UuidInterface $actual): void {
		self::assertThat($actual, self::uuidEquals($expected));
	}
}
