<?php

$container->loadFromExtension('framework', [
    'csrf_protection' => [
        'storage' => 'session',
    ],
]);
