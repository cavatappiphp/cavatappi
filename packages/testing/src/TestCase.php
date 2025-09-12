<?php

namespace Cavatappi\Test;

use Cavatappi\Foundation\Value\Fields\Identifier;
use Cavatappi\Foundation\Value\Fields\RandomIdentifier;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase {
	protected mixed $subject;

	protected function randomId(bool $scrub = false): Identifier {
		return $this->scrubId(new RandomIdentifier());
	}

	protected function scrubId(Identifier $id): Identifier {
		return Identifier::fromByteString($id->toByteString());
	}
}
