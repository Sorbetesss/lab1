<?php

$container->loadFromExtension('framework', [
    'messenger' => [
        'transports' => [
            'foo' => [
                'dsn' => 'null://',
                'symfony_serializer' => [
                    'format' => 'xml',
                ]
            ],
        ],
    ],
]);
