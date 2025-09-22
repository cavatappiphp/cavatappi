<?php

namespace Cavatappi\Foundation\Module;

use Cavatappi\Foundation\Command\CommandBus;
use Cavatappi\Foundation\Exceptions\CodePathNotSupported;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Test\DiscoveryTestFixture;
use Cavatappi\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Psr\EventDispatcher\EventDispatcherInterface;

interface ModuleUtilsTestInterfaceOne {}
interface ModuleUtilsTestInterfaceTwo {}
interface ModuleUtilsTestInterfaceOneMarkTwo extends ModuleUtilsTestInterfaceOne {}
interface ModuleUtilsTestInterfaceOneMarkThree extends ModuleUtilsTestInterfaceOneMarkTwo {}

final class ModuleUtilsTestClassZero {}
final class ModuleUtilsTestClassOne implements ModuleUtilsTestInterfaceOne {}
final class ModuleUtilsTestClassTwo implements ModuleUtilsTestInterfaceTwo {}
class ModuleUtilsTestClassThree implements ModuleUtilsTestInterfaceOneMarkTwo {}
final class ModuleUtilsTestClassFour implements ModuleUtilsTestInterfaceOneMarkThree {}
class ModuleUtilsTestClassThreeMarkTwo extends ModuleUtilsTestClassThree {}
final class ModuleUtilsTestClassThreeMarkThree extends ModuleUtilsTestClassThreeMarkTwo implements ModuleUtilsTestInterfaceOneMarkThree {}

final class ModuleUtilsTestNoConstructor {}
final class ModuleUtilsTestNoDependencies { public function __construct() {} }
final class ModuleUtilsTestSomeDependencies {
	public function __construct(private CommandBus $bus, private EventDispatcherInterface $event) {}
}

final class ModuleUtilsTestNoType { public function __construct(private $whatAmI) {} }
final class ModuleUtilsTestUnionType { public function __construct(private CommandBus|Registry $thing) {} }
final class ModuleUtilsTestIntersectionType { public function __construct(private CommandBus & Registry $also) {} }
final class ModuleUtilsTestBuiltInType { public function __construct(private string $noGood) {} }

final class ModuleUtilsTest extends TestCase {
	public function testItWillListInterfacesImplementedByTheGivenClasses() {
		$expected = [
			ModuleUtilsTestClassZero::class => [],
			ModuleUtilsTestClassOne::class => [
				ModuleUtilsTestInterfaceOne::class,
			],
			ModuleUtilsTestClassTwo::class => [
				ModuleUtilsTestInterfaceTwo::class,
			],
			ModuleUtilsTestClassThree::class => [
				ModuleUtilsTestInterfaceOne::class,
				ModuleUtilsTestInterfaceOneMarkTwo::class,
			],
			ModuleUtilsTestClassFour::class => [
				ModuleUtilsTestInterfaceOne::class,
				ModuleUtilsTestInterfaceOneMarkThree::class,
				ModuleUtilsTestInterfaceOneMarkTwo::class,
			],
			ModuleUtilsTestClassThreeMarkTwo::class => [
				ModuleUtilsTestInterfaceOne::class,
				ModuleUtilsTestInterfaceOneMarkTwo::class,
			],
			ModuleUtilsTestClassThreeMarkThree::class => [
				ModuleUtilsTestInterfaceOne::class,
				ModuleUtilsTestInterfaceOneMarkThree::class,
				ModuleUtilsTestInterfaceOneMarkTwo::class,
			],
		];
		$actual = ModuleUtils::analyzeClasses([
			ModuleUtilsTestClassZero::class,
			ModuleUtilsTestClassOne::class,
			ModuleUtilsTestClassTwo::class,
			ModuleUtilsTestClassThree::class,
			ModuleUtilsTestClassFour::class,
			ModuleUtilsTestClassThreeMarkTwo::class,
			ModuleUtilsTestClassThreeMarkThree::class,
		]);
		// Sort the output so that the test is consistent.
		array_walk($actual, fn(&$arr) => sort($arr));

		$this->assertEquals($expected, $actual);
	}

	public static function badServices(): array {
		return [
			'no' => [ModuleUtilsTestNoType::class],
			'a union' => [ModuleUtilsTestUnionType::class],
			'an intersection' => [ModuleUtilsTestIntersectionType::class],
			'a built-in' => [ModuleUtilsTestBuiltInType::class],
		];
	}

	#[DataProvider('badServices')]
	#[TestDox('It will not analyze dependencies if a dependency has $_dataName type.')]
	public function testBadServices(string $model) {
		$this->expectException(CodePathNotSupported::class);
		ModuleUtils::reflectService($model);
	}

	public static function goodServices(): array {
		return [
			'no constructor' => [ModuleUtilsTestNoConstructor::class, []],
			'no dependencies' => [ModuleUtilsTestNoDependencies::class, []],
			'dependencies listed in constructor' => [ModuleUtilsTestSomeDependencies::class,
				[
					'bus' => CommandBus::class,
					'event' => EventDispatcherInterface::class,
				],
			],
		];
	}

	#[DataProvider('goodServices')]
	#[TestDox('It will analyze dependencies for a class with $_dataName.')]
	public function testGoodServices(string $model, array $expected) {
		$this->assertEquals($expected, ModuleUtils::reflectService($model));
	}

	public function testItWillDiscoverClassesInTheGivenAutoloadedFolder() {
		$expected = DiscoveryTestFixture\Model::EXPECTED_CLASSES;

		$this->assertEquals($expected, ModuleUtils::getClassNamesFromFolder(DiscoveryTestFixture\Model::FOLDER));
	}

	public function testItWillNotDiscoverClassesThatAreNotLoaded() {
		$this->assertNotContains(
			'Smolblog\Foundation\Test\NotAutoloadedFixture\NonAutoloadedClass',
			ModuleUtils::getClassNamesFromFolder(__DIR__),
		);
	}
}