<?php
/**
 * Konfigurace DB. Pro produkci zkopíruj config.example.php a přepiš hodnoty,
 * nebo nastav proměnné prostředí MYSQL_HOST, MYSQL_DBNAME, MYSQL_USER, MYSQL_PASSWORD.
 */
$host     = getenv('MYSQL_HOST')     ?: 'localhost';
$dbname   = getenv('MYSQL_DBNAME')  ?: 'logik_mastermind';
$user     = getenv('MYSQL_USER')     ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';

return [
    'db' => [
        'host'     => $host,
        'dbname'   => $dbname,
        'user'     => $user,
        'password' => $password,
        'charset'  => 'utf8mb4',
    ],
];
