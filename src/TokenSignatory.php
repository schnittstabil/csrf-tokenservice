<?php

namespace Schnittstabil\Csrf\TokenService;

use Base64Url\Base64Url;

/**
 * A TokenSignatory.
 */
class TokenSignatory
{
    protected $key;
    protected $algo;
    protected $base64url;

    /**
     * Create a new TokenSignatory.
     *
     * @param string $key  Shared secret key used for generating token signatures
     * @param string $algo Name of hashing algorithm. See hash_algos() for a list of supported algorithms
     */
    public function __construct($key, $algo = 'SHA512')
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key may not be empty');
        }

        $this->key = $key;
        $this->algo = $algo;
        $this->base64url = new Base64Url();
    }

    /**
     * Sign a Base64Url encoded token payload.
     *
     * @param string $tokenPayload The Base64Url encoded token payload
     *
     * @return string Base64Url encoded signature
     */
    public function __invoke($tokenPayload)
    {
        return $this->base64url->encode(hash_hmac($this->algo, $tokenPayload, $this->key, true));
    }
}
