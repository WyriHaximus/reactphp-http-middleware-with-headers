<?php

declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use ReflectionProperty;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\React\Http\Middleware\Header;
use WyriHaximus\React\Http\Middleware\WithRandomHeadersMiddleware;

use function array_key_exists;
use function array_map;
use function array_values;
use function count;
use function React\Async\await;
use function strlen;
use function strtoupper;

final class WithRandomHeadersMiddlewareTest extends AsyncTestCase
{
    /** @test */
    public function withRandomHeaders(): void
    {
        $headers = [];
        for ($char = 'a'; strlen($char) === 1; $char++) {
            $headers['X-' . strtoupper($char)] = new Header('X-' . strtoupper($char), $char);
        }

        $middleware         = (new WithRandomHeadersMiddleware(...$headers))->withMinimum(count($headers));
        $request            = new ServerRequest('GET', 'https://example.com/');
        $requestWithHeaders = await($middleware($request, static fn (ServerRequestInterface $request): ResponseInterface => new Response()));

        $requestHeaders = [];
        foreach ($requestWithHeaders->getHeaders() as $headerName => $value) {
            if (! array_key_exists($headerName, $headers)) {
                continue;
            }

            $requestHeaders[] = $headerName;
        }

        self::assertCount(count($headers), $requestHeaders);
        self::assertNotSame(
            [
                ...array_map(
                    static fn (Header $header): string => $header->header,
                    array_values($headers),
                ),
            ],
            $requestHeaders,
        );
    }

    /** @test */
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
     * @dataProvider minMaxMAthDataProvider
     */
    public function minMaxMath(int $min, int $max, int $expectedMin, int $expectedMax): void
    {
        $a = new WithRandomHeadersMiddleware(new Header('A', 'a'), new Header('Foo', 'bar'), new Header('Foo', 'bar'), new Header('Foo', 'bar'), new Header('Foo', 'bar'));
        $b = $a->withMinimum($min);

        self::assertSame($expectedMin, self::getPropertyValue($b, 'minimum'));

        $middleware = $b->withMaximum($max);
        self::assertNotSame($a, $b);
        self::assertNotSame($a, $middleware);
        self::assertNotSame($b, $middleware);

        self::assertSame($expectedMin, self::getPropertyValue($middleware, 'minimum'));
        self::assertSame($expectedMax, self::getPropertyValue($middleware, 'maximum'));
    }

    /** @return iterable<array<int>> */
    public static function minMaxMAthDataProvider(): iterable
    {
        yield 'Same' => [1, 1, 1, 1];
        yield 'Max higher than Min' => [1, 2, 1, 2];
        yield 'Same but with 2 instead of 1 ' => [2, 2, 2, 2];
        yield 'Min higher than Max so they are both pulled to Max' => [2, 1, 2, 2];
        yield 'Min and Max can\'t be higher than the 5 headers we put into it, but with Max lower than Min' => [13, 1, 5, 5];
        yield 'Min and Max can\'t be higher than the 5 headers we put into it' => [6, 13, 5, 5];
    }

    private static function getPropertyValue(WithRandomHeadersMiddleware $middleware, string $propertyName): int
    {
        $property = (new ReflectionProperty(WithRandomHeadersMiddleware::class, $propertyName));
        $property->setAccessible(true);

        /** @phpstan-ignore-next-line */
        return $property->getValue($middleware);
    }
}
