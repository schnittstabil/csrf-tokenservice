<?php

namespace Schnittstabil\Csrf\TokenService;

use Base64Url\Base64Url;

/**
 * TokenValidator Tests.
 */
class TokenValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $signatory;

    protected function setUp()
    {
        $this->signatory = new TokenSignatory('secret');
        $this->generator = new TokenGenerator($this->signatory);
    }

    public function testEmptyTokensShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $this->assertValidationsContain($sut('666', ''), function ($validation) {
            return strpos($validation->getMessage(), 'Wrong number of segments') !== false;
        });
    }

    public function testEmptySignituresShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $token = Base64Url::encode(json_encode(new \stdClass())).'.';
        $this->assertValidationsContain($sut('666', $token), function ($validation) {
            return strpos($validation->getMessage(), 'Signature verification') !== false;
        });
    }

    public function testNonObjectPayloadsShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $payload = Base64Url::encode(json_encode([]));
        $sign = $this->signatory;
        $token = $payload.'.'.$sign($payload);
        $this->assertValidationsContain($sut('666', $token), function ($validation) {
            return strpos($validation->getMessage(), 'payload encoding') !== false;
        });
    }

    public function testNonceMismatchShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $generate = $this->generator;
        $token = $generate('666');

        $this->assertValidationsContain($sut('777', $token), function ($validation) {
            return strpos($validation->getMessage(), 'Nonce mismatch') !== false;
        });
    }

    public function testExpiredTokensShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $generate = $this->generator;
        $token = $generate('666', 1, 2);

        $this->assertValidationsContain($sut('666', $token), function ($validation) {
            return strpos($validation->getMessage(), 'already expired') !== false;
        });
    }

    public function testPreemieTokensShouldReturnViolations()
    {
        $sut = new TokenValidator($this->signatory);
        $generate = $this->generator;
        $token = $generate('666', time() + 24 * 60 * 60);

        $this->assertValidationsContain($sut('666', $token), function ($validation) {
            return strpos($validation->getMessage(), 'token prior to') !== false;
        });
    }

    protected function assertValidationsContain($validations, callable $callback)
    {
        foreach ($validations as $validation) {
            if ($callback($validation)) {
                return;
            }
        }
        $this->assertArraySubset(array_map('strval', $validations), [], '', 'Assertion not found');
    }
}
