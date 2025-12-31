<?php

namespace Cavatappi\Infrastructure\Test\Serialization;

use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

final readonly class ComplexValue implements Value {
	use ValueKit;

	public function __construct(
		public string $name,
		public SimpleValue $object,
		#[ListType('string')] public array $list,
		#[MapType('string')] public array $map,
		#[MapType('mixed')] public array $misc,
		public FieldValue $field,
	)
	{
	}
}