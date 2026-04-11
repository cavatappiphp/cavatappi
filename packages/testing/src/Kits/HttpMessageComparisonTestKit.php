<?php

namespace Cavatappi\Test\Kits;

use Cavatappi\Test\Constraints\HttpMessageIsEquivalent;
use PHPUnit\Framework\Constraint\Constraint;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait HttpMessageComparisonTestKit {
	private static function httpMessageEqualTo(RequestInterface|ResponseInterface $expected): Constraint {
		return new HttpMessageIsEquivalent($expected);
	}

	private static function assertHttpMessageEquals(RequestInterface|ResponseInterface $expected, ?object $actual, string $message = ''): void {
		self::assertThat($actual, self::httpMessageEqualTo($expected), $message);
	}

	private static function assertHttpMessageNotEquals(RequestInterface|ResponseInterface $expected, ?object $actual, string $message = ''): void {
		self::assertThat($actual, self::logicalNot(self::httpMessageEqualTo($expected)), $message);
	}
}
