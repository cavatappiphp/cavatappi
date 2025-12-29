<?php

namespace Cavatappi\Foundation\Reflection;

use Crell\Serde\TypeMap;

/**
 * A service that stores a map of keys and subclass names used for better serialization.
 */
interface TypeRegistry extends TypeMap {
	/**
	 * Get the supertype or interface this registry stores type maps for.
	 *
	 * @return class-string
	 */
	public static function getTypeToRegister(): string;
}