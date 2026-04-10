<?php

$config = require __DIR__ . '/config/config.php';

spl_autoload_register(function (string $class): void {
    $dirs = [
        __DIR__ . '/core/',
        __DIR__ . '/dto/',
        __DIR__ . '/controllers/',
        __DIR__ . '/services/',
        __DIR__ . '/repositories/',
        __DIR__ . '/middlewares/',
        __DIR__ . '/config/',
    ];

    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session_name']);
    session_start();
}

date_default_timezone_set('Europe/Istanbul');
