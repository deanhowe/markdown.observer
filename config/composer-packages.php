<?php
return [
    'cache' => [
        'prefix' => 'composer_packages',
        'ttl' => 3600,
    ],
    'analysis' => [
        'rate_limit' => [
            'attempts' => 1,
            'decay_minutes' => 1,
        ],
        'excluded_packages' => [
            'php',
        ],
    ],
    'storage' => [
        'disk' => 'local',
        'path' => 'composer-packages',
    ],
];
