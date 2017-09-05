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
     * @param string $nonce Value used to associate a client session
     * @param int    $iat   The time that the token was issued, defaults to `time()`
     * @param int    $exp   The expiration time
     *
     * @return string
     *
     * @throws \InvalidArgumentException For invalid $iat and $exp arguments
     */
    public function generate($nonce, $iat = null, $exp = null);

    /**
     * Validate a CSRF token.
     *
     * @param string $nonce  Value used to associate a client session
     * @param string $token  The token to validate
     * @param int    $now    The current time, defaults to `time()`
     * @param int    $leeway The leeway in seconds
     *
     * @return bool true iff $token is valid
     */
    public function validate($nonce, $token, $now = null, $leeway = 0);
}
