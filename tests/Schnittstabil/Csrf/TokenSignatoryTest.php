<?php

namespace Schnittstabil\Csrf;

use Base64Url\Base64Url;

/**
 * TokenSignatory Tests.
 */
class TokenSignatoryTest extends \PHPUnit_Framework_TestCase
{
    use \VladaHejda\AssertException;

    public function testEmptyKeysShouldThrowExceptions()
    {
        $this->assertException(function () {
            new TokenSignatory('');
        }, \InvalidArgumentException::class);
    }

    public function testShouldSignWithSha512()
    {
        $sut = new TokenSignatory('secret');
        $this->assertSame('sOllDF-vnNiuAidmcVRUJBBFibNlZzHsGTsl0BsHVhwnY3wtTWg4nWz1AHqGMsJuyJuoCgHHemzdOJ7CjbQ5AQ', $sut(''));
        $this->assertSame('GMTS7bfcAS1K3jh-WHq3xS9Qo4RSnz42g5KhsLFhg_QKYv6BTLoqBJ2bDnK3qskyAGovbXf6e3au3hvWPYiCQQ', $sut('abc'));
    }

    public function testShouldSignWithSha256()
    {
        $rfc7515Key = Base64Url::decode('AyM1SysPpbyDfgZld3umj1qzKObwVMkoqQ-EstJQLr_T-1qS0gZH75aKtMN3Yj0iPS4hcgUuTwjAzZr1Z9CAow');
        $rfc7515Token = 'eyJ0eXAiOiJKV1QiLA0KICJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ';
        $sut = new TokenSignatory($rfc7515Key, 'SHA256');
        $this->assertSame('dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk', $sut($rfc7515Token));
    }
}
