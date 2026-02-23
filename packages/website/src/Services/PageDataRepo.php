<?php

namespace Cavatappi\Website\Services;

use Cavatappi\Foundation\Service;
use Cavatappi\Website\Entities\Page;
use Ramsey\Uuid\UuidInterface;

interface PageDataRepo extends Service {
	public function dataForPage(UuidInterface $pageId): ?Page;
}