<?php

$container->loadFromExtension('security', [
    'access_decision_manager' => [
        'service' => 'app.access_decision_manager',
        'strategy' => 'affirmative',
    ],
    'providers' => [
        'default' => [
            'memory' => [
                'users' => [
                    'foo' => ['password' => 'foo', 'roles' => 'ROLE_USER'],
                ],
            ],
        ],
    ],
    'firewalls' => [
        'simple' => ['path' => '/login', 'security' => false],
    ],
]);
