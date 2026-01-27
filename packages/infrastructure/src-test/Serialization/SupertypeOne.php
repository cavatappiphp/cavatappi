<?php

namespace Cavatappi\Infrastructure\Test\Serialization;

use Cavatappi\Foundation\Value\ValueKit;

class SupertypeOne implements Supertype {
	use ValueKit;

	public static function getKey(): string {
		return 'one';
	}

	public function __construct(
		public readonly string $oneValue,
		public readonly string $superValue,
	) {
	}
}