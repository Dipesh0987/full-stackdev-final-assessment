<?php
require_once '../config/db.php';
require_once '../includes/session.php';
requireAdmin();

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $pdo->prepare("SELECT poster FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $movie = $stmt->fetch();
    
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($movie && $movie['poster'] !== 'no-image.png') {
        $poster_path = '../assets/uploads/' . $movie['poster'];
        if (file_exists($poster_path)) {
            unlink($poster_path);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM movie_cast WHERE movie_id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
?>