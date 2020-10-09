<?php

$container->loadFromExtension('framework', [
    'http_client' => [
        'default_options' => [
            'retry_failed' => [
                'backoff_service' => null,
                'decider_service' => null,
                'http_codes' => [429, 500],
                'max_retries' => 2,
                'retry_timeout' => 10,
                'delay' => 100,
                'multiplier' => 2,
                'max_delay' => 0,
                'jitter' => 0.3,
            ]
        ],
        'scoped_clients' => [
            'foo' => [
                'base_uri' => 'http://example.com',
                'retry_failed' => ['multiplier' => 4],
            ],
        ],
    ],
]);
