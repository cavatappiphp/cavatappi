<?php

namespace Cavatappi\Foundation\Factories;

use Cavatappi\Foundation\Utilities\HttpVerb;
use Cavatappi\Test\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class HttpMessageFactoryTest extends TestCase {
	public function testItCreatesPsr7RequestObjects() {
		$request = HttpMessageFactory::request(
			verb: HttpVerb::GET,
			url: 'https://smol.blog/hello',
			headers: ['Accept' => 'application/json']
		);

		$this->assertEquals('/hello', $request->getRequestTarget());
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('https://smol.blog/hello', $request->getUri());
		$this->assertEquals('1.1', $request->getProtocolVersion());
		$this->assertEquals(['smol.blog'], $request->getHeader('host'));
		$this->assertTrue($request->hasHeader('Host'));
		$this->assertEquals('smol.blog', $request->getHeaderLine('host'));
		$this->assertInstanceOf(StreamInterface::class, $request->getBody());
	}

	public function testRequestUsesAStringBodyVerbatim() {
		$request = HttpMessageFactory::request(verb: HttpVerb::GET, url: 'https://smol.blog/hello', body: 'one=two');

		$this->assertEquals('one=two', $request->getBody()->__toString());
	}

	public function testRequestFormatsAnArrayBodyIntoJson() {
		$body = ['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$request = HttpMessageFactory::request(verb: HttpVerb::GET, url: 'https://smol.blog/hello', body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $request->getBody()->__toString());
	}

	public function testRequestFormatsAnObjectBodyIntoJson() {
		$body = (object)['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$request = HttpMessageFactory::request(verb: HttpVerb::GET, url: 'https://smol.blog/hello', body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $request->getBody()->__toString());
	}
	public function testItCreatesPsr7ResponseObjects() {
		$response = HttpMessageFactory::response(code: 301, headers: ['Location' => 'https://smol.blog/']);

		$this->assertEquals(301, $response->getStatusCode());
		$this->assertEquals('Moved Permanently', $response->getReasonPhrase());
		$this->assertEquals('1.1', $response->getProtocolVersion());
		$this->assertEquals(['https://smol.blog/'], $response->getHeader('location'));
		$this->assertEquals('https://smol.blog/', $response->getHeaderLine('location'));
		$this->assertTrue($response->hasHeader('location'));
		$this->assertInstanceOf(StreamInterface::class, $response->getBody());
	}

	public function testItUsesAStringBodyVerbatim() {
		$response = HttpMessageFactory::response(body: 'one=two');

		$this->assertEquals('one=two', $response->getBody()->getContents());
	}

	public function testItFormatsAnArrayBodyIntoJson() {
		$body = ['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$response = HttpMessageFactory::response(body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $response->getBody()->getContents());
	}

	public function testItFormatsAnObjectBodyIntoJson() {
		$body = (object)['one' => 'two'];
		$bodyJson = '{"one":"two"}';

		$response = HttpMessageFactory::response(body: $body);

		$this->assertJsonStringEqualsJsonString($bodyJson, $response->getBody()->getContents());
	}

	public function testItCreatesAndAcceptsPsr7UriObjects() {
		$actual = HttpMessageFactory::uri('https://smol.blog/');
		$this->assertInstanceOf(UriInterface::class, $actual);

		$this->assertInstanceOf(RequestInterface::class, HttpMessageFactory::request(HttpVerb::GET, $actual));
	}

	public function testReplacement() {
		$mockExpected = new Psr17Factory()->createRequest('HEAD', 'https://eph.me/');
		$mock = $this->createMock(Psr17Factory::class);
		$mock->expects($this->once())->method('createRequest')->willReturn($mockExpected);

		HttpMessageFactory::setSource($mock);

		$mockActual = HttpMessageFactory::request(HttpVerb::OPTIONS, 'https://smol.blog/post/123');
		$this->assertEquals($mockExpected, $mockActual);

		HttpMessageFactory::setSource(null);

		$actual = HttpMessageFactory::request(HttpVerb::OPTIONS, 'https://smol.blog/post/123');
		$this->assertEquals('https://smol.blog/post/123', $actual->getUri()->__toString());
	}
}