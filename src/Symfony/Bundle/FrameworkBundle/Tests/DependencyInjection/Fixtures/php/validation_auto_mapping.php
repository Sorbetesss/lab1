<?php

$container->loadFromExtension('framework', [
    'annotations' => false,
    'http_method_override' => false,
    'property_info' => ['enabled' => true],
    'validation' => [
        'auto_mapping' => [
            'App\\' => ['foo', 'bar'],
            'Symfony\\' => ['a', 'b'],
            'Foo\\',
        ],
    ],
]);
