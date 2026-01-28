<?php
// File: public/view.php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $movie = $stmt->fetch();
    
    $castStmt = $pdo->prepare("SELECT * FROM movie_cast WHERE movie_id = ? ORDER BY actor_name");
    $castStmt->execute([$id]);
    $cast = $castStmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php elseif ($movie): ?>
    <div class="movie-details">
        <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
        
        <?php
        // LOCAL FILE HANDLING ONLY
        $poster = $movie['poster'] ?? 'no-image.png';
        $posterPath = '../assets/uploads/' . $poster;
        if (!file_exists($posterPath)) {
            $posterPath = '../assets/uploads/no-image.png';
        }
        ?>
        <img src="<?php echo $posterPath; ?>" class="detail-img" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        
        <div class="detail-info">
            <p><strong>Director:</strong> <?php echo htmlspecialchars($movie['director']); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($movie['year']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
            <p><strong>Rating:</strong> <?php echo htmlspecialchars($movie['rating'] ?: 'N/A'); ?>/10</p>
        </div>
        
        <div class="detail-description">
            <h3>Description</h3>
            <p><?php echo nl2br(htmlspecialchars($movie['description'] ?: 'No description available.')); ?></p>
        </div>
        
        <?php if (count($cast) > 0): ?>
            <div class="cast-section">
                <h3>Cast</h3>
                <table class="table">
                    <tr><th>Actor</th><th>Role</th></tr>
                    <?php foreach ($cast as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['actor_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['role'] ?: 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="edit.php?id=<?php echo $id; ?>" class="btn">Edit</a>
            <a href="cast.php?movie_id=<?php echo $id; ?>" class="btn">Manage Cast</a>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-error">Movie not found.</div>
    <a href="index.php" class="btn">Back to movies</a>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>