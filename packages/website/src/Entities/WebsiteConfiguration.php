<?php

namespace Cavatappi\Website\Entities;

use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Psr\Http\Message\UriInterface;

class WebsiteConfiguration implements Value {
	use ValueKit;

	public readonly string $pathOnDisk;

	/**
	 * Undocumented function
	 * 
	 * @param string $pathOnDisk Path to the folder to place the built files.
	 * @param UriInterface $baseUrl URL the site will live at.
	 */
	public function __construct(
		string $pathOnDisk,
		public readonly UriInterface $baseUrl,
	)
	{
		$this->pathOnDisk = rtrim($pathOnDisk, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}
}