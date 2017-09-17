<?php

namespace WyriHaximus\React\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function React\Promise\resolve;

final class WithHeadersMiddleware
{
    private $headers = [];

    /**
     * @param array $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return resolve($next($request))->then(function (ResponseInterface $response) {
            foreach ($this->headers as $header => $value) {
                $response = $response->withHeader($header, $value);
            }

            return resolve($response);
        });
    }
}