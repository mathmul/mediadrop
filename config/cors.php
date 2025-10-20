<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'https://mediadrop.test',
    ],
    'allowed_origins_patterns' => [
        '/^https?:\/\/([a-z0-9-]+\.)?mediadrop\.test$/',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
