<?php

namespace Cavatappi\Foundation\DomainEvent;

use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Value\ValueKit;
use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

trait DomainEventKit {
	use ValueKit;

	public string $type { get => static::class; }

	public readonly UuidInterface $id;
	public readonly DateTimeInterface $timestamp;

	private function setIdAndTime(?UuidInterface $id, ?DateTimeInterface $timestamp): void {
		$this->timestamp = $timestamp ?? new DateTimeImmutable();
		$this->id = $id ?? UuidFactory::date($this->timestamp);
	}
}