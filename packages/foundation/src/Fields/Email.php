<?php

namespace Cavatappi\Foundation\Fields;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * Field to store a valid email address.
 *
 * Not stored in any special format, just validated on creation.
 */
readonly class Email implements Value, Field, Validated {
	use ValueKit;

	/**
	 * @param string $email Email address to save.
	 */
	public function __construct(public string $email) {
		$this->validate();
	}

	/**
	 * Validate the field.
	 *
	 * Uses PHP's FILTER_VALIDATE_EMAIL.
	 *
	 * @throws InvalidValueProperties When $email is not a valid email.
	 *
	 * @return void
	 */
	public function validate(): void {
		if (!\filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidValueProperties("{$this->email} is not a valid email address.");
		}
	}

	/**
	 * Get the string value of the email.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->email;
	}

	/**
	 * Create an email address from a string.
	 *
	 * @param  string $string Valid email address.
	 * @return static
	 */
	public static function fromString(string $string): static {
		return new self($string);
	}
}
