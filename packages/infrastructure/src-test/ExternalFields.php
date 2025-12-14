<?php

namespace Cavatappi\Foundation\Test\Serialization;

use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use DateTimeInterface;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\UuidInterface;

final readonly class ExternalFields implements Value {
	use ValueKit;

	public function __construct(
		public UriInterface $uri,
		public UuidInterface $uuid,
		public DateTimeInterface $date,
	)
	{
	}
}