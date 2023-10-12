<?php

$container->loadFromExtension('framework', [
    'annotations' => false,
    'http_method_override' => false,
    'handle_all_throwables' => true,
    'php_errors' => ['log' => true],
    'feature_flags' => [
        'strategies' => [
            [
                'name' => 'date.feature-strategy',
                'type' => 'date',
                'with' => ['since' => '-2 days'],
            ],
            [
                'name' => 'env.feature-strategy',
                'type' => 'env',
                'with' => ['name' => 'SOME_ENV'],
            ],
            [
                'name' => 'request_header.feature-strategy',
                'type' => 'request_header',
                'with' => ['name' => 'SOME-HEADER-NAME'],
            ],
            [
                'name' => 'request_query.feature-strategy',
                'type' => 'request_query',
                'with' => ['name' => 'some_query_parameter'],
            ],
            [
                'name' => 'request_attribute.feature-strategy',
                'type' => 'request_attribute',
                'with' => ['name' => 'some_request_attribute'],
            ],
            [
                'name' => 'priority.feature-strategy',
                'type' => 'priority',
                'with' => ['strategies' => ['env.feature-strategy', 'grant.feature-strategy']],
            ],
            [
                'name' => 'affirmative.feature-strategy',
                'type' => 'affirmative',
                'with' => ['strategies' => ['env.feature-strategy', 'grant.feature-strategy']],
            ],
            [
                'name' => 'unanimous.feature-strategy',
                'type' => 'unanimous',
                'with' => ['strategies' => ['env.feature-strategy', 'grant.feature-strategy']],
            ],
            [
                'name' => 'not.feature-strategy',
                'type' => 'not',
                'with' => ['strategy' => 'grant.feature-strategy'],
            ],
            [
                'name' => 'grant.feature-strategy',
                'type' => 'grant',
            ],
            [
                'name' => 'deny.feature-strategy',
                'type' => 'deny',
            ],
        ],
        'features' => [
        ],
    ],
]);
