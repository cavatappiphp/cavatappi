<?php

namespace Cavatappi\Foundation\Test\Serialization;

use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

final readonly class SimpleValue implements Value {
	use ValueKit;

	public function __construct(
		public string $one,
		public int $two,
	)
	{
	}
}