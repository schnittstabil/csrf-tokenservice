<?php

namespace Schnittstabil\Csrf;

/**
 * TokenService Tests.
 */
class TokenServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testValidTokensShouldReturnNoViolations()
    {
        $sut = new TokenService('secret');
        $token = $sut->generate(1);

        $this->assertNotEmpty($token);
        $this->assertContains('.', $token);
        $this->assertSame([], $sut->getConstraintViolations($token, 2));
        $this->assertTrue($sut->validate($token, 2));
    }
}
