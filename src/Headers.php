<?php

declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

final readonly class Headers
{
    /**
     * @param iterable<string, string> $headers
     *
     * @return iterable<Header>
     */
    public static function fromIterable(iterable $headers): iterable
    {
        foreach ($headers as $header => $contents) {
            yield new Header($header, $contents);
        }
    }
}
