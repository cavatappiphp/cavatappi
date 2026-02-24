<?php

namespace Cavatappi\Website\Services;

use Cavatappi\Foundation\Service;
use Cavatappi\Website\Entities\Page;

interface PageDataRepo extends Service {
	public function dataForPage(string $pageId): ?Page;
}