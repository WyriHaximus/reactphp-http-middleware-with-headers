<?php

declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use WyriHaximus\React\Http\Middleware\Header;
use WyriHaximus\React\Http\Middleware\WithHeadersMiddleware;

use function Clue\React\Block\await;

final class WithHeadersMiddlewareTest extends TestCase
{
    public function testWithHeaders(): void
    {
        $headers            = [
            new Header('X-Powered-By', 'ReactPHP 7'),
            new Header('X-Foo', 'Bar'),
        ];
        $request            = new ServerRequest('GET', 'https://example.com/');
        $middleware         = new WithHeadersMiddleware(...$headers);
        $requestWithHeaders = await($middleware($request, static fn (ServerRequestInterface $request): ResponseInterface => new Response()), Factory::create());

        self::assertTrue($requestWithHeaders->hasHeader('X-Powered-By'));
        self::assertSame('ReactPHP 7', $requestWithHeaders->getHeaderLine('X-Powered-By'));
        self::assertTrue($requestWithHeaders->hasHeader('X-Foo'));
        self::assertSame('Bar', $requestWithHeaders->getHeaderLine('X-Foo'));
    }
}
