<?php

use ksoftm\system\core\Env;

return [
    'connection' => Env::get('DB_CONNECTION', 'mysql'),
    'data' => [
        'mysql' => [
            'host' => Env::get('DB_HOST', 'localhost'),
            'port' => Env::get('DB_PORT', '3306'),
            'database' => Env::get('DB_DATABASE', 'mysql'),
            'username' => Env::get('DB_USERNAME', 'root'),
            'password' => Env::get('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'engine' => 'InnoDB',
        ]
    ]
];
