<?php

namespace Cavatappi\Infrastructure\Test\Serialization;

use Cavatappi\Foundation\Validation\AtLeastOneOf;
use Cavatappi\Foundation\Validation\ExactlyOneOf;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Validation\ValidatedKit;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

#[AtLeastOneOf('atLeastOne', 'atLeastTwo', 'atLeastThree')]
#[ExactlyOneOf('onlyOne', 'onlyTwo', 'onlyThree')]
final class ValidatedValue implements Value, Validated {
	use ValueKit;
	use ValidatedKit;

	public function __construct(
		public readonly ?string $atLeastOne,
		public readonly ?string $atLeastTwo,
		public readonly ?string $atLeastThree,
		public readonly ?string $onlyOne,
		public readonly ?string $onlyTwo,
		public readonly ?string $onlyThree,
	) {
		$this->validate();
	}
}