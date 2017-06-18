<?php

namespace Schnittstabil\Csrf\TokenService;

use Base64Url\Base64Url;

/**
 * A TokenValidator.
 */
class TokenValidator
{
    protected $sign;
    protected $base64url;

    /**
     * Create a new TokenValidator.
     *
     * @param callable $sign Callable used for generating the token signatures
     */
    public function __construct(callable $sign)
    {
        $this->sign = $sign;
        $this->base64url = new Base64Url();
    }

    /**
     * Determine constraint violations of a CSRF token.
     *
     * @param string $nonce Value used to associate a client session
     * @param string $token The token to validate
     * @param int    $now   The current time, defaults to `time()`
     *
     * @return InvalidArgumentException[] Constraint violations; if $token is valid, an empty array
     */
    public function __invoke($nonce, $token, $now = null)
    {
        $parseResult = $this->parse($token);

        if ($violations = $parseResult->violations) {
            return $violations;
        }

        return $this->validatePayload($nonce, $parseResult->payload, $now);
    }

    /**
     * Parse a CSRF token.
     *
     * @param string $token The token to parse
     *
     * @return \stdClass Parse result containing payload and constraint violations
     */
    protected function parse($token)
    {
        $result = new \stdClass();
        $result->violations = [];
        $result->payload = null;
        $segments = explode('.', $token);

        if (count($segments) !== 2) {
            $result->violations[] = new \InvalidArgumentException('Wrong number of segments');

            return $result;
        }

        list($payloadBase64, $signature) = $segments;

        $sign = $this->sign;

        if ($signature !== $sign($payloadBase64)) {
            $result->violations[] = new \InvalidArgumentException('Signature verification failed');
        }

        $result->payload = json_decode($this->base64url->decode($payloadBase64));

        if (!($result->payload instanceof \stdClass)) {
            $result->violations[] = new \InvalidArgumentException('Invalid payload encoding');
        }

        return $result;
    }

    /**
     * Validate the payload of a CSRF token.
     *
     * @param string    $nonce   Value used to associate a client session
     * @param \stdClass $payload The token payload to validate
     * @param int       $now     The current time, defaults to `time()`
     *
     * @return InvalidArgumentException[] Constraint violations; if $payload is valid, an empty array
     */
    protected function validatePayload($nonce, \stdClass $payload, $now = null)
    {
        $now = $now ?: time();
        $violations = [];

        if ($payload->exp < $now) {
            $exp = date(\DateTime::ISO8601, $payload->exp);
            $violations[] = new \InvalidArgumentException('Token already expired at '.$exp);
        }

        if ($now < $payload->iat) {
            $issuedAt = date(\DateTime::ISO8601, $payload->iat);
            $violations[] = new \InvalidArgumentException('Cannot handle token prior to '.$issuedAt);
        }

        if ($payload->nonce !== $nonce) {
            $violations[] = new \InvalidArgumentException('Nonce mismatch');
        }

        return $violations;
    }
}
