<?php

namespace Cavatappi\Foundation\DomainEvent;

use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Value\ValueKit;
use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

trait DomainEventKit {
	use ValueKit;

	/**
	 * Use the fully-qualified class name for the Event key.
	 *
	 * @var string
	 */
	public string $type { get => static::class; }

	/**
	 * Unique identifier for this Event.
	 *
	 * @var UuidInterface
	 */
	public readonly UuidInterface $id;
	/**
	 * Date and time that this Event occurred.
	 *
	 * @var DateTimeInterface
	 */
	public readonly DateTimeInterface $timestamp;

	private function setIdAndTime(?UuidInterface $id, ?DateTimeInterface $timestamp): void {
		$this->timestamp = $timestamp ?? new DateTimeImmutable();
		$this->id = $id ?? UuidFactory::date($this->timestamp);
	}
}
