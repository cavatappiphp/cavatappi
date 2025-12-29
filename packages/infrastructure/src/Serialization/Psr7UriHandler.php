<?php

namespace Cavatappi\Infrastructure\Serialization;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Crell\Serde\Attributes\Field;
use Crell\Serde\DeformatterResult;
use Crell\Serde\Deserializer;
use Crell\Serde\PropertyHandler\{Exporter, Importer};
use Crell\Serde\Serializer;
use Psr\Http\Message\UriInterface;

/**
 * Attempt at handling PSR-7 URI objects in Serde.
 */
class Psr7UriHandler implements Exporter, Importer {
	/**
	 * Can this handler handle this field.
	 *
	 * @param Field $field  Field information.
	 * @param mixed          $value  Object value.
	 * @param string         $format Format to serialize to.
	 * @return boolean
	 */
	public function canExport(Field $field, mixed $value, string $format): bool {
		return $value instanceof UriInterface;
	}

	/**
	 * Serizlize the given value.
	 *
	 * @param Serializer     $serializer   Serde serializer.
	 * @param Field $field        Field information.
	 * @param mixed          $value        Value to serialize.
	 * @param mixed          $runningValue Current serialized value.
	 * @return mixed
	 */
	public function exportValue(Serializer $serializer, Field $field, mixed $value, mixed $runningValue): mixed {
		/** @var UriInterface */
		$uuidObject = $value;
		return $serializer->formatter->serializeString($runningValue, $field, $uuidObject->__toString());
	}

	/**
	 * Can this field be imported.
	 *
	 * @param Field $field  Field information.
	 * @param string         $format Serialized format.
	 * @return boolean
	 */
	public function canImport(Field $field, string $format): bool {
		return is_a($field->phpType, UriInterface::class, allow_string: true);
	}

	/**
	 * Deserialize a Field string to its object value.
	 *
	 * @param Deserializer   $deserializer Serde deserializer object.
	 * @param Field $field        Field information.
	 * @param mixed          $source       Serialized value.
	 * @return UriInterface Deserialization result
	 */
	public function importValue(Deserializer $deserializer, Field $field, mixed $source): mixed {
		$string = $deserializer->deformatter->deserializeString($source, $field);

		if ($string instanceof DeformatterResult || $string === null) {
			return null;
		}

		return HttpMessageFactory::uri($string);
	}
}
