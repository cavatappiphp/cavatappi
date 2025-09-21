<?php

namespace Cavatappi\Foundation\Reflection;

use Attribute;
use Crell\Serde\Attributes\SequenceField;

/**
 * Indicates that this array is a list.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ListType extends SequenceField implements ArrayType {
	/**
	 * @param class-string $type Class or type for this array's values.
	 */
	public function __construct(string $type) {
		$arrayType = ArrayTypeUtils::checkPrimitive($type);
		parent::__construct(arrayType: $arrayType);
	}
}
