<?php

namespace Cavatappi\Infrastructure;

use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\CommandBus;
use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\ModuleKit;
use Cavatappi\Foundation\Registry\RegistryUtils;
use Cavatappi\Foundation\Value\ValueKit;
use Crell\Tukio\Dispatcher;
use Crell\Tukio\OrderedListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Cavatappi\Infrastructure\Registries\CommandHandlerRegistry;
use Cavatappi\Infrastructure\Registries\ServiceRegistry;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use Cavatappi\Test\TestCase;

final class AppKitTestSampleImplementation {
	use AppKit {
		buildDiscoveredClassList as public;
		buildContainerFromModels as public;
		buildDependencyMap as public;
	}
}

readonly class AppKitTestExampleCommand implements Command {
	use ValueKit;
	public function __construct(public string $name) {}
}

final class AppKitTestExampleCommandHandler implements CommandHandlerService {
	#[CommandHandler]
	public function handleExampleCommand(AppKitTestExampleCommand $cmd) {
		return "The command {$cmd->name} has been handled.";
	}
}

final class AppKitTest extends TestCase {
	private AppKitTestSampleImplementation $testApp;

	protected function setUp(): void {
		$this->testApp = new AppKitTestSampleImplementation();
	}

	public function testDependencyMap() {
		$testModel = new class() implements Module {
			use ModuleKit;
			private static function listClasses(): array { return []; }
			private static function serviceMapOverrides(): array {
				return [
					OrderedListenerProvider::class => ['container' => ContainerInterface::class],
					ListenerProviderInterface::class => OrderedListenerProvider::class,
				];
			}
		};

		$models = [
			DefaultModule::class,
			get_class($testModel),
		];

		$expected = [
			Registries\CommandHandlerRegistry::class => ['container' => ContainerInterface::class],
			Registries\EventListenerRegistry::class => ['container' => ContainerInterface::class],
			LoggerInterface::class => NullLogger::class,
			EventDispatcherInterface::class => Dispatcher::class,
			CommandBus::class => Registries\CommandHandlerRegistry::class,
			NullLogger::class => [],
			Dispatcher::class => [
				ListenerProviderInterface::class,
				LoggerInterface::class,
			],
			OrderedListenerProvider::class => ['container' => ContainerInterface::class],
			ListenerProviderInterface::class => OrderedListenerProvider::class,
			ServiceRegistry::class => [],
			SerializationService::class => [],
		];

		$this->assertEquals($expected, $this->testApp->buildDependencyMap($models));
	}

	public function testContainerSetup() {
		$testModel = new class() implements Module {
			use ModuleKit;
			private static function listClasses(): array { return [AppKitTestExampleCommandHandler::class]; }
			private static function serviceMapOverrides(): array { return []; }
		};

		$testMap = $this->testApp->buildDiscoveredClassList([
			DefaultModule::class,
			get_class($testModel),
		]);
		$testConfigs = RegistryUtils::makeRegistryConfigs($testMap);

		$this->assertEquals([AppKitTestExampleCommandHandler::class], $testConfigs[CommandHandlerRegistry::class]);

		$container = $this->testApp->buildContainerFromModels([
			DefaultModule::class,
			get_class($testModel),
		]);
		$id = $this->randomId()->toString();
		$command = new AppKitTestExampleCommand(name: $id);
		$expected = "The command $id has been handled.";

		$this->assertEquals($expected, $container->get(CommandBus::class)->execute($command));
	}
}
