<?php
$db = [
    'params' => [
        'driver' => 'pdo_pgsql',
        'host' => 'db',
        'port' => 5432,
        'dbname' => 'mydatabase',
        'user' => 'myuser',
        'password' => 'mypassword',
    ],
    'entities' => [
        //The namespace must fit the point of the directory
        'directory' => dirname(__DIR__) . '/app/Database/Entities',
        'namespace' => 'App\\Database\\Entities',
    ]
];

if (file_exists(__DIR__ . '/environment.php')) {
    $env = include(__DIR__ . '/environment.php');

    if (isset($env['DB_HOST'])) {
        $db['params']['host'] = $env['DB_HOST'];
    }

    if (isset($env['DB_PORT'])) {
        $db['params']['port'] = $env['DB_PORT'];
    }

    if (isset($env['DB_DATABASE'])) {
        $db['params']['dbname'] = $env['DB_DATABASE'];
    }

    if (isset($env['DB_USER'])) {
        $db['params']['user'] = $env['DB_USER'];
    }

    if (isset($env['DB_PASSWORD'])) {
        $db['params']['password'] = $env['DB_PASSWORD'];
    }
}

return $db;