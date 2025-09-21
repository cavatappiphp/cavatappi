<?php

namespace Cavatappi\Foundation\Factories;

use Cavatappi\Foundation\Utilities\HttpVerb;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Http\Message\UriFactoryInterface as UriFactory;
use Psr\Http\Message\UriInterface;

/**
 * Static factory for creating PSR-7 HTTP messages.
 *
 * Default implementation is Nyholm\Psr7.
 */
class HttpMessageFactory {
	/**
	 * Internal Nyholm PSR-17 factory.
	 *
	 * PSR-17 has different interfaces for each thing. Nyholm puts them all in one. And that's what we're doing.
	 *
	 * @var RequestFactory & ResponseFactory & StreamFactory & UriFactory
	 */
	private static null | (RequestFactory & ResponseFactory & StreamFactory & UriFactory) $internalFactory;

	/**
	 * Get the internal PSR-17 factory.
	 *
	 * @return RequestFactory&ResponseFactory&StreamFactory&UriFactory
	 */
	private static function factory(): RequestFactory & ResponseFactory & StreamFactory & UriFactory {
		self::$internalFactory ??= new Psr17Factory();
		return self::$internalFactory;
	}

	/**
	 * Replace the instance of the factory.
	 *
	 * @param null|(RequestFactory&ResponseFactory&StreamFactory&UriFactory) $newSource A new factory to use.
	 * @return void
	 */
	public static function setSource(
		null | (RequestFactory & ResponseFactory & StreamFactory & UriFactory) $newSource
	) {
		self::$internalFactory = $newSource;
	}

	/**
	 * Create a PSR-7 HTTP Request.
	 *
	 * @param  HttpVerb            $verb    HTTP method to use.
	 * @param  string|UriInterface $url     URI to retrieve.
	 * @param  array               $headers Any headers to add to the request.
	 * @param  mixed               $body    Body of the request. Arrays and objects will be serialized to JSON.
	 * @return RequestInterface
	 */
	public static function request(
		HttpVerb $verb,
		string|UriInterface $url,
		array $headers = [],
		mixed $body = null,
	): RequestInterface {
		$newRequest = self::factory()->createRequest($verb->value, $url);

		$newRequest = self::addBody($newRequest, $body);

		foreach ($headers as $key => $value) {
			$newRequest = $newRequest->withHeader($key, $value);
		}

		return $newRequest;
	}

	/**
	 * Create a PSR-7 HTTP Response.
	 *
	 * @param  integer $code    HTTP code for the response. Default 200 (OK).
	 * @param  array   $headers Headers of the response.
	 * @param  mixed   $body    Response body in string or object format.
	 * @return ResponseInterface
	 */
	public static function response(
		int $code = 200,
		array $headers = [],
		mixed $body = null,
	): ResponseInterface {
		$newResponse = self::factory()->createResponse($code);

		$newResponse = self::addBody($newResponse, $body);

		foreach ($headers as $key => $value) {
			$newResponse = $newResponse->withHeader($key, $value);
		}

		return $newResponse;
	}

	/**
	 * Create a PSR-7 URI object.
	 *
	 * @param  string $uri URI to create.
	 * @return UriInterface
	 */
	public static function uri(string $uri): UriInterface {
		return self::factory()->createUri($uri);
	}

	/**
	 * Add the body to the given message.
	 *
	 * @template M
	 * @param    M     $message
	 * @param    mixed $body
	 * @return   M
	 */
	private static function addBody(
		MessageInterface $message,
		mixed $body
	): MessageInterface {
		if (empty($body)) {
			return $message;
		}

		$parsedBody = \is_string($body) ? $body : (\json_encode($body) ?: '');

		return $message->withBody(self::factory()->createStream($parsedBody));
	}
}
