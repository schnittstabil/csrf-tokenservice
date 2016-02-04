<?php

require 'vendor/autoload.php';

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in('Schnittstabil');

return new Sami(
    $iterator,
    array(
        'title' => 'Schnittstabil\Csrf\TokenService API',
        'build_dir' => __DIR__.'/doc',
        'cache_dir' => __DIR__.'/build/cache',
        'default_opened_level' => 2,
    )
);
