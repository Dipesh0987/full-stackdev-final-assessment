<?php
// File: public/ajax_search.php
require_once '../config/db.php';

// Set JSON header
header('Content-Type: application/json');

// Get search query
$q = $_GET['q'] ?? '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Search for movie titles
$stmt = $pdo->prepare("SELECT DISTINCT title FROM movies WHERE title LIKE ? ORDER BY title LIMIT 10");
$stmt->execute(["%$q%"]);

$results = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($results);
?>