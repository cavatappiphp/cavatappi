<?php

namespace Cavatappi\Foundation\Value;

use Cavatappi\Foundation\Reflection\Reflectable;
use Cavatappi\Foundation\Reflection\ReflectableKit;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Validation\ValidatedKit;
use Cavatappi\Foundation\Value;

/**
 * A general-purpose base for Value objects that includes several interfaces and traits.
 */
abstract class DefaultValueBase implements Value, Clonable, Reflectable, Validated {
	use EqualityKit;
	use CloneKit;
	use ValidatedKit;
	use ReflectableKit;
}
