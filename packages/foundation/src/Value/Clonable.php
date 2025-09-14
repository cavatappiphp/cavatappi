<?php

namespace Cavatappi\Foundation\Value;

interface Clonable {
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
