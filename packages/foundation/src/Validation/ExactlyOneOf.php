<?php

namespace Cavatappi\Foundation\Validation;

use Attribute;

/**
 * For classes that require at only one of a set of properties.
 *
 * Will check that ONLY one of the listed properties is not null.
 */
#[Attribute(Attribute::TARGET_CLASS)]
readonly class ExactlyOneOf {
	/**
	 * @var string[]
	 */
	public array $properties;

	/**
	 * @param string ...$properties Class properties to check.
	 */
	public function __construct(string ...$properties) {
		$this->properties = $properties;
	}
}
