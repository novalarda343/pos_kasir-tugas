<?php

declare(strict_types=1);

$dbConfig = [
    'host' => 'sql311.infinityfree.com',
    'port' => '3306',
    'database' => 'if0_42198835_pos_kasir_tailadmin',
    'username' => 'if0_42198835',
    'password' => 'YBqJBp7L8Ca',
    'charset' => 'utf8mb4',
];

function db(): PDO
{
    static $pdo = null;
    global $dbConfig;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['database'],
        $dbConfig['charset']
    );

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
