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
}
