<?php

namespace Cavatappi\Foundation\Module;

use ReflectionClass;

trait FileDiscoveryKit {
	/**
	 * Get the list of discoverable classes in this Module.
	 *
	 * @return class-string[]
	 */
	private static function listClasses(): array {
		$dir = new ReflectionClass(static::class)->getFileName();
		if ($dir === false) {
			return []; // @codeCoverageIgnore
		}
		return ModuleUtils::getClassNamesFromFolder(folder: \dirname(\realpath($dir)));
	}
}
