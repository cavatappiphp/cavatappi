<?php

namespace Cavatappi\Website\Services;

use Cavatappi\Foundation\Service;
use Cavatappi\Website\Entities\BuildManifest;
use Psr\Log\LoggerInterface;

class SiteBuilder implements Service {
	public function __construct(
		private PageDataRepo $pageRepo,
		private PageBuilder $pageBuilder,
		private LoggerInterface $log,
	)
	{
	}

	public function build(BuildManifest $manifest): void {
		if ($manifest->clean) {
			$this->cleanBuildDirectory($manifest->config->pathOnDisk);
		}

		foreach ($manifest->pages as $pagePath => $pageId) {
			$pageData = $this->pageRepo->dataForPage($pageId);
			if (!$pageData) {
				$this->log->error("SiteBuilder: No data found for page $pageId");
				continue;
			}

			$html = $this->pageBuilder->htmlForPage(page: $pageData, config: $manifest->config);

			$result = file_put_contents(
				filename: $manifest->config->pathOnDisk . $pagePath,
				data: $html,
			);
			if ($result === false) {
				$this->log->error("SiteBuilder: unable to write page at $pagePath");
			}
		}
	}

	private function cleanBuildDirectory(string $dir): void {
		// Source - https://stackoverflow.com/a/4594262
		// Posted by Floern, modified by community. See post 'Timeline' for change history
		// Retrieved 2026-02-22, License - CC BY-SA 4.0

		$files = glob($dir . '{,.}*', GLOB_BRACE) ?: [];
		foreach($files as $file){ // iterate files
			if(is_file($file)) {
				unlink($file); // delete file
			}
		}
	}
}