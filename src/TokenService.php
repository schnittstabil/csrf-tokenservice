<?php

namespace Schnittstabil\Csrf\TokenService;

/**
 * A TokenService.
 */
class TokenService implements TokenServiceInterface
{
    protected $generator;
    protected $validator;

    /**
     * Create a new TokenService.
     *
     * `$ttl` is used for calculating the expiration time of the tokens, its default value (1440sec === 24min)
     * correspond to the default `session.gc_maxlifetime`.
     *
     * @see http://php.net/manual/en/session.configuration.php Documentation of `session.gc-maxlifetime`
     *
     * @param string $key  Shared secret key used for generating token signatures
     * @param int    $ttl  Default Time to Live in seconds
     * @param string $algo Name of hashing algorithm. See hash_algos() for a list of supported algorithms
     */
    public function __construct($key, $ttl = 1440, $algo = 'SHA512')
    {
        $signatory = new TokenSignatory($key, $algo);
        $this->generator = new TokenGenerator($signatory, $ttl);
        $this->validator = new TokenValidator($signatory);
    }

    /**
     * Generate a CSRF token.
     *
     * @param int $iat The time that the token was issued, defaults to `time()`
     * @param int $exp The expiration time, defaults to `$iat + $this->ttl`
     *
     * @return string
     *
     * @throws \InvalidArgumentException For invalid $iat and $exp arguments
     */
    public function generate($iat = null, $exp = null)
    {
        $generator = $this->generator;

        return $generator($iat, $exp);
    }

    /**
     * Determine constraint violations of CSRF tokens.
     *
     * @param string $token The token to validate
     * @param int    $now   The current time, defaults to `time()`
     *
     * @return InvalidArgumentException[] Constraint violations; if $token is valid, an empty array
     */
    public function getConstraintViolations($token, $now = null)
    {
        $validator = $this->validator;

        return $validator($token, $now);
    }

    /**
     * Validate a CSRF token.
     *
     * @param string $token The token to validate
     * @param int    $now   The current time, defaults to `time()`
     *
     * @return bool true iff $token is valid
     */
    public function validate($token, $now = null)
    {
        return count($this->getConstraintViolations($token, $now)) === 0;
    }
}
