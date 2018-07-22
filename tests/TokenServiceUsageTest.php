<?php

namespace Schnittstabil\Csrf\TokenService;

use PHPUnit\Framework\TestCase;

/**
 * TokenService Usage Tests.
 */
class TokenServiceUsageTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testUsage()
    {
        http_response_code(200);
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['PHP_AUTH_USER'] = 'unicorn';
        ob_start();
        // --- usage start ---
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
            // exit();
        }
        // --- usage end ---
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('', (string) $output);
        $this->assertSame(200, http_response_code());
    }
}
