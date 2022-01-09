<?php

$container->loadFromExtension('framework', [
    'messenger' => [
        'serializer' => [
            'default_serializer' =>  'messenger.transport.symfony_serializer',
            'symfony_serializer' => [
                'format' => 'json',
                'context' => ['some_context' => true]
            ]
        ],
        'transports' => [
            'foo' => [
                'dsn' => 'null://',
                'symfony_serializer' => [
                    'format' => 'xml',
                    'context' => ['some_other_context' => true]
                ]
            ],
        ],
    ],
]);
