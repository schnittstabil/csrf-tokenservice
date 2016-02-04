<?php

namespace Schnittstabil\Csrf;

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
     * @param callable $sign Callable used for generating the token signatures.
     * @param int      $ttl  Default Time to Live in seconds used for calculating the expiration time of the tokens (1440sec === 24min === default of session.gc_maxlifetime).
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
     * @param int $iat The time that the token was issued, defaults to `time()`
     * @param int $exp The expiration time, defaults to `$iat + $this->ttl`
     *
     * @return string
     *
     * @throws \InvalidArgumentException For invalid $iat and $exp arguments.
     */
    public function __invoke($iat = null, $exp = null)
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

        $payload = [
            'iat' => $iat,
            'ttl' => $exp - $iat,
            'exp' => $exp,
        ];

        $payloadBase64 = $this->base64url->encode(json_encode($payload));
        $sign = $this->sign;

        return $payloadBase64.'.'.$sign($payloadBase64);
    }
}
