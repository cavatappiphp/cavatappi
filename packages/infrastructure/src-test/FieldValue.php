<?php

namespace Cavatappi\Foundation\Test\Serialization;

use Cavatappi\Foundation\Fields\Field;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

final readonly class FieldValue implements Value, Field {
	use ValueKit;

	public function __construct(
		public array $tags
	) {
	}

	public function __toString(): string
	{
		return implode(', ', $this->tags);
	}

	public static function fromString(string $serialized): static
	{
		return new self(array_map(fn($tag) => trim($tag), explode(',', $serialized)));
	}
}