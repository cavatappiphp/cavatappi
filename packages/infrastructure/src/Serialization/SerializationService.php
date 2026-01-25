<?php

namespace Cavatappi\Infrastructure\Serialization;

use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value;
use Crell\Serde\Serde;
use Crell\Serde\SerdeCommon;

class SerializationService implements Service {
	private Serde $internal;

	public function __construct(private TypeRegistryRegistry $typeRegistries) {
		$this->internal = new SerdeCommon(
			handlers: [
				new FieldHandler(),
				new Psr7UriHandler(),
				new UuidHandler(),
			],
			typeMaps: $this->typeRegistries->getSerdeTypeMap(),
		);
	}

	public function toArray(Value $object): array {
		return $this->serializeValue($object, format: 'array');
	}

	public function toJson(Value $object): string {
		return $this->serializeValue($object, format: 'json');
	}

	public function toYaml(Value $object): string {
		return $this->serializeValue($object, format: 'yaml');
	}

	/**
	 * Deserialize
	 *
	 * @template T
	 *
	 * @param array           $input Serialized object.
	 * @param class-string<T> $as    What the resulting object should be.
	 * @return T
	 */
	public function fromArray(array $input, string $as): mixed {
		return $this->deserializeValue($input, from: 'array', to: $as);
	}

	/**
	 * Deserialize
	 *
	 * @template T
	 *
	 * @param array           $input Serialized object.
	 * @param class-string<T> $as    What the resulting object should be.
	 * @return T
	 */
	public function fromJson(string $input, string $as): mixed {
		return $this->deserializeValue($input, from: 'json', to: $as);
	}

	/**
	 * Deserialize
	 *
	 * @template T
	 *
	 * @param array           $input Serialized object.
	 * @param class-string<T> $as    What the resulting object should be.
	 * @return T
	 */
	public function fromYaml(string $input, string $as): mixed {
		return $this->deserializeValue($input, from: 'yaml', to: $as);
	}

	private function serializeValue(object $input, string $format) {
		return $this->internal->serialize($input, format: $format);
	}

	private function deserializeValue(string|array $input, string $from, string $to) {
		$obj = $this->internal->deserialize($input, from: $from, to: $to);
		// Would like to make this work with Serde itself, but it relies on an attribute on the object itself.
		if (\is_a($obj, Validated::class)) {
			$obj->validate();
		}

		return $obj;
	}
}
