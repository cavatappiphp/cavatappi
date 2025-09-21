<?php

namespace Cavatappi\Foundation\Exceptions;

use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Cavatappi\Test\TestCase;
use Throwable;

final class CommandNotAuthorizedTest extends TestCase {
	public function testItRequiresACommand() {
		$cmd = new class() implements Command { use ValueKit; };
		$actual = new CommandNotAuthorized(originalCommand: $cmd);
		$this->assertInstanceOf(Throwable::class, $actual);
		$this->assertEquals($cmd, $actual->originalCommand);
	}
}
