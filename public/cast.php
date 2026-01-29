<?php

require_once '../config/db.php';
require_once '../includes/session.php';
require_once '../includes/init.php';
requireAdmin();
require_once '../includes/header.php';

$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    echo '<div class="alert alert-error">No movie specified.</div>';
    echo '<a href="index.php" class="btn">Back to Movies</a>';
    require_once '../includes/footer.php';
    exit;
}


$movieStmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$movieStmt->execute([$movie_id]);
$movie = $movieStmt->fetch();

if (!$movie) {
    echo '<div class="alert alert-error">Movie not found.</div>';
    echo '<a href="index.php" class="btn">Back to Movies</a>';
    require_once '../includes/footer.php';
    exit;
}

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cast'])) {
    $actor_name = trim($_POST['actor_name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    
    if (empty($actor_name)) {
        $error = 'Please enter actor name.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO movie_cast (movie_id, actor_name, role) VALUES (?, ?, ?)");
            $stmt->execute([$movie_id, $actor_name, $role]);
            $success = "Cast member '$actor_name' added successfully!";
            $_POST['actor_name'] = '';
            $_POST['role'] = '';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['delete_cast'])) {
    $cast_id = $_GET['delete_cast'];
    $stmt = $pdo->prepare("DELETE FROM movie_cast WHERE id = ?");
    $stmt->execute([$cast_id]);
    $success = "Cast member deleted successfully!";
}

$castStmt = $pdo->prepare("SELECT * FROM movie_cast WHERE movie_id = ? ORDER BY actor_name");
$castStmt->execute([$movie_id]);
$cast = $castStmt->fetchAll();
?>

<h2>Cast Management</h2>
<h3>Movie: <?php echo htmlspecialchars($movie['title']); ?> (<?php echo htmlspecialchars($movie['year']); ?>)</h3>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="search-container">
    <h4>Add New Cast Member</h4>
    <form method="POST" class="cast-form">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="actor_name">Actor Name *</label>
                <input type="text" id="actor_name" name="actor_name" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['actor_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role/Character</label>
                <input type="text" id="role" name="role" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['role'] ?? ''); ?>" 
                       placeholder="Character name">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" name="add_cast" class="btn btn-success">Add Cast Member</button>
            <a href="view.php?id=<?php echo $movie_id; ?>" class="btn btn-secondary">Back to Movie</a>
        </div>
    </form>
</div>

<h4>Cast List</h4>
<?php if (count($cast) > 0): ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Actor</th>
                    <th>Role/Character</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cast as $index => $cast_member): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($cast_member['actor_name']); ?></td>
                        <td><?php echo htmlspecialchars($cast_member['role'] ?: 'N/A'); ?></td>
                        <td>
                            <a href="cast.php?movie_id=<?php echo $movie_id; ?>&delete_cast=<?php echo $cast_member['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this cast member?')">
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
        <p>No cast members found. Add one above.</p>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>