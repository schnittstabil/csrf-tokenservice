<?php

namespace Schnittstabil\Psr7\Csrf;

require __DIR__.'/../vendor/autoload.php';

/*
 * PHPUnit 5/6
 */
if (!class_exists(\PHPUnit\Framework\TestCase::class)) {
    class_alias(\PHPUnit_Framework_TestCase::class, \PHPUnit\Framework\TestCase::class);
}
