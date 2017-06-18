# CSRF\TokenService [![Build Status](https://travis-ci.org/schnittstabil/csrf-tokenservice.svg?branch=master)](https://travis-ci.org/schnittstabil/csrf-tokenservice) [![Coverage Status](https://coveralls.io/repos/github/schnittstabil/csrf-tokenservice/badge.svg?branch=master)](https://coveralls.io/github/schnittstabil/csrf-tokenservice?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/schnittstabil/csrf-tokenservice/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/schnittstabil/csrf-tokenservice/?branch=master) [![Code Climate](https://codeclimate.com/github/schnittstabil/csrf-tokenservice/badges/gpa.svg)](https://codeclimate.com/github/schnittstabil/csrf-tokenservice)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d03769f0-d78c-49cf-b1a5-7baf8993ff81/big.png)](https://insight.sensiolabs.com/projects/d03769f0-d78c-49cf-b1a5-7baf8993ff81)

> Stateless CSRF (Cross-Site Request Forgery) token service :meat_on_bone:


## Install

```sh
$ composer require schnittstabil/csrf-tokenservice
```


## Usage

```php
<?php
require __DIR__.'/vendor/autoload.php';

use Schnittstabil\Csrf\TokenService\TokenService;

// Shared secret key used for generating and validating token signatures:
$key = 'This key is not so secret - change it!';

// Time to Live in seconds; default is 1440 seconds === 24 minutes:
$ttl = 1440;

// create the TokenService
$tokenService = new TokenService($key, $ttl);

// generate a URL-safe token, using the name of the authenticated user as nonce:
$token = $tokenService->generate($_SERVER['PHP_AUTH_USER']);

// validate the token - stateless; no session needed
if (!$tokenService->validate($_SERVER['PHP_AUTH_USER'], $token)) {
    http_response_code(403);
    echo '<h2>403 Access Forbidden, bad CSRF token</h2>';
    exit();
}
```


## Related

* [schnittstabil/psr7-csrf-middleware](https://github.com/schnittstabil/psr7-csrf-middleware) – (stateless) PSR-7 CSRF protection middleware
* [schnittstabil/csrf-twig-helpers](https://github.com/schnittstabil/csrf-twig-helpers) – Twig helpers for token rendering


## License

MIT © [Michael Mayer](http://schnittstabil.de)
