<?php

namespace Cavatappi\Infrastructure\Test\Serialization;

use Cavatappi\Foundation\Reflection\TypeRegistry;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Registry\RegistryKit;
use Cavatappi\Foundation\Service;

class SupertypeRegistry implements Service, Registry, TypeRegistry {
	use RegistryKit;

	public static function getInterfaceToRegister(): string {
		return Supertype::class;
	}

	public static function getTypeToRegister(): string {
		return Supertype::class;
	}

	public function keyField(): string {
		return 'type';
	}

	public function findClass(string $id): ?string {
		return $this->library[$id] ?? null;
	}

	public function findIdentifier(string $class): ?string {
		return array_search($class, $this->library, true) ?: null;
	}
}
