<?php
return [
    'mysql'=>[
        'driver' => 'mysql',
        'host' => getenv('DBHOST'),
        'database' => getenv('DATABASE'),
        'username' => getenv('DBUSER'),
        'password' => getenv('DBPASSWORD'),
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    ]
];
