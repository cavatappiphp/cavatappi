<?php

namespace Cavatappi\Website\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

class BuildManifest implements Value, Entity {
	use ValueKit;

	/**
	 * Unique ID for this manifest
	 *
	 * @var UuidInterface
	 */
	public readonly UuidInterface $id;

	/**
	 * Create the manifest.
	 * 
	 * @param WebsiteConfiguration $config General information about the website being built.
	 * @param array<string, string> $pages List of paths to pages and their associated identifiers.
	 * @param boolean $clean Whether any existing files should be removed before building. Default false.
	 * @param UuidInterface $id Unique ID for this manifest. Default is a new randomly generated UUID.
	 */
	public function __construct(
		public readonly WebsiteConfiguration $config,
		#[MapType('string')] public readonly array $pages,
		public readonly bool $clean = false,
		?UuidInterface $id = null,
	) {
		$this->id = $id ?? UuidFactory::random();
	}
}