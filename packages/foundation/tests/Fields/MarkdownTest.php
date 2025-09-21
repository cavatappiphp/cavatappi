<?php

namespace Cavatappi\Foundation\Fields;

use PHPUnit\Framework\Attributes\TestDox;
use Cavatappi\Test\TestCase;

final class MarkdownTest extends TestCase {
	#[TestDox('It stores a markdown string (which is any string really)')]
	public function testRandom() {
		$this->assertInstanceOf(Markdown::class, new Markdown('My email is <snek@smol.blog>.'));
	}

	#[TestDox('It will serialize to and deserialize from a string. Because it is one.')]
	public function testSerialization() {
		$markdownString = 'All your base are belong to us.';
		$markdownObject = new Markdown('All your base are belong to us.');

		$this->assertEquals($markdownString, $markdownObject->__toString());
		$this->assertEquals(strval($markdownObject), strval(Markdown::fromString($markdownString)));
	}
}
