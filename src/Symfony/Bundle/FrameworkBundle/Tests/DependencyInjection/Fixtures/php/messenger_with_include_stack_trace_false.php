<?php

$container->loadFromExtension('framework', [
    'annotations' => false,
    'http_method_override' => false,
    'handle_all_throwables' => true,
    'php_errors' => ['log' => true],
    'messenger' => [
        'include_stack_trace_in_error' => false,
        'transports' => [
            'sender.biz' => 'null://',
            'sender.bar' => [
                'dsn' => 'null://',
                'include_stack_trace_in_error' => true,
            ],
            'sender.foo' => 'null://',
        ],
    ],
]);