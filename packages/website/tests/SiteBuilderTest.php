<?php

namespace Cavatappi\Website\Services;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Test\AppTest;
use Cavatappi\Website\Entities\BuildManifest;
use Cavatappi\Website\Entities\Page;
use Cavatappi\Website\Entities\WebsiteConfiguration;
use Cavatappi\Website\WebsiteModule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class SiteBuilderTest extends AppTest {
	const INCLUDED_MODELS = [WebsiteModule::class];

	protected PageBuilder & MockObject $pageBuilderMock;
	protected PageDataRepo & Stub $pageRepoMock;
	protected LoggerInterface & MockObject $logMock;

	private Filesystem $disk;
	private string $testDir;

	protected function createMockServices(): array
	{
		$this->pageBuilderMock = $this->createMock(PageBuilder::class);
		$this->pageRepoMock = $this->createStub(PageDataRepo::class);
		$this->logMock = $this->createMock(LoggerInterface::class);

		return [
			...parent::createMockServices(),
			PageBuilder::class => fn() => $this->pageBuilderMock,
			PageDataRepo::class => fn() => $this->pageRepoMock,
			LoggerInterface::class => fn() => $this->logMock,
		];
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this->disk = new Filesystem();

		$this->testDir = Path::join(sys_get_temp_dir(), $this->randomId());
		$this->disk->mkdir($this->testDir);
	}

	protected function tearDown(): void
	{
		$this->disk->remove($this->testDir);

		parent::tearDown();
	}

	public function testHappyPath() {
		$builder = $this->app->container->get(SiteBuilder::class);

		$pageOne = new Page(
			id: $this->randomId(),
			template: 'homepage',
			payload: [
				'mobile' => true,
				'tagline' => 'Where Penne meets Pork'
			],
		);
		$pageTwo = new Page(
			id: $this->randomId(),
			template: 'landing',
			payload: [
				'ajax' => true,
			],
		);
		$pageThree = new Page(
			id: $this->randomId(),
			template: 'detail',
			payload: [
				'headline' => 'This one weird trick to better coffee at home',
				'body' => 'Buy local and grind the beans at home.'
			],
		);

		$config = new WebsiteConfiguration(
			pathOnDisk: $this->testDir,
			baseUrl: HttpMessageFactory::uri('https://copy.pasta/'),
		);
		$manifest = new BuildManifest(
			config: $config,
			pages: [
				'/' => $pageOne->id,
				'/blog/' => $pageTwo->id,
				'blog/this-one-weird.html' => $pageThree->id,
			],
		);

		$this->pageRepoMock->method('dataForPage')->willReturnCallback(fn($id) => match(strval($id)) {
			$pageOne->id->toString() => $pageOne,
			$pageTwo->id->toString() => $pageTwo,
			$pageThree->id->toString() => $pageThree,
		});

		$this->pageBuilderMock->
			expects($this->exactly(count($manifest->pages)))->
			method('htmlForPage')->
			with(
				page: $this->isInstanceOf(Page::class),
				config: $this->valueObjectEquals($config),
			)->
			willReturn('<!DOCTYPE html><html></html>');
		
		$this->logMock->expects($this->never())->method('error');

		$builder->build($manifest);
		
		self::assertFileExists($this->testDir . '/index.html');
		self::assertFileExists($this->testDir . '/blog/index.html');
		self::assertFileExists($this->testDir . '/blog/this-one-weird.html');
	}
}