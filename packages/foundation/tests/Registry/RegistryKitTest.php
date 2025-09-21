<?php

namespace Cavatappi\Foundation\Registry;

use PHPUnit\Framework\Attributes\TestDox;
use Cavatappi\Test\TestCase;
use Cavatappi\Foundation\Exceptions\CodePathNotSupported;

interface TestRegisterable extends Registerable {}
interface TestConfigurable extends ConfiguredRegisterable {}

class TestRegistry implements Registry {
	use RegistryKit;
	private static string $myInterface = TestRegisterable::class;
	public static function getInterfaceToRegister(): string { return self::$myInterface; }
	public static function _test_setInterface(string $int): void { self::$myInterface = $int; }
	public function _test_getLibrary(): array { return $this->library; }
}

final class RegistryKitTest extends TestCase {
	private array $basicServices;
	private array $configuredServices;
	private TestRegistry $service;

	protected function setUp(): void {
		$this->service = new TestRegistry();
	}

	protected function setUpBasic(): void {
		$this->basicServices = [
			new class() implements TestRegisterable { public static function getKey(): string { return 'one'; } },
			new class() implements TestRegisterable { public static function getKey(): string { return 'two'; } },
		];

		TestRegistry::_test_setInterface(TestRegisterable::class);
		$this->service->configure(array_map(fn($srv) => get_class($srv), $this->basicServices));
	}

	protected function setUpConfigured(): void {
		$this->configuredServices = [
			new class() implements TestConfigurable {
				public static function getConfiguration(): RegisterableConfiguration {
					return new class() implements RegisterableConfiguration {
						public string $key { get => 'one'; }
					};
				}
			},
			new class() implements TestConfigurable {
				public static function getConfiguration(): RegisterableConfiguration {
					return new class() implements RegisterableConfiguration {
						public string $key { get => 'two'; }
					};
				}
			},
		];

		TestRegistry::_test_setInterface(TestConfigurable::class);
		$this->service->configure(array_map(fn($srv) => get_class($srv), $this->configuredServices));
	}

	#[TestDox('::configure will configure the Registry for a Registerable interface')]
	function testConfigure() {
		$this->setUpBasic();

		$this->assertEquals(
			array_combine(
				keys: ['one', 'two'],
				values: array_map(fn($srv) => get_class($srv), $this->basicServices),
			),
			$this->service->_test_getLibrary()
		);
	}

	#[TestDox('::configure will configure the Registry for a ConfiguredRegisterable interface')]
	function testConfigureWithObjects() {
		$this->setUpConfigured();

		$this->assertEquals(
			array_combine(
				keys: ['one', 'two'],
				values: array_map(fn($srv) => get_class($srv), $this->configuredServices),
			),
			$this->service->_test_getLibrary()
		);
	}

	#[TestDox('::configure will throw an exception if the interface is not Registerable')]
	function testConfigureWithBadInterface() {
		$this->expectException(CodePathNotSupported::class);

		TestRegistry::_test_setInterface(Registry::class);
		$this->service->configure(['one' => self::class]);
	}

	#[TestDox('::getConfig will return the class config if the given key is present')]
	function testGetConfigWithKey() {
		$this->setUpConfigured();

		$config = $this->service->getConfig('one');
		$this->assertInstanceOf(RegisterableConfiguration::class, $config);
		$this->assertEquals('one', $config?->key);
	}

	#[TestDox('::getConfig will return null if the given key is not present')]
	function testGetConfigWithNoKey() {
		$this->setUpConfigured();

		$actual = $this->service->getConfig('three');
		$this->assertNull($actual);
	}
}

