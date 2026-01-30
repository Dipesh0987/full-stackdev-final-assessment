<?php
// made connection to database, session functions from session.php, initialize system and check if user is admin
require_once '../config/db.php';
require_once '../includes/session.php';
require_once '../includes/init.php'; 
require_once '../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_genre'])) {
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $error = 'Please enter a genre name.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO genres (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = "Genre '$name' added successfully!";
            $_POST['name'] = '';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Genre '$name' already exists.";
            } else {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM movies WHERE genre = (SELECT name FROM genres WHERE id = ?)");
    $checkStmt->execute([$id]);
    $result = $checkStmt->fetch();
    
    if ($result['count'] > 0) {
        $error = 'Cannot delete genre because it is being used by movies.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM genres WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Genre deleted successfully!";
    }
}

$genres = $pdo->query("
    SELECT g.*, COUNT(m.id) as movie_count 
    FROM genres g 
    LEFT JOIN movies m ON g.name = m.genre 
    GROUP BY g.id 
    ORDER BY g.name
")->fetchAll();
?>

<h2>Manage Genres</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="search-container">
    <h3>Add New Genre</h3>
    <form method="POST" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div style="flex: 1;">
            <input type="text" name="name" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                   placeholder="Enter genre name" required>
        </div>
        <div>
            <button type="submit" name="add_genre" class="btn btn-success">Add Genre</button>
        </div>
    </form>
</div>

<h3>Existing Genres</h3>
<?php if (count($genres) > 0): ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Movies</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($genres as $genre): ?>
                    <tr>
                        <td><?php echo $genre['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($genre['name']); ?></strong>
                        </td>
                        <td>
                            <span class="badge" style="background-color: #3498db; color: white; padding: 2px 8px; border-radius: 10px;">
                                <?php echo $genre['movie_count']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="genres.php?delete=<?php echo $genre['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this genre?\n\nNote: Only empty genres can be deleted.')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="no-results">
        <p>No genres found. Add your first genre above.</p>
    </div>
<?php endif; ?>

<div style="margin-top: 2rem;">
    <a href="index.php" class="btn btn-secondary">Back to Movies</a>
</div>

<?php require_once '../includes/footer.php'; ?>