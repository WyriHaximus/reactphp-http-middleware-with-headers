<?php

declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

final class WithHeadersMiddleware
{
    /** @var Header[] */
    private array $headers;

    public function __construct(Header ...$headers)
    {
        $this->headers = $headers;
    }

    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        return resolve($next($request))->then(function (ResponseInterface $response): ResponseInterface {
            foreach ($this->headers as $header) {
                $response = $response->withHeader($header->name(), $header->contents());
            }

            return $response;
        });
    }
}
