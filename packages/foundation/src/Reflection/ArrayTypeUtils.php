<?php

namespace Cavatappi\Foundation\Reflection;

use Crell\Serde\ValueType;

/**
 * @internal Use ListType or MapType attributes.
 */
final class ArrayTypeUtils {
	/**
	 * Check a type string and convert to a ValueType if necessary.
	 *
	 * @internal
	 * @param string $type Type to convert.
	 * @return string|ValueType
	 */
	public static function checkPrimitive(string $type): string|ValueType {
		return match ($type) {
			'string' => ValueType::String,
			'int' => ValueType::Int,
			'float' => ValueType::Float,
			'array' => ValueType::Array,
			default => $type,
		};
	}

	/**
	 * Check an array type value and convert to a string if necessary.
	 *
	 * @internal
	 * @param string|ValueType|null $type Type to convert.
	 * @return string|null
	 */
	public static function checkValueType(string|ValueType|null $type): ?string {
		return match ($type) {
			null => null,
			ValueType::String => 'string',
			ValueType::Int => 'int',
			ValueType::Float => 'float',
			ValueType::Array => 'array',
			default => \is_string($type) ? $type : null,
		};
	}
}
