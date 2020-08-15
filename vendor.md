# Guzzle, PHP HTTP client

[![Latest Version](https://img.shields.io/github/release/guzzle/guzzle.svg?style=flat-square)](https://github.com/guzzle/guzzle/releases) [![Build Status](https://img.shields.io/travis/guzzle/guzzle.svg?style=flat-square)](https://travis-ci.org/guzzle/guzzle) [![Total Downloads](https://img.shields.io/packagist/dt/guzzlehttp/guzzle.svg?style=flat-square)](https://packagist.org/packages/guzzlehttp/guzzle)

Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and trivial to integrate with web services.

* Simple interface for building query strings, POST requests, streaming large

  uploads, streaming large downloads, using HTTP cookies, uploading JSON data,

  etc...

* Can send both synchronous and asynchronous requests using the same interface.
* Uses PSR-7 interfaces for requests, responses, and streams. This allows you

  to utilize other PSR-7 compatible libraries with Guzzle.

* Abstracts away the underlying HTTP transport, allowing you to write

  environment and transport agnostic code; i.e., no hard dependency on cURL,

  PHP streams, sockets, or non-blocking event loops.

* Middleware system allows you to augment and compose client behavior.

```php
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://api.github.com/repos/guzzle/guzzle');

echo $response->getStatusCode(); # 200
echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'

# Send an asynchronous request.
$request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
$promise = $client->sendAsync($request)->then(function ($response) {
    echo 'I completed! ' . $response->getBody();
});

$promise->wait();
```

## Help and docs

* [Documentation](http://guzzlephp.org/)
* [Stack Overflow](http://stackoverflow.com/questions/tagged/guzzle)
* [Gitter](https://gitter.im/guzzle/guzzle)

## Installing Guzzle

The recommended way to install Guzzle is through [Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of Guzzle:

```bash
composer require guzzlehttp/guzzle
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update Guzzle using composer:

```bash
composer update
```

## Version Guidance

| Version | Status | Packagist | Namespace | Repo | Docs | PSR-7 | PHP Version |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 3.x | EOL | `guzzle/guzzle` | `Guzzle` | [v3](https://github.com/guzzle/guzzle3) | [v3](http://guzzle3.readthedocs.org) | No | &gt;= 5.3.3 |
| 4.x | EOL | `guzzlehttp/guzzle` | `GuzzleHttp` | [v4](https://github.com/guzzle/guzzle/tree/4.x) | N/A | No | &gt;= 5.4 |
| 5.x | EOL | `guzzlehttp/guzzle` | `GuzzleHttp` | [v5](https://github.com/guzzle/guzzle/tree/5.3) | [v5](http://guzzle.readthedocs.org/en/5.3/) | No | &gt;= 5.4 |
| 6.x | Latest | `guzzlehttp/guzzle` | `GuzzleHttp` | [v6](https://github.com/guzzle/guzzle) | [v6](http://guzzle.readthedocs.org/en/latest/) | Yes | &gt;= 5.5 |

