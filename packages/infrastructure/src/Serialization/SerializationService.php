<?php

namespace Cavatappi\Infrastructure\Serialization;

use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value;
use Crell\Serde\Serde;
use Crell\Serde\SerdeCommon;

/**
 * Handle serialization and deserialization of Value objects.
 *
 * A thin wrapper around Crell\Serde.
 */
class SerializationService implements Service {
	/**
	 * Internal Serde object.
	 *
	 * @var Serde
	 */
	private Serde $internal;

	/**
	 * Construct the service
	 *
	 * @param TypeRegistryRegistry $typeRegistries Available TypeRegistries to register with this service.
	 */
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

	/**
	 * Convert a Value object to a serializable array.
	 *
	 * @param Value $object Object to serialize.
	 * @return array
	 */
	public function toArray(Value $object): array {
		return $this->serializeValue($object, format: 'array');
	}

	/**
	 * Convert a Value object to a JSON string.
	 *
	 * @param Value $object Object to serialize.
	 * @return string
	 */
	public function toJson(Value $object): string {
		return $this->serializeValue($object, format: 'json');
	}

	/**
	 * Convert a Value object to a YAML string.
	 *
	 * @param Value $object Object to serialize.
	 * @return string
	 */
	public function toYaml(Value $object): string {
		return $this->serializeValue($object, format: 'yaml');
	}

	/**
	 * Deserialize an object from an array.
	 *
	 * @template OBJ
	 *
	 * @param array             $input Serialized object.
	 * @param class-string<OBJ> $as    What the resulting object should be.
	 * @return OBJ
	 */
	public function fromArray(array $input, string $as): mixed {
		return $this->deserializeValue($input, from: 'array', to: $as);
	}

	/**
	 * Deserialize an object from a JSON string.
	 *
	 * @template OBJ
	 *
	 * @param string            $input Serialized object.
	 * @param class-string<OBJ> $as    What the resulting object should be.
	 * @return OBJ
	 */
	public function fromJson(string $input, string $as): mixed {
		return $this->deserializeValue($input, from: 'json', to: $as);
	}

	/**
	 * Deserialize an object from a YAML string.
	 *
	 * @template OBJ
	 *
	 * @param string            $input Serialized object.
	 * @param class-string<OBJ> $as    Identifier of the entry to look for.
	 * @return OBJ
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
