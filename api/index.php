<?php

$host = $_ENV['POSTGRES_HOST'];
$port = $_ENV['POSTGRES_PORT'];
$db = $_ENV['POSTGRES_DATABASE'];
$user = $_ENV['POSTGRES_USER'];
$password = $_ENV['POSTGRES_PASSWORD'];

$connection_string = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $db = new PDO($connection_string, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "PostgreSQLに接続成功";
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
}
?>
