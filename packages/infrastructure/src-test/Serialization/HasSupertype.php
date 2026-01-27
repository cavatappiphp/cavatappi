<?php

namespace Cavatappi\Infrastructure\Test\Serialization;

use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

class HasSupertype implements Value {
	use ValueKit;

	public function __construct(
		public readonly string $name,
		public readonly Supertype $super,
	) {
	}
}