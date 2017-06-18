<?php

namespace Schnittstabil\Csrf\TokenService;

/**
 * The TokenServiceInterface.
 */
interface TokenServiceInterface
{
    /**
     * Generate a CSRF token.
     *
     * @param int $iat The time that the token was issued, defaults to `time()`
     * @param int $exp The expiration time
     *
     * @return string
     *
     * @throws \InvalidArgumentException For invalid $iat and $exp arguments
     */
    public function generate($iat = null, $exp = null);

    /**
     * Validate a CSRF token.
     *
     * @param string $token The token to validate
     * @param int    $now   The current time, defaults to `time()`
     *
     * @return bool true iff $token is valid
     */
    public function validate($token, $now = null);
}
