<?php
// db_connect.php
$config = require __DIR__ . '/config.php';

function getDb() {
    static $pdo = null;
    global $config;
    if ($pdo === null) {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            file_put_contents($config['error_log'], "[".date('Y-m-d H:i:s')."] DB connection error: ".$e->getMessage().PHP_EOL, FILE_APPEND);
            die("Connection failed. Check log.");
        }
    }
    return $pdo;
}
