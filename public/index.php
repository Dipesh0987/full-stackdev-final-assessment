<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/header.php';

// Get all movies
try {
    // Check if movies table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'movies'")->fetch();
    if ($tableExists) {
        $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC");
        $movies = $stmt->fetchAll();
    } else {
        $movies = [];
    }
} catch (PDOException $e) {
    $error = "Error fetching movies: " . $e->getMessage();
}
?>

<h2>All Movies</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (isset($movies) && count($movies) > 0): ?>
    <div class="grid">
        <?php foreach ($movies as $movie): ?>
            <div class="card">
                <?php 
                // Handle poster image - LOCAL FILES ONLY
                $poster = $movie['poster'] ?? 'no-image.png';
                $posterPath = '../assets/uploads/' . htmlspecialchars($poster);

                // Check if file exists
                $fullPath = __DIR__ . '/../assets/uploads/' . $poster;
                if (!file_exists($fullPath) || $poster == 'no-image.png') {
                    $posterPath = '../assets/uploads/no-image.png';
                }
                ?>
                <img src="<?php echo $posterPath; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <div class="card-content">
                    <h3 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                    <p class="card-meta"><?php echo htmlspecialchars($movie['genre']); ?> | <?php echo htmlspecialchars($movie['year']); ?></p>
                    <p class="card-rating">Rating: <?php echo htmlspecialchars($movie['rating'] ?: 'N/A'); ?>/10</p>
                    
                    <div class="actions">
                        <a href="view.php?id=<?php echo $movie['id']; ?>">View</a>
                        
                        <?php if (isAdmin()): ?>
                            <a href="edit.php?id=<?php echo $movie['id']; ?>">Edit</a>
                            <a href="cast.php?movie_id=<?php echo $movie['id']; ?>">Cast</a>
                            <a href="delete.php?id=<?php echo $movie['id']; ?>" 
                               onclick="return confirm('Are you sure?')">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-results">
        <p>No movies found. 
        <?php if (isAdmin()): ?>
            <a href="add.php">Add your first movie</a>
        <?php else: ?>
            Please check back later.
        <?php endif; ?>
        </p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
