<?php

namespace Cavatappi\Foundation\Registry;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Cavatappi\Test\TestCase;
use Psr\Container\ContainerInterface;
use Cavatappi\Foundation\Exceptions\CodePathNotSupported;
use Cavatappi\Foundation\Exceptions\ServiceNotRegistered;

interface TestServiceRegisterable extends Registerable {}
interface TestServiceConfigurable extends ConfiguredRegisterable {}

class TestServiceRegistry implements Registry {
	use ServiceRegistryKit;
	private static string $myInterface = TestServiceRegisterable::class;
	public function __construct(ContainerInterface $container) { $this->container = $container; }
	public static function getInterfaceToRegister(): string { return self::$myInterface; }
	public static function _test_setInterface(string $int): void { self::$myInterface = $int; }
	public function _test_getLibrary(): array { return $this->library; }
}

final class ServiceRegistryKitTest extends TestCase {
	private array $basicServices;
	private array $configuredServices;
	private TestServiceRegistry $service;
	private ContainerInterface & MockObject $container;

	protected function setUp(): void {
		$this->container = $this->createMock(ContainerInterface::class);
		$this->service = new TestServiceRegistry($this->container);
	}

	protected function setUpBasic(): void {
		$this->basicServices = [
			new class() implements TestServiceRegisterable { public static function getKey(): string { return 'one'; } },
			new class() implements TestServiceRegisterable { public static function getKey(): string { return 'two'; } },
		];

		TestServiceRegistry::_test_setInterface(TestServiceRegisterable::class);
		$this->service->configure(array_map(fn($srv) => get_class($srv), $this->basicServices));
	}

	protected function setUpConfigured(): void {
		$this->configuredServices = [
			new class() implements TestServiceConfigurable {
				public static function getConfiguration(): RegisterableConfiguration {
					return new class() implements RegisterableConfiguration {
						public function getKey(): string { return 'one'; }
					};
				}
			},
			new class() implements TestServiceConfigurable {
				public static function getConfiguration(): RegisterableConfiguration {
					return new class() implements RegisterableConfiguration {
						public function getKey(): string { return 'two'; }
					};
				}
			},
		];

		TestServiceRegistry::_test_setInterface(TestServiceConfigurable::class);
		$this->service->configure(array_map(fn($srv) => get_class($srv), $this->configuredServices));
	}

	#[TestDox('::has will return true if the given key is present and the class is in the container')]
	function testHasWithKeyAndContainer() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);

		$this->assertTrue($this->service->has('one'));
		$this->assertTrue($this->service->has('two'));
	}

	#[TestDox('::has will return false if the given key is not present')]
	function testHasWithNoKey() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);

		$this->assertFalse($this->service->has(get_class($this->basicServices[0])));
	}

	#[TestDox('::has will return false if the given key is present but the class is not in the container')]
	function testHasWithNoContainer() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(false);

		$this->assertFalse($this->service->has('one'));
	}

	#[TestDox('::get will return an instance of the class if the given key is present and the class is in the container')]
	function testGetWithContainerAndKey() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);
		$this->container->method('get')->willReturn('ServiceOne_class_instance');

		$this->assertEquals('ServiceOne_class_instance', $this->service->getService('one'));
	}

	#[TestDox('::get will throw exception if the given key is not present')]
	function testGetWithNoKey() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);
		$this->container->method('get')->willReturn('ServiceOne_class_instance');

		$this->expectException(ServiceNotRegistered::class);
		$this->service->getService(get_class($this->basicServices[0]));
	}

	#[TestDox('::get will throw exception if the given key is present but the class is not in the container')]
	function testGetWithNoContainer() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(false);
		$this->container->expects($this->never())->method('get');

		$this->expectException(ServiceNotRegistered::class);
		$this->service->getService('one');
	}
}

