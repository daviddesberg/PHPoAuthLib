<?php
use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in( __DIR__ . '/src/' )
;

return new Sami($iterator, array(
    'theme'                => 'enhanced',
    'title'                => 'PHPoAuthLib API',
    'build_dir'            => __DIR__.'/doc/api',
    'cache_dir'            => '/tmp/cache',
    'default_opened_level' => 2,
));
