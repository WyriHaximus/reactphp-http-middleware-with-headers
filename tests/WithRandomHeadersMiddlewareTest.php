<?php

declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\React\Http\Middleware\Header;
use WyriHaximus\React\Http\Middleware\WithRandomHeadersMiddleware;

final class WithRandomHeadersMiddlewareTest extends AsyncTestCase
{
    public function testWithRandomHeaders(): void
    {
        $headers            = [
            new Header('X-Hamsterred-By', 'ReactPHP 7'),
            new Header('X-Foo', 'Bar'),
            new Header('X-Bar', 'Foo'),
        ];
        $middleware         = new WithRandomHeadersMiddleware(...$headers);
        $request            = new ServerRequest('GET', 'https://example.com/');
        $requestWithHeaders = $this->await($middleware($request, static fn (ServerRequestInterface $request): ResponseInterface => new Response()), 1);

        $count = 0;

        foreach ($headers as $header) {
            if (! $requestWithHeaders->hasHeader($header->name())) {
                continue;
            }

            $count++;
        }

        self::assertSame(2, $count);
    }

    /**
     * @test
     */
    public function immutability(): void
    {
        $a          = new WithRandomHeadersMiddleware(new Header('Foo', 'bar'), new Header('Foo', 'bar'), new Header('Foo', 'bar'));
        $b          = $a->withMinimum(5);
        $middleware = $b->withMaximum(1);
        self::assertNotSame($a, $b);
        self::assertNotSame($a, $middleware);
        self::assertNotSame($b, $middleware);
    }

    /**
     * @test
     */
    public function minMaxMath(): void
    {
        $a          = new WithRandomHeadersMiddleware(new Header('Foo', 'bar'), new Header('Foo', 'bar'), new Header('Foo', 'bar'), new Header('Foo', 'bar'), new Header('Foo', 'bar'));
        $b          = $a->withMinimum(5);
        $request    = new ServerRequest('GET', 'https://example.com/');
        $middleware = $b->withMaximum(1);
        self::assertNotSame($a, $b);
        self::assertNotSame($a, $middleware);
        self::assertNotSame($b, $middleware);
    }
}
