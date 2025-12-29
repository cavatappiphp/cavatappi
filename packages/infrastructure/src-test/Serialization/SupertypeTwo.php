<?php

namespace Cavatappi\Infrastructure\Test\Serialization;

use Cavatappi\Foundation\Value\ValueKit;

class SupertypeTwo implements Supertype {
	use ValueKit;

	public static function getKey(): string {
		return 'two';
	}

	public function __construct(
		public readonly string $twoValue,
		public readonly string $superValue,
	) {
	}
}