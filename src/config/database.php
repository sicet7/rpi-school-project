<?php
return [
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