<?php

return [
    'default' => 'local',
    'disks' => [
    'local' => [
    'driver' => 'local',
    'root' => '/Users/deanhowe/PLANNR/VALET/VHOSTS/markdown.observer/storage/app/private',
    'serve' => true,
    'throw' => false,
    'report' => false
],
    'composer-packages' => [
    'driver' => 'local',
    'root' => '/Users/deanhowe/PLANNR/VALET/VHOSTS/markdown.observer/vendor',
    'url' => NULL,
    'visibility' => 'public',
    'throw' => false,
    'report' => false,
    'key' => NULL,
    'secret' => NULL,
    'region' => NULL,
    'bucket' => NULL,
    'endpoint' => NULL,
    'use_path_style_endpoint' => false,
    'host' => NULL,
    'username' => NULL,
    'password' => NULL,
    'port' => 21,
    'passive' => true,
    'ssl' => true,
    'timeout' => 30
],
    'public' => [
    'driver' => 'local',
    'root' => '/Users/deanhowe/PLANNR/VALET/VHOSTS/markdown.observer/storage/app/public',
    'url' => 'https://markdown.observer.test/storage',
    'visibility' => 'public',
    'throw' => false,
    'report' => false
],
    'pages' => [
    'driver' => 'local',
    'root' => storage_path('framework/testing/pages'),
    'url' => NULL,
    'visibility' => 'public',
    'throw' => false,
    'report' => false,
    'key' => NULL,
    'secret' => NULL,
    'region' => NULL,
    'bucket' => NULL,
    'endpoint' => NULL,
    'use_path_style_endpoint' => false,
    'host' => NULL,
    'username' => NULL,
    'password' => NULL,
    'port' => 21,
    'passive' => true,
    'ssl' => true,
    'timeout' => 30
],
    'test_pages' => [
    'driver' => 'local',
    'root' => '/Users/deanhowe/PLANNR/VALET/VHOSTS/markdown.observer/tests/Fixtures/pages',
    'throw' => false,
    'report' => false
],
    's3' => [
    'driver' => 's3',
    'key' => '',
    'secret' => '',
    'region' => 'us-east-1',
    'bucket' => '',
    'url' => NULL,
    'endpoint' => NULL,
    'use_path_style_endpoint' => false,
    'throw' => false,
    'report' => false
],
    'github' => [
    'driver' => 'local',
    'root' => '/Users/deanhowe/PLANNR/VALET/VHOSTS/markdown.observer/storage/app/github',
    'url' => NULL,
    'visibility' => 'public',
    'throw' => false,
    'report' => false,
    'token' => NULL,
    'repository' => NULL,
    'branch' => 'main'
]
],
    'links' => [
    '/Users/deanhowe/PLANNR/VALET/VHOSTS/markdown.observer/public/storage' => '/Users/deanhowe/PLANNR/VALET/VHOSTS/markdown.observer/storage/app/public'
]
];
