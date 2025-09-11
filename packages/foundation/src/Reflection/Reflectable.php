<?php

namespace Cavatappi\Foundation\Reflection;

use Cavatappi\Foundation\Value\ValueProperty;

interface Reflectable {
	/**
	 * Get information about the class' properties.
	 *
	 * @return ValueProperty[]
	 */
	public static function reflect(): array;
}
