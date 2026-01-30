<?php
$host = 'localhost';
$dbname = 'movie_database';
$username = 'root';
$password = '';

// $host = 'localhost';
// $dbname = 'NP03CS4A240334';
// $username = 'NP03CS4A240334';
// $password = 'g00RqxkcrQ';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>