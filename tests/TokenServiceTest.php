<?php

namespace Schnittstabil\Csrf\TokenService;

/**
 * TokenService Tests.
 */
class TokenServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testValidTokensShouldReturnNoViolations()
    {
        $sut = new TokenService('secret');
        $token = $sut->generate('666', 1);

        $this->assertNotEmpty($token);
        $this->assertContains('.', $token);
        $this->assertSame([], $sut->getConstraintViolations('666', $token, 2));
        $this->assertTrue($sut->validate('666', $token, 2));
    }
}
