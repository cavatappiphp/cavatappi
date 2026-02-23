<?php

namespace Cavatappi\Website\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

class Page implements Value, Entity {
	use ValueKit;

	/**
	 * Create the page
	 *
	 * @param UuidInterface $id Unique identiifer for the page.
	 * @param string $template Type of page; will be used to determine template.
	 * @param array<string, mixed> $payload Data needed by the template.
	 */
	public function __construct(
		public readonly UuidInterface $id,
		public readonly string $template,
		#[MapType('mixed')] public readonly array $payload,
	)
	{
	}
}