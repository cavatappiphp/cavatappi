<?php

namespace Cavatappi\Website\Services;

use Cavatappi\Foundation\Service;
use Cavatappi\Website\Entities\BuildManifest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class SiteBuilder implements Service {
	public function __construct(
		private PageDataRepo $pageRepo,
		private PageBuilder $pageBuilder,
		private LoggerInterface $log,
		private Filesystem $disk,
	)
	{
	}

	public function build(BuildManifest $manifest): void {
		if ($manifest->clean) {
			$this->disk->remove($manifest->config->pathOnDisk);
		}

		foreach ($manifest->pages as $pagePath => $pageId) {
			$pageData = $this->pageRepo->dataForPage($pageId);
			if (!$pageData) {
				$this->log->error("SiteBuilder: No data found for page $pageId");
				continue;
			}

			$html = $this->pageBuilder->htmlForPage(page: $pageData, config: $manifest->config);

			if (str_ends_with($pagePath, '/')) {
				$pagePath .= 'index.html';
			}

			$result = $this->disk->dumpFile(
				filename: Path::join($manifest->config->pathOnDisk, $pagePath),
				content: $html,
			);
			if ($result === false) {
				$this->log->error("SiteBuilder: unable to write page at $pagePath");
			}
		}
	}
}