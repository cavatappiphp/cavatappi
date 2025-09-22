<?php

namespace Cavatappi\Foundation\Validation;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use ReflectionAttribute;
use ReflectionClass;

/**
 * Add some standard validation based on attributes.
 *
 * TODO: Make this extendable. This ties into a whole story of "how do we enable customization (and therefore
 * dependencies) in the largely static Value ecosystem?"
 */
trait ValidatedKit {
	/**
	 * Validate the object and throw an exception if conditions are not met.
	 *
	 * This is for (de)serialization, cloning, or any other object creation method that bypasses the constructor. This
	 * method should be called from the constructor after all necessary properties are set.
	 *
	 * @throws InvalidValueProperties When the object does not pass validation.
	 *
	 * @return void
	 */
	public function validate(): void {
		$classReflection = new ReflectionClass($this);

		$this->checkOneOfAttributes($classReflection);
	}

	private function checkOneOfAttributes(ReflectionClass $classReflection): void {
		$maybeAtLeastOne = $classReflection->getAttributes(AtLeastOneOf::class, ReflectionAttribute::IS_INSTANCEOF);
		if (!empty($maybeAtLeastOne)) {
			$atLeastOne = $maybeAtLeastOne[0]->newInstance();
			// Using strict comparision instead of isset() in case $prop is virtual.
			if (\array_all($atLeastOne->properties, fn($prop) => $this->$prop === null)) {
				throw new InvalidValueProperties(
					'At least one of these properties must be set: ' . \implode(',', $atLeastOne->properties)
				);
			}
		}

		$maybeExactlyOne = $classReflection->getAttributes(ExactlyOneOf::class, ReflectionAttribute::IS_INSTANCEOF);
		if (!empty($maybeExactlyOne)) {
			$exactlyOne = $maybeExactlyOne[0]->newInstance();
			// Using strict comparision instead of isset() in case $prop is virtual.
			$present = \array_filter($exactlyOne->properties, fn($prop) => $this->$prop !== null);
			if (\count($present) !== 1) {
				throw new InvalidValueProperties(
					'Exactly one of these properties must be set: ' . \implode(', ', $exactlyOne->properties)
				);
			}
		}
	}
}
