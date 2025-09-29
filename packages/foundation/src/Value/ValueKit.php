<?php

namespace Cavatappi\Foundation\Value;

use Cavatappi\Foundation\Exceptions\CodePathNotSupported;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Reflection\ArrayType;
use Cavatappi\Foundation\Reflection\ArrayTypeUtils;
use Cavatappi\Foundation\Reflection\DisplayName;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Reflection\Target;
use Cavatappi\Foundation\Reflection\ValueProperty;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Stringable;
use Throwable;

trait ValueKit {
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

			if ($val != $other->$prop) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Create a copy of the object with the given properties replacing existing ones.
	 *
	 * @throws InvalidValueProperties When the object cannot be copied.
	 *
	 * @param  mixed ...$props Fields to change for the new object.
	 * @return static
	 */
	public function with(mixed ...$props): static {
		// TODO PHP 8.5 Replace with new clone syntax.
		$base = [];
		$reflection = static::reflect();
		foreach ($reflection as $prop) {
			$argName = $prop->name;
			$base[$argName] = $this->$argName;
		}

		try {
			// @phpstan-ignore-next-line
			$new = new static(...\array_merge($base, $props));

			// TODO PHP 8.5 Manually call $new->validate after cloning
			// if (\is_a($new, Validated::class)) {
			// 	$new->validate();
			// }

			return $new;
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: 'Unable to copy Value in ' . static::class . '::with(): ' . $e->getMessage(),
				previous: $e,
			);
		}
	}

	/**
	 * Get information about this class' properties.
	 *
	 * @return array<string, ValueProperty>
	 */
	public static function reflect(): array {
		$class = new ReflectionClass(static::class);
		$propReflections = $class->getProperties(ReflectionProperty::IS_PUBLIC);
		$props = [];
		foreach ($propReflections as $prop) {
			$info = static::getPropertyInfo($prop, $class);
			if (isset($info)) {
				$props[$prop->getName()] = $info;
			}
		}
		return $props;
	}

	/**
	 * Get the ValueProperty object for the given property.
	 *
	 * The individual ReflectionProperty and whole class ReflectionClass are provided to avoid re-work. To override
	 * an individual property, check `$prop->getName()`.
	 *
	 * If a field should be disculded from reflection (such as a virtual property), return `null`.
	 *
	 * @throws CodePathNotSupported If a property has a union/intersection type or an array does not have an ArrayType.
	 *
	 * @param  ReflectionProperty $prop  ReflectionProperty for the property being evaluated.
	 * @param  ReflectionClass    $class ReflectionClass for this class.
	 * @return ValueProperty|null
	 */
	protected static function getPropertyInfo(ReflectionProperty $prop, ReflectionClass $class): ?ValueProperty {
		if ($prop->isVirtual()) {
			return null;
		}

		$type = $prop->getType();
		if (!isset($type) || \get_class($type) !== ReflectionNamedType::class) {
			throw new CodePathNotSupported(
				message: 'Union/intersection types are not supported; ' .
				'change the type or override the getPropertyInfo() method.',
				location: 'ValueKit::getPropertyInfo via' . static::class,
			);
		}

		$params = [
			'name' => $prop->getName(),
			'type' => $type->getName(),
		];

		if ($params['type'] === 'array') {
			$attributeReflections = $prop->getAttributes(ArrayType::class, ReflectionAttribute::IS_INSTANCEOF);
			$arrayType = ($attributeReflections[0] ?? null)?->newInstance() ?? null;
			if (!isset($arrayType)) {
				throw new CodePathNotSupported(
					message: 'Arrays must have either a ListType or MapType attribute; ' .
					'add the attribute or override the getPropertyInfo() method.',
					location: 'Value::getPropertyInfo via' . static::class,
				);
			}

			$params['type'] = \get_class($arrayType) === MapType::class ? 'map' : 'list';

			$params['items'] = ArrayTypeUtils::checkValueType($arrayType->arrayType);
		}

		$targetReflection = $prop->getAttributes(Target::class, ReflectionAttribute::IS_INSTANCEOF);
		$target = ($targetReflection[0] ?? null)?->newInstance() ?? null;
		if ($target) {
			$params['target'] = $target->type;
		}

		$nameReflection = $prop->getAttributes(DisplayName::class, ReflectionAttribute::IS_INSTANCEOF);
		$displayName = ($nameReflection[0] ?? null)?->newInstance() ?? null;
		if ($displayName) {
			$params['displayName'] = $displayName->name;
		}

		// @codeCoverageIgnoreStart
		// This exception is very useful during framework development but difficult to trigger in tests.
		try {
			return new ValueProperty(...$params);
		} catch (InvalidValueProperties $e) {
			throw new CodePathNotSupported(
				message: "Error reflecting field {$prop->getName()}: {$e->getMessage()}",
				previous: $e
			);
		}
		// @codeCoverageIgnoreEnd
	}
}
