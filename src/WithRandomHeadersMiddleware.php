<?php

declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

use function array_keys;
use function count;
use function random_int;
use function React\Promise\resolve;

use const WyriHaximus\Constants\Numeric\ONE;
use const WyriHaximus\Constants\Numeric\TWO;
use const WyriHaximus\Constants\Numeric\ZERO;

final class WithRandomHeadersMiddleware
{
    /** @var Header[] */
    private array $headers;

    private int $minimum = TWO;

    private int $maximum = TWO;

    public function __construct(Header ...$headers)
    {
        $this->headers = $headers;
    }

    public function withMinimum(int $minimum): self
    {
        $clone          = clone $this;
        $clone->minimum = $minimum;
        $clone->enforceMinimumMaximum();

        return $clone;
    }

    public function withMaximum(int $maximum): self
    {
        $clone          = clone $this;
        $clone->maximum = $maximum;
        $clone->enforceMinimumMaximum();

        return $clone;
    }

    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        return resolve($next($request))->then(function (ResponseInterface $response): ResponseInterface {
            $count   = random_int($this->minimum, $this->maximum);
            $headers = $this->headers;
            for ($i = ZERO; $i < $count; $i++) {
                $randomizer = array_keys($headers);
                $header     = $randomizer[random_int(ZERO, count($headers) - ONE)];
                $response   = $response->withHeader($headers[$header]->name(), $headers[$header]->contents());
                unset($headers[$header]);
            }

            return $response;
        });
    }

    // phpcs:disable
    private function enforceMinimumMaximum(): void
    {
        $headersCount = count($this->headers);
        if ($this->minimum > $headersCount) {
            $this->minimum = $headersCount;
        }

        if ($this->maximum > $headersCount) {
            $this->maximum = $headersCount;
        }

        if ($this->maximum >= $this->minimum) {
            return;
        }

        $this->maximum = $this->minimum;
    }
}
