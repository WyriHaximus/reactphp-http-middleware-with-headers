# Add headers to responses

[![Build Status](https://travis-ci.org/WyriHaximus/reactphp-http-middleware-with-headers.svg?branch=master)](https://travis-ci.org/WyriHaximus/reactphp-http-middleware-with-headers)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-http-middleware-with-headers/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-http-middleware-with-headers)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-http-middleware-with-headers/downloads.png)](https://packagist.org/packages/WyriHaximus/react-http-middleware-with-headers)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-middleware-with-headers/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-middleware-with-headers/?branch=master)
[![License](https://poser.pugx.org/WyriHaximus/react-http-middleware-with-headers/license.png)](https://packagist.org/packages/WyriHaximus/react-http-middleware-with-headers)

# Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/react-http-middleware-with-headers
```

This middleware adds all the headers passed into the constructor to the responses flowing through this middleware.

# Usage

```php
$server = new \React\Http\HttpServer([
    /** Other middleware */
    new WithHeadersMiddleware(
        'X-Powered-By' => 'wyrihaximus.net (11.0.33)',
    ),
    new WithRandomHeadersMiddleware(
        1, // Minimum header count to attach
        2,  // Maximum header count to attach
        new Header('X-nanananana', 'Batcache'),
        new Header('X-Horde', 'For the Horde!'),
        new Header('X-Picard', 'Make it so'),
    ),
    /** Other middleware */
]);
```

Combined with [`wyrihaximus-net/x-headers`](https://github.com/WyriHaximusNet/php-x-headers) you'll get an ever-growing
set of Nerdy headers:

```php
$server = new \React\Http\HttpServer([
    /** Other middleware */
    new WithRandomHeadersMiddleware(
        1,
        ceil(count(Headers::HEADERS) / 4), // Add up to 25% of the list to it
        ...(static function (array $headers): iterable {
            foreach ($headers as $key => $value) {
                yield new Header($key, $value);
            }
        })(Headers::HEADERS),
    ),
    /** Other middleware */
]);
```

# License

The MIT License (MIT)

Copyright (c) 2024 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
