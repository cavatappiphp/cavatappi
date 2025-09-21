<?php

namespace Cavatappi\Foundation\Utilities;

use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Job\AsyncDispatchJob;
use Cavatappi\Test\TestCase;

final class StringUtilsTest extends TestCase {
	public function testDequalifyClassName() {
		$this->assertEquals('StringUtilsTest', StringUtils::dequalifyClassName(self::class));
		$this->assertEquals('AsyncDispatchJob', StringUtils::dequalifyClassName(AsyncDispatchJob::class));
		$this->assertEquals('CommandHandlerService', StringUtils::dequalifyClassName(CommandHandlerService::class));
		$this->assertEquals('SomeOtherClass', StringUtils::dequalifyClassName('\\Some\\Other\\Namespace\\SomeOtherClass'));
		$this->assertEquals('NonNamespacedClass', StringUtils::dequalifyClassName('NonNamespacedClass'));
	}

	public function testCamelToTitle() {
		$this->assertEquals('Some Exceptionally Long Name', StringUtils::camelToTitle('someExceptionallyLongName'));
		$this->assertEquals('Short One', StringUtils::camelToTitle('shortOne'));
		$this->assertEquals('Name', StringUtils::camelToTitle('name'));
		$this->assertEquals('String Utils Test', StringUtils::camelToTitle(StringUtils::dequalifyClassName(self::class)));
	}
}