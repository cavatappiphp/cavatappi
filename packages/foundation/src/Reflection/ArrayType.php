<?php

namespace Cavatappi\Foundation\Reflection;

use Crell\Serde\ValueType;

/**
 * @internal Use ListType or MapType.
 */
interface ArrayType {
	/**
	 * @var class-string|ValueType
	 */
	public string|ValueType $arrayType { get; }
}
