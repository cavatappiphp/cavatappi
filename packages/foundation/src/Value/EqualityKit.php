<?php

namespace Cavatappi\Foundation\Value;

use JsonSerializable;
use Stringable;

trait EqualityKit {
	/**
	 * Check for equality.
	 *
	 * This performs a very basic comparison; if a subclass has a more reliable method, it should override this method.
	 *
	 * @param mixed $other Object to compare to.
	 * @return boolean True if $this and $other are the same type with the same values.
	 */
	public function equals(mixed $other): bool {
		if (!\is_object($other) || \get_class($this) !== \get_class($other)) {
			return false;
		}

		$thisValues = \get_object_vars(...)->__invoke($this);
		foreach ($thisValues as $prop => $val) {
			if (\is_a($val, Stringable::class)) {
				if (\strval($val) != \strval($other->$prop)) {
					return false;
				}
				continue;
			}

			if (\is_a($val, JsonSerializable::class)) {
				if (\json_encode($val) != \json_encode($other->$prop)) {
					return false;
				}
				continue;
			}

			if ($val != $other->$prop) {
				return false;
			}
		}
		return true;
	}
}
