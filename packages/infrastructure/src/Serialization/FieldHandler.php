<?php

namespace Cavatappi\Foundation\Fields;

use Crell\Serde\Attributes\Field as FieldAttribute;
use Crell\Serde\DeformatterResult;
use Crell\Serde\Deserializer;
use Crell\Serde\PropertyHandler\{Exporter, Importer};
use Crell\Serde\Serializer;

/**
 * Attempt at handling Field objects in Serde.
 */
class FieldHandler implements Exporter, Importer {
	/**
	 * Can this handler handle this field.
	 *
	 * @param FieldAttribute $field  Field information.
	 * @param mixed          $value  Object value.
	 * @param string         $format Format to serialize to.
	 * @return boolean
	 */
	public function canExport(FieldAttribute $field, mixed $value, string $format): bool {
		return $value instanceof Field;
	}

	/**
	 * Serizlize the given value.
	 *
	 * @param Serializer     $serializer   Serde serializer.
	 * @param FieldAttribute $field        Field information.
	 * @param mixed          $value        Value to serialize.
	 * @param mixed          $runningValue Current serialized value.
	 * @return mixed
	 */
	public function exportValue(Serializer $serializer, FieldAttribute $field, mixed $value, mixed $runningValue): mixed {
		/** @var Field */
		$fieldObject = $value;
		return $serializer->formatter->serializeString($runningValue, $field, $fieldObject->__toString());
	}

	/**
	 * Can this field be imported.
	 *
	 * @param FieldAttribute $field  Field information.
	 * @param string         $format Serialized format.
	 * @return boolean
	 */
	public function canImport(FieldAttribute $field, string $format): bool {
		return is_a($field->phpType, Field::class, allow_string: true);
	}

	/**
	 * Deserialize a Field string to its object value.
	 *
	 * @param Deserializer   $deserializer Serde deserializer object.
	 * @param FieldAttribute $field        Field information.
	 * @param mixed          $source       Serialized value.
	 * @return Field Deserialization result
	 */
	public function importValue(Deserializer $deserializer, FieldAttribute $field, mixed $source): mixed {
		$string = $deserializer->deformatter->deserializeString($source, $field);

		if ($string instanceof DeformatterResult || $string === null) {
			return null;
		}

		/** @var class-string<Field> */
		$fieldType = $field->phpType;
		return $fieldType::fromString($string);
	}
}
