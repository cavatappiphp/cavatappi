<?php

namespace Cavatappi\Website\Services;

use Cavatappi\Foundation\Service;
use Cavatappi\Website\Entities\Page;
use Cavatappi\Website\Entities\WebsiteConfiguration;

interface PageBuilder extends Service {
	public function htmlForPage(Page $page, WebsiteConfiguration $config): string;
}