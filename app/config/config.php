<?php
return [
    'app_name' => 'Galeri Uygulamasi',
    'session_name' => 'gallery_session',
    'db' => [
        'driver' => getenv('DB_DRIVER') ?: 'pgsql',

        // PostgreSQL settings
        'host' => getenv('PG_HOST') ?: '127.0.0.1',
        'port' => getenv('PG_PORT') ?: '5432',
        'database' => getenv('PG_DATABASE') ?: 'galleryDb',
        'username' => getenv('PG_USER') ?: 'postgres',
        'password' => getenv('PG_PASSWORD') ?: '12345',
        'charset' => getenv('PG_CHARSET') ?: 'utf8',

        // Oracle fallback settings
        'oracle_username' => getenv('ORACLE_USER') ?: 'gallery_user',
        'oracle_password' => getenv('ORACLE_PASSWORD') ?: 'gallery_pass',
        'oracle_connection_string' => getenv('ORACLE_DSN') ?: 'localhost/XE',
        'oracle_charset' => getenv('ORACLE_CHARSET') ?: 'AL32UTF8',
    ],
];
