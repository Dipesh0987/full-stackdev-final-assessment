<?php
// File: public/edit.php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Get movie
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    $error = "Movie not found.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $title = trim($_POST['title'] ?? '');
    $director = trim($_POST['director'] ?? '');
    $year = $_POST['year'] ?? '';
    $genre = trim($_POST['genre'] ?? '');
    $rating = $_POST['rating'] ?? '';
    $description = trim($_POST['description'] ?? '');
    
    // Keep current poster unless new one is uploaded
    $poster = $movie['poster'];
    
    // Handle new file upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['poster']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES['poster']['size'];
        $tempname = $_FILES['poster']['tmp_name'];
        
        if (in_array($ext, $allowed) && $filesize <= 5000000) {
            // Delete old image if not default
            if ($poster !== 'no-image.png') {
                $old_path = '../assets/uploads/' . $poster;
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            
            // Upload new image
            $poster = uniqid('movie_') . '.' . $ext;
            $upload_path = '../assets/uploads/' . $poster;
            
            if (!move_uploaded_file($tempname, $upload_path)) {
                $error = 'Failed to upload new image. Keeping old one.';
                $poster = $movie['poster'];
            }
        } else {
            $error = 'Invalid image file. Keeping current image.';
        }
    }
    
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE movies SET title=?, director=?, year=?, genre=?, rating=?, description=?, poster=? WHERE id=?");
            $stmt->execute([$title, $director, $year, $genre, $rating, $description, $poster, $id]);
            $success = 'Movie updated successfully!';
            $movie = $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?")->execute([$id])->fetch();
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

$genres = $pdo->query("SELECT name FROM genres ORDER BY name")->fetchAll();
?>

<h2>Edit Movie</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($movie): ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Director *</label>
            <input type="text" name="director" class="form-control" value="<?php echo htmlspecialchars($movie['director']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Year *</label>
            <input type="number" name="year" class="form-control" value="<?php echo htmlspecialchars($movie['year']); ?>" min="1900" max="<?php echo date('Y'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Genre *</label>
            <select name="genre" class="form-control" required>
                <option value="">Select Genre</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?php echo htmlspecialchars($g['name']); ?>" <?php echo $movie['genre'] == $g['name'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($g['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Rating</label>
            <input type="number" name="rating" class="form-control" step="0.1" value="<?php echo htmlspecialchars($movie['rating']); ?>" min="0" max="10">
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($movie['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Current Poster:</label>
            <?php
            $current_poster = $movie['poster'] ?? 'no-image.png';
            $poster_path = '../assets/uploads/' . $current_poster;
            if (!file_exists($poster_path)) {
                $poster_path = '../assets/uploads/no-image.png';
            }
            ?>
            <img src="<?php echo $poster_path; ?>" style="max-width: 200px; display: block; margin: 10px 0;">
            
            <label>Upload New Poster (Optional)</label>
            <input type="file" name="poster" class="form-control" accept="image/*">
            <small>Leave empty to keep current image</small>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Update Movie</button>
            <a href="view.php?id=<?php echo $id; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
<?php else: ?>
    <p>Movie not found. <a href="index.php">Back to list</a></p>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>