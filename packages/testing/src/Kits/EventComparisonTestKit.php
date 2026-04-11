<?php

namespace Cavatappi\Test\Kits;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Test\Constraints\DomainEventChecker;
use PHPUnit\Framework\Constraint\Constraint;

trait EventComparisonTestKit {
	private static function eventEquivalentTo(DomainEvent $expected): Constraint {
		return new DomainEventChecker([$expected]);
	}

	private static function assertEventEquivalentTo(DomainEvent $expected, ?object $actual, string $message = ''): void {
		self::assertThat($actual, self::eventEquivalentTo($expected), $message);
	}

	private static function assertEventNotEquivalentTo(DomainEvent $expected, ?object $actual, string $message = ''): void {
		self::assertThat($actual, self::logicalNot(self::eventEquivalentTo($expected)), $message);
	}
}
