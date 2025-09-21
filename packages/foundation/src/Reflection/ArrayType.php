<?php

namespace Cavatappi\Foundation\Reflection;

use Crell\Serde\ValueType;

/**
 * @internal Use ListType or MapType.
 */
interface ArrayType {
	/**
	 * @var class-string|ValueType|null
	 */
	public string|ValueType|null $arrayType { get; }
}
