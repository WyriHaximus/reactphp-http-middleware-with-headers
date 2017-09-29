<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\ServerRequest;
use RingCentral\Psr7\Response;
use WyriHaximus\React\Http\Middleware\WithRandomHeadersMiddleware;
use function Clue\React\Block\await;

final class WithRandomHeadersMiddlewareTest extends TestCase
{
    public function testWithRandomHeaders()
    {
        $headers = [
            'X-Hamsterred-By' => 'ReactPHP 7',
            'X-Foo' => 'Bar',
            'X-Bar' => 'Foo',
        ];
        $request = new ServerRequest('GET', 'https://example.com/');
        $middleware = new WithRandomHeadersMiddleware($headers);
        /** @var ServerRequestInterface $requestWithHeaders */
        $requestWithHeaders = await($middleware($request, function (ServerRequestInterface $request) {
            return new Response();
        }), Factory::create());

        $count = 0;

        foreach ($headers as $headerName => $headerValue) {
            if ($requestWithHeaders->hasHeader($headerName)) {
                $count++;
            }
        }

        self::assertSame(2, $count);
    }
}
