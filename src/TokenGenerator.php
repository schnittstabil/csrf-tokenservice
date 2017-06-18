<?php

namespace Schnittstabil\Csrf\TokenService;

use Base64Url\Base64Url;

/**
 * A TokenGenerator.
 */
class TokenGenerator
{
    protected $sign;
    protected $ttl;
    protected $base64url;

    /**
     * Create a new TokenGenerator.
     *
     * `$ttl` is used for calculating the expiration time of the tokens, its default value (1440sec === 24min)
     * correspond to the default `session.gc_maxlifetime`.
     *
     * @see http://php.net/manual/en/session.configuration.php Documentation of `session.gc-maxlifetime`
     *
     * @param callable $sign Callable used for generating the token signatures
     * @param int      $ttl  Default Time to Live in seconds
     */
    public function __construct(callable $sign, $ttl = 1440)
    {
        if (!is_int($ttl)) {
            throw new \InvalidArgumentException('ttl is not an integer');
        }

        if ($ttl <= 0) {
            throw new \InvalidArgumentException('ttl is non-positive');
        }

        $this->sign = $sign;
        $this->ttl = $ttl;
        $this->base64url = new Base64Url();
    }

    /**
     * Generate a CSRF token.
     *
     * @param string $nonce Value used to associate a client session
     * @param int    $iat   The time that the token was issued, defaults to `time()`
     * @param int    $exp   The expiration time, defaults to `$iat + $this->ttl`
     *
     * @return string
     *
     * @throws \InvalidArgumentException For invalid $iat, $exp and $nonce arguments
     */
    public function __invoke($nonce, $iat = null, $exp = null)
    {
        if ($iat === null) {
            $iat = time();
        }

        if (!is_int($iat)) {
            throw new \InvalidArgumentException('iat is not an integer');
        }

        if ($exp === null) {
            $exp = $iat + $this->ttl;
        }

        if (!is_int($exp)) {
            throw new \InvalidArgumentException('exp is not an integer');
        }

        if ($exp < $iat) {
            throw new \InvalidArgumentException('exp before iat');
        }

        if (!is_string($nonce)) {
            throw new \InvalidArgumentException('nonce is not an string');
        }

        $payload = new \stdClass();
        $payload->nonce = $nonce;
        $payload->iat = $iat;
        $payload->ttl = $exp - $iat;
        $payload->exp = $exp;

        $payloadBase64 = $this->base64url->encode(json_encode($payload));
        $sign = $this->sign;

        return $payloadBase64.'.'.$sign($payloadBase64);
    }
}
