<?php

declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

use function count;
use function random_int;
use function React\Promise\resolve;
use function Safe\shuffle;

final class WithRandomHeadersMiddleware
{
    private const DEFAULT_MIN_MAX = 2;

    /** @var array<Header> */
    private array $headers;

    private int $minimum = self::DEFAULT_MIN_MAX;

    private int $maximum = self::DEFAULT_MIN_MAX;

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

    /** @return PromiseInterface<ResponseInterface> */
    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        return resolve($next($request))->then(function (ResponseInterface $response): ResponseInterface {
            $count   = random_int($this->minimum, $this->maximum);
            $headers = $this->headers;
            shuffle($headers);
            $i = 0;
            do {
                /**
                 * @psalm-suppress MixedPropertyFetch
                 * @psalm-suppress MixedArgument
                 */
                $response = $response->withHeader($headers[$i]->header, $headers[$i]->contents);
            } while (++$i < $count);

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
