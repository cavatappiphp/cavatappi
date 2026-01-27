<?php

namespace Cavatappi\Foundation\Validation;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Crell\Serde\Attributes\PostLoad;

interface Validated {
	/**
	 * Validate the object and throw an exception if conditions are not met.
	 *
	 * This is for (de)serialization, cloning, or any other object creation method that bypasses the constructor. This
	 * method should be called from the constructor after all necessary properties are set.
	 *
	 * @throws InvalidValueProperties When the object does not pass validation.
	 *
	 * @return void
	 */
	#[PostLoad]
	public function validate(): void;
}
