<?php

namespace Cavatappi\Foundation\DomainEvent;

use Cavatappi\Test\TestCase;
use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

// If this class cannot be instantiated it is a breaking change.
final class SimpleDomainEvent implements DomainEvent {
	use DomainEventKit;

	public function __construct(
		public readonly UuidInterface $userId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null
	)
	{
		$this->setIdAndTime($id, $timestamp);
	}

	public null $entityId { get => null; }
	public null $aggregateId { get => null; }
	public null $processId { get => null; }
}

final class DomainEventKitTest extends TestCase {
	public function testAMinimalDomainEventCanBeInstantiated() {
		$actual = new SimpleDomainEvent(userId: $this->randomId());

		$this->assertInstanceOf(DomainEvent::class, $actual);
		$this->assertEquals(SimpleDomainEvent::class, $actual->type);
		$this->assertInstanceOf(UuidInterface::class, $actual->id);
		$this->assertInstanceOf(DateTimeInterface::class, $actual->timestamp);
	}

	public function testKitMethodWillNotOverridePassedValues() {
		$expectedId = $this->randomId();
		$expectedTimestamp = new DateTimeImmutable();
		$actual = new SimpleDomainEvent(userId: $this->randomId(), id: $expectedId, timestamp: $expectedTimestamp);

		$this->assertEquals($expectedId, $actual->id);
		$this->assertEquals($expectedTimestamp, $actual->timestamp);
	}

	public function testWillUseGivenTimestampToCreateId() {
		$first = new SimpleDomainEvent(userId: $this->randomId(), timestamp: new DateTimeImmutable('2025-05-04 12:34:56'));
		$second = new SimpleDomainEvent(userId: $this->randomId(), timestamp: new DateTimeImmutable('2025-07-04 12:34:56'));

		$this->assertEquals(-1, $first->id->compareTo($second->id));
	}
}