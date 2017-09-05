<?php

namespace Schnittstabil\Csrf\TokenService;

use Base64Url\Base64Url;

/**
 * TokenValidator Tests.
 */
class TokenValidatorTest extends \PHPUnit\Framework\TestCase
{
    protected $base64url;
    protected $signatory;
    protected $generator;

    protected function setUp()
    {
        $this->base64url = new Base64Url();
        $this->signatory = new TokenSignatory('secret');
        $this->generator = new TokenGenerator($this->signatory);
    }

    public function testEmptyTokensShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $this->assertValidationsContain($sut('666', ''), function ($validation) {
            return strpos($validation->getMessage(), 'Wrong number of segments');
        });
    }

    public function testEmptySignituresShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $token = $this->base64url->encode(json_encode(new \stdClass())).'.';
        $this->assertValidationsContain($sut('666', $token), function ($validation) {
            return strpos($validation->getMessage(), 'Signature verification');
        });
    }

    public function testNonObjectPayloadsShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $payload = $this->base64url->encode(json_encode([]));
        $sign = $this->signatory;
        $token = $payload.'.'.$sign($payload);
        $this->assertValidationsContain($sut('666', $token), function ($validation) {
            return strpos($validation->getMessage(), 'payload encoding');
        });
    }

    public function testNonceMismatchShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $generate = $this->generator;
        $token = $generate('666');

        $this->assertValidationsContain($sut('777', $token), function ($validation) {
            return strpos($validation->getMessage(), 'Nonce mismatch');
        });
    }

    public function testExpiredTokensShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $generate = $this->generator;
        $token = $generate('666', 1, 2);

        $this->assertValidationsContain($sut('666', $token, 2), function ($validation) {
            return strpos($validation->getMessage(), 'already expired');
        });
    }

    public function testPreemieTokensShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $generate = $this->generator;
        $token = $generate('666', time() + 24 * 60 * 60);

        $this->assertValidationsContain($sut('666', $token), function ($validation) {
            return strpos($validation->getMessage(), 'token prior to');
        });
    }

    protected function assertValidationsContain($validations, callable $callback)
    {
        $this->assertNotCount(0, $validations, 'Assertion not found');

        foreach ($validations as $validation) {
            if ($callback($validation) !== false) {
                // phpunit risky tests workaround
                $this->assertTrue(true);

                return;
            }
        }
        $this->assertArraySubset(array_map('strval', $validations), [], '', 'Assertion not found');
    }
}
