<?php

namespace Cavatappi\Infrastructure\Serialization;

use Cavatappi\Foundation\Service;
use Cavatappi\Foundation\Value;
use Crell\Serde\Serde;
use Crell\Serde\SerdeCommon;

class SerializationService implements Service {
	private Serde $internal;

	public function __construct()
	{
		$this->internal = new SerdeCommon(handlers: [
			new FieldHandler(),
			new Psr7UriHandler(),
			new UuidHandler(),
		]);
	}

	public function toArray(Value $object): array {
		return $this->internal->serialize($object, format: 'array');
	}

	public function toJson(Value $object): string {
		return $this->internal->serialize($object, format: 'json');
	}

	public function toYaml(Value $object): string {
		return $this->internal->serialize($object, format: 'yaml');
	}

	/**
	 * Deserialize
	 * 
	 * @template T
	 *
	 * @param array $input Serialized object.
	 * @param class-string<T> $as What the resulting object should be.
	 * @return T
	 */
	public function fromArray(array $input, string $as): mixed {
		return $this->internal->deserialize($input, from: 'array', to: $as);
	}

	/**
	 * Deserialize
	 * 
	 * @template T
	 *
	 * @param array $input Serialized object.
	 * @param class-string<T> $as What the resulting object should be.
	 * @return T
	 */
	public function fromJson(string $input, string $as): mixed {
		return $this->internal->deserialize($input, from: 'json', to: $as);
	}

	/**
	 * Deserialize
	 * 
	 * @template T
	 *
	 * @param array $input Serialized object.
	 * @param class-string<T> $as What the resulting object should be.
	 * @return T
	 */
	public function fromYaml(string $input, string $as): mixed {
		return $this->internal->deserialize($input, from: 'yaml', to: $as);
	}
}