<?php declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function React\Promise\resolve;

final class WithRandomHeadersMiddleware
{
    private $headers = [];

    private $minimum = 2;

    private $maximum = 2;

    /**
     * @param array $headers
     */
    public function __construct(array $headers, int $minimum = 2, int $maximum = 2)
    {
        $this->headers = $headers;
        $this->minimum = $minimum;
        $this->maximum = $maximum;

        $headersCount = count($headers);
        if ($this->minimum > $headersCount) {
            $this->minimum = $headersCount;
        }
        if ($this->maximum > $headersCount) {
            $this->maximum = $headersCount;
        }
        if ($this->maximum < $this->minimum) {
            $this->maximum = $this->minimum;
        }
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return resolve($next($request))->then(function (ResponseInterface $response) {
            $count = random_int($this->minimum, $this->maximum);
            $headers = $this->headers;
            for ($i = 0; $i < $count; $i++) {
                $randomizer = array_keys($headers);
                $header = $randomizer[random_int(0, count($headers) - 1)];
                $response = $response->withHeader($header, $headers[$header]);
                unset($headers[$header]);
            }

            return resolve($response);
        });
    }
}
