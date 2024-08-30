<?php

declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

final readonly class Header
{
    public function __construct(
        public string $header,
        public string $contents,
    ) {
    }
}
