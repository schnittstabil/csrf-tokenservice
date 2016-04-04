<?php

namespace Schnittstabil\Csrf\TokenService;

use Base64Url\Base64Url;
use VladaHejda\AssertException;

/**
 * TokenGenerator Tests.
 */
class TokenGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use AssertException;

    protected $signatory;

    protected function setUp()
    {
        $this->signatory = new TokenSignatory('secret');
    }

    public function testNonIntTtlsShouldThrowExceptions()
    {
        $this->assertException(function () {
            new TokenGenerator($this->signatory, '');
        }, \InvalidArgumentException::class);
    }

    public function testNonPositiveIntTtlsShouldThrowExceptions()
    {
        $this->assertException(function () {
            new TokenGenerator($this->signatory, 0);
        }, \InvalidArgumentException::class);

        $this->assertException(function () {
            new TokenGenerator($this->signatory, -1);
        }, \InvalidArgumentException::class);
    }

    public function testNonIntIatShouldThrowExceptions()
    {
        $this->assertException(function () {
            $sut = new TokenGenerator($this->signatory);
            $sut('today');
        }, \InvalidArgumentException::class);
    }

    public function testNonIntExpShouldThrowExceptions()
    {
        $this->assertException(function () {
            $sut = new TokenGenerator($this->signatory);
            $sut(time(), 'today');
        }, \InvalidArgumentException::class);
    }

    public function testNonExpBeforeIntExpShouldThrowExceptions()
    {
        $this->assertException(function () {
            $sut = new TokenGenerator($this->signatory);
            $now = time();
            $sut($now + 1, $now);
        }, \InvalidArgumentException::class);
    }

    public function testIatShouldBeValid()
    {
        $sut = new TokenGenerator($this->signatory);
        $token = $sut(42);
        $payload = self::extractPayload($token);

        $this->assertSame(42, $payload->iat);
    }

    public function testExpShouldBeValid()
    {
        $sut = new TokenGenerator($this->signatory);
        $token = $sut(1, 42);
        $payload = self::extractPayload($token);

        $this->assertSame(42, $payload->exp);
    }

    public function testTtlShouldBeValid()
    {
        $sut = new TokenGenerator($this->signatory);
        $token = $sut();
        $payload = self::extractPayload($token);

        $this->assertGreaterThanOrEqual(0, $payload->ttl);
        $this->assertSame($payload->exp - $payload->iat, $payload->ttl);
    }

    protected static function extractPayload($token)
    {
        return json_decode(Base64Url::decode(explode('.', $token)[0]));
    }
}
