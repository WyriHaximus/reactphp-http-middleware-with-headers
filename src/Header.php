<?php

declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

final class Header
{
    private string $header;
    private string $contents;

    public function __construct(string $header, string $contents)
    {
        $this->header   = $header;
        $this->contents = $contents;
    }

    public function name(): string
    {
        return $this->header;
    }

    public function contents(): string
    {
        return $this->contents;
    }
}
