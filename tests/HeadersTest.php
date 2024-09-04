<?php

declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\React\Http\Middleware\Headers;

final class HeadersTest extends AsyncTestCase
{
    /**
     * @param iterable<string, string> $headers
     *
     * @test
     * @dataProvider provideHeaderIterables
     */
    public function fromIterable(iterable $headers): void
    {
        $headerObjects = [...Headers::fromIterable($headers)];

        self::assertCount(2, $headerObjects);

        self::assertSame('X-A', $headerObjects[0]->header);
        self::assertSame('a', $headerObjects[0]->contents);

        self::assertSame('X-B', $headerObjects[1]->header);
        self::assertSame('b', $headerObjects[1]->contents);
    }

    /** @return iterable<array<iterable<string, string>>> */
    public static function provideHeaderIterables(): iterable
    {
        $headers = [
            'X-A' => 'a',
            'X-B' => 'b',
        ];

        yield [$headers];

        yield [
            (static fn (): iterable => yield from $headers)(),
        ];
    }
}
