<?php
require_once '../config/db.php';
header('Content-Type: application/json');
$q = $_GET['q'] ?? '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT DISTINCT title FROM movies WHERE title LIKE ? ORDER BY title LIMIT 10");
    $stmt->execute(["%$q%"]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $results = [];
}

echo json_encode($results);
?>