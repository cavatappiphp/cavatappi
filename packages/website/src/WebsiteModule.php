<?php

namespace Cavatappi\Website;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;

class WebsiteModule implements Module {
	use FileDiscoveryKit;
	use ModuleKit;

	public static function serviceMapOverrides(): array
	{
		return [];
	}
}
