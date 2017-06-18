<?php

namespace Schnittstabil\Csrf\TokenService;

use InvalidArgumentException;
use Base64Url\Base64Url;

/**
 * TokenGenerator Tests.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class TokenGeneratorTest extends \PHPUnit\Framework\TestCase
{
    protected $base64url;
    protected $signatory;

    protected function setUp()
    {
        $this->base64url = new Base64Url();
        $this->signatory = new TokenSignatory('secret');
    }

    public function testNonIntTtlsShouldThrowExceptions()
    {
        $this->expectException(InvalidArgumentException::class);
        new TokenGenerator($this->signatory, '');
    }

    public function testZeroIntTtlsShouldThrowExceptions()
    {
        $this->expectException(InvalidArgumentException::class);
        new TokenGenerator($this->signatory, 0);
    }

    public function testNegativeIntTtlsShouldThrowExceptions()
    {
        $this->expectException(InvalidArgumentException::class);
        new TokenGenerator($this->signatory, -1);
    }

    public function testNonStringNounceShouldThrowExceptions()
    {
        $sut = new TokenGenerator($this->signatory);
        $this->expectException(InvalidArgumentException::class);
        $sut(666);
    }

    public function testNonIntIatShouldThrowExceptions()
    {
        $sut = new TokenGenerator($this->signatory);
        $this->expectException(InvalidArgumentException::class);
        $sut('666', 'today');
    }

    public function testNonIntExpShouldThrowExceptions()
    {
        $sut = new TokenGenerator($this->signatory);
        $this->expectException(InvalidArgumentException::class);
        $sut('666', time(), 'today');
    }

    public function testNonExpBeforeIntExpShouldThrowExceptions()
    {
        $sut = new TokenGenerator($this->signatory);
        $now = time();
        $this->expectException(InvalidArgumentException::class);
        $sut('666', $now + 1, $now);
    }

    public function testIatShouldBeValid()
    {
        $sut = new TokenGenerator($this->signatory);
        $token = $sut('666', 42);
        $payload = $this->extractPayload($token);

        $this->assertSame(42, $payload->iat);
    }

    public function testExpShouldBeValid()
    {
        $sut = new TokenGenerator($this->signatory);
        $token = $sut('666', 1, 42);
        $payload = $this->extractPayload($token);

        $this->assertSame(42, $payload->exp);
    }

    public function testTtlShouldBeValid()
    {
        $sut = new TokenGenerator($this->signatory);
        $token = $sut('666');
        $payload = $this->extractPayload($token);

        $this->assertGreaterThanOrEqual(0, $payload->ttl);
        $this->assertSame($payload->exp - $payload->iat, $payload->ttl);
    }

    protected function extractPayload($token)
    {
        return json_decode($this->base64url->decode(explode('.', $token)[0]));
    }
}
