<?php

namespace Cavatappi\Test\Constraints;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Value\Clonable;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\Exporter;
use Ramsey\Uuid\UuidInterface;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Compare two DomainEvents while ignoring ID and timestamp.
 */
class DomainEventChecker extends Constraint {
	/**
	 * @param array<DomainEvent&Clonable> $expectedEvents Events to check against.
	 * @param boolean                     $checkProcess   True if the events should be checked for the same processId.
	 */
	public function __construct(private array $expectedEvents, private bool $checkProcess = false) {
	}

	/**
	 * The current expected event.
	 *
	 * @var DomainEvent&Clonable
	 */
	private DomainEvent & Clonable $expected;

	/**
	 * Expected processId.
	 *
	 * @var UuidInterface|null
	 */
	private ?UuidInterface $processId = null;

	public function toString(): string {
		return 'two Events are equivalent';
	}

	protected function failureDescription($other): string {
		return $this->toString();
	}

	protected function matches(mixed $other): bool {
		$maybeExpected = \array_shift($this->expectedEvents);
		if (!isset($maybeExpected) || !\is_a($maybeExpected, DomainEvent::class) || !\is_a($maybeExpected, Clonable::class)) {
			throw new InvalidArgumentException('Expected value is not a Clonable DomainEvent.');
		}
		$this->expected = $maybeExpected;

		if (!\is_a($other, DomainEvent::class) || !\is_a($other, Clonable::class)) {
			throw new InvalidArgumentException('Object is not a Clonable DomainEvent.');
		}

		if ($this->checkProcess) {
			$this->processId ??= $other->processId;
		}

		$expectedComparison = $this->despecializeEvent($this->expected);
		$actualComparison = $this->despecializeEvent($other);
		if (isset($this->processId)) {
			$expectedComparison = $this->expected->with(processId: $this->processId);
		}

		return $expectedComparison == $actualComparison;
	}

	protected function fail(mixed $other, string $description, ?ComparisonFailure $comparisonFailure = null): never {
		if ($comparisonFailure === null) {
			$expectedComparison = $this->despecializeEvent($this->expected);
			$actualComparison = $this->despecializeEvent($other);
			if (isset($this->processId)) {
				$expectedComparison = $this->expected->with(processId: $this->processId);
			}

			$comparisonFailure = new ComparisonFailure(
				$this->expected,
				$other,
				Exporter::export($expectedComparison),
				Exporter::export($actualComparison),
				isset($this->expected) ? 'Failed asserting that two Events are equivalent.' : 'Event was not expected.'
			);
		}

		parent::fail($other, $description, $comparisonFailure);
	}

	private function despecializeEvent(DomainEvent & Clonable $event): DomainEvent & Clonable {
		return $event->with(id: UuidFactory::nil(), timestamp: new DateTimeImmutable('@0'));
	}
}
