<?php

namespace Cavatappi\Foundation;

/**
 * A read-only object that is internally consistent.
 *
 * @psalm-immutable
 */
interface Value {
	/**
	 * Returns true if this object is equal to the provided object
	 *
	 * @param mixed $other An object to test for equality with this object.
	 *
	 * @return boolean True if the other object is equal to this object
	 */
	public function equals(mixed $other): bool;

	/**
	 * Get information about the class' properties.
	 *
	 * @return ValueProperty[]
	 */
	public static function reflect(): array;

	/**
	 * Create a copy of the object with the given properties replacing existing ones.
	 *
	 * @throws InvalidValueProperties When the object cannot be copied.
	 *
	 * @param  mixed ...$props Fields to change for the new object.
	 * @return static
	 */
	public function with(mixed ...$props): static;
}
