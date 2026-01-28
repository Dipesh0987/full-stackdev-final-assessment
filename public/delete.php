<?php
// File: public/delete.php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    // Get movie to delete its poster file
    $stmt = $pdo->prepare("SELECT poster FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $movie = $stmt->fetch();
    
    // Delete movie
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    
    // Delete poster file if not default
    if ($movie && $movie['poster'] !== 'no-image.png') {
        $poster_path = '../assets/uploads/' . $movie['poster'];
        if (file_exists($poster_path)) {
            unlink($poster_path);
        }
    }
    
    // Also delete cast entries
    $stmt = $pdo->prepare("DELETE FROM movie_cast WHERE movie_id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
?>